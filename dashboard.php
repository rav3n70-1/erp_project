<?php
$page_title = "Dashboard";
include('includes/header.php');
include('includes/db.php');
include('includes/accounts_schema.php');

$conn = connect_db();
ensure_accounts_schema($conn);

// --- Fetch data for all dynamic cards ---
$sql_pending_pos = "SELECT COUNT(id) as pending_count FROM purchase_orders WHERE status = 'Pending'";
$pending_pos_count = $conn->query($sql_pending_pos)->fetch_assoc()['pending_count'];

$sql_suppliers = "SELECT COUNT(id) as supplier_count FROM suppliers";
$suppliers_count = $conn->query($sql_suppliers)->fetch_assoc()['supplier_count'];

$sql_projects = "SELECT COUNT(id) as project_count FROM projects WHERE status = 'In Progress'";
$projects_count = $conn->query($sql_projects)->fetch_assoc()['project_count'];

$sql_spend = "SELECT SUM(total_amount) as month_spend FROM purchase_orders WHERE status IN ('Approved', 'Partially Delivered', 'Completed') AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
$month_spend = $conn->query($sql_spend)->fetch_assoc()['month_spend'] ?? 0;

// --- Fetch accounts-related data ---
// Total Cash & Bank Balances
$sql_cash_balance = "SELECT SUM(balance) as total_cash FROM bank_accounts";
$total_cash = $conn->query($sql_cash_balance)->fetch_assoc()['total_cash'] ?? 0;

// Outstanding Accounts Receivable
$sql_ar_outstanding = "SELECT SUM(total - paid) as ar_outstanding FROM ar_invoices WHERE status != 'Paid'";
$ar_outstanding = $conn->query($sql_ar_outstanding)->fetch_assoc()['ar_outstanding'] ?? 0;

// Outstanding Accounts Payable
$sql_ap_outstanding = "SELECT SUM(total - paid) as ap_outstanding FROM ap_bills WHERE status != 'Paid'";
$ap_outstanding = $conn->query($sql_ap_outstanding)->fetch_assoc()['ap_outstanding'] ?? 0;

// Monthly Revenue (from AR invoices)
$sql_monthly_revenue = "SELECT SUM(total) as monthly_revenue FROM ar_invoices WHERE MONTH(invoice_date) = MONTH(CURDATE()) AND YEAR(invoice_date) = YEAR(CURDATE())";
$monthly_revenue = $conn->query($sql_monthly_revenue)->fetch_assoc()['monthly_revenue'] ?? 0;

// --- Fetch data for the Spend Analysis Chart ---
$sql_chart = "SELECT s.supplier_name, SUM(po.total_amount) as total_spent FROM purchase_orders po JOIN suppliers s ON po.supplier_id = s.id WHERE po.status IN ('Approved', 'Partially Delivered', 'Completed') GROUP BY s.supplier_name ORDER BY total_spent DESC LIMIT 7";
$chart_result = $conn->query($sql_chart);
$chart_labels = [];
$chart_data = [];
while ($row = $chart_result->fetch_assoc()) {
    $chart_labels[] = $row['supplier_name'];
    $chart_data[] = $row['total_spent'];
}
$chart_labels_json = json_encode($chart_labels);
$chart_data_json = json_encode($chart_data);

// --- Fetch data for Accounts Receivable vs Payable Chart ---
$from_date = date('Y-m-01', strtotime('-5 months'));
$sql_ar_ap_monthly = "
    SELECT 
        'AR' as type, 
        MONTH(invoice_date) as month, 
        YEAR(invoice_date) as year,
        SUM(total) as amount 
    FROM ar_invoices 
    WHERE invoice_date >= '$from_date'
    GROUP BY YEAR(invoice_date), MONTH(invoice_date)
    
    UNION ALL
    
    SELECT 
        'AP' as type,
        MONTH(bill_date) as month,
        YEAR(bill_date) as year,
        SUM(total) as amount
    FROM ap_bills
    WHERE bill_date >= '$from_date'
    GROUP BY YEAR(bill_date), MONTH(bill_date)
";

$ar_ap_result = $conn->query($sql_ar_ap_monthly);
$temp_data = [];
while ($row = $ar_ap_result->fetch_assoc()) {
    $month_key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
    $temp_data[$month_key][$row['type']] = (float)$row['amount'];
}

$month_labels = [];
$ar_data = [];
$ap_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month_key = date('Y-m', strtotime("-$i months"));
    $month_labels[] = date('M Y', strtotime($month_key . '-01'));
    $ar_data[] = $temp_data[$month_key]['AR'] ?? 0.0;
    $ap_data[] = $temp_data[$month_key]['AP'] ?? 0.0;
}

$ar_ap_labels_json = json_encode($month_labels);
$ar_data_json = json_encode($ar_data);
$ap_data_json = json_encode($ap_data);

// --- Fetch data for Account Types Distribution ---
$sql_account_types = "
    SELECT 
        account_type,
        COUNT(*) as count,
        SUM(opening_balance) as total_balance
    FROM chart_of_accounts 
    WHERE is_active = 1
    GROUP BY account_type";

$account_types_result = $conn->query($sql_account_types);
$account_type_labels = [];
$account_type_counts = [];
$account_type_colors = [
    'Asset' => '#28a745',
    'Liability' => '#dc3545', 
    'Equity' => '#6f42c1',
    'Revenue' => '#007bff',
    'Expense' => '#fd7e14'
];

while ($row = $account_types_result->fetch_assoc()) {
    $account_type_labels[] = $row['account_type'];
    $account_type_counts[] = $row['count'];
}

$account_type_labels_json = json_encode($account_type_labels);
$account_type_counts_json = json_encode($account_type_counts);
$account_type_colors_json = json_encode(array_values(array_intersect_key($account_type_colors, array_flip($account_type_labels))));
?>

<div class="d-flex justify-content-between align-items-center">
    <h1 class="mt-4 text-gradient">Dashboard</h1>
</div>
<p class="lead mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here is a summary of system activity.</p>

<!-- Main KPI Cards -->
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-warning text-dark h-100">
            <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo $pending_pos_count; ?>">0</div><div class="text-uppercase">POs Awaiting Approval</div></div><i class="bi bi-patch-question-fill stat-icon"></i></div>
            <a class="card-footer text-dark d-flex" href="/erp_project/modules/purchase_orders/view_pos.php?filter_status=Pending">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-primary text-white h-100">
             <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo $suppliers_count; ?>">0</div><div class="text-uppercase">Total Suppliers</div></div><i class="bi bi-people-fill stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/suppliers/view_suppliers.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-info text-white h-100">
             <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo $projects_count; ?>">0</div><div class="text-uppercase">In-Progress Projects</div></div><i class="bi bi-kanban-fill stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/projects/view_projects.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-success text-white h-100">
             <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo (float)$month_spend; ?>" data-currency>0</div><div class="text-uppercase">Spend This Month</div></div><i class="bi bi-cash-coin stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/reports/purchase_history.php">View Reports <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
</div>

<!-- Financial Overview Cards -->
<?php if (has_permission('finance_view')): ?>
<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-dark text-white h-100">
            <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo (float)$total_cash; ?>" data-currency>0</div><div class="text-uppercase">Total Cash & Bank</div></div><i class="bi bi-bank stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/accounts/bank_accounts.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-gradient-blue text-white h-100">
            <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo (float)$ar_outstanding; ?>" data-currency>0</div><div class="text-uppercase">Outstanding Receivables</div></div><i class="bi bi-receipt stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/accounts/accounts_receivable.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-gradient-orange text-white h-100">
            <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo (float)$ap_outstanding; ?>" data-currency>0</div><div class="text-uppercase">Outstanding Payables</div></div><i class="bi bi-receipt-cutoff stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/accounts/accounts_payable.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-gradient-green text-white h-100">
            <div class="card-body"><div><div class="fs-1 fw-bold" data-count-up="<?php echo (float)$monthly_revenue; ?>" data-currency>0</div><div class="text-uppercase">Revenue This Month</div></div><i class="bi bi-graph-up-arrow stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/accounts/accounts_receivable.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-lightning-charge-fill me-1"></i>Quick Actions</div>
    <div class="card-body">
        <div class="row text-center">
            <?php if (has_permission('po_create')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/purchase_orders/create_po.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-receipt-cutoff display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">New PO</p>
                </a>
            </div>
            <?php endif; ?>
             <?php if (has_permission('hr_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/hr/add_employee.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-person-plus-fill display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">Add Employee</p>
                </a>
            </div>
            <?php endif; ?>
            <?php if (has_permission('invoice_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/finance/log_invoice.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-journal-plus display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">Log Invoice</p>
                </a>
            </div>
            <?php endif; ?>
            <?php if (has_permission('budget_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/accounts/add_account.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-plus-circle-fill display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">Add Account</p>
                </a>
            </div>
            <?php endif; ?>
            <?php if (has_permission('project_create')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/projects/add_project.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-folder-plus display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">New Project</p>
                </a>
            </div>
            <?php endif; ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/reports/purchase_history.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-file-earmark-bar-graph-fill display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">View Reports</p>
                </a>
            </div>
             <?php if (has_permission('user_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/admin/manage_users.php" class="text-decoration-none quick-actions-button">
                    <i class="bi bi-people-fill display-6 quick-actions-icon"></i>
                    <p class="mt-1 mb-0">Manage Users</p>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-bar-chart-line-fill me-1"></i>Spend Analysis by Supplier</div>
            <div class="card-body"><canvas id="spendBySupplierChart" width="100%" height="30"></canvas></div>
        </div>
    </div>
    <div class="col-lg-4">
        <?php if (has_permission('finance_view')): ?>
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-pie-chart-fill me-1"></i>Account Types Distribution</div>
            <div class="card-body"><canvas id="accountTypesChart" width="100%" height="60"></canvas></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (has_permission('finance_view')): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-graph-up me-1"></i>Accounts Receivable vs Payable Trends (Last 6 Months)</div>
            <div class="card-body"><canvas id="arApTrendsChart" width="100%" height="25"></canvas></div>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$conn->close();
include('includes/footer.php');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Spend by Supplier Chart
const spendCtx = document.getElementById('spendBySupplierChart');
new Chart(spendCtx, { 
    type: 'bar', 
    data: { 
        labels: <?php echo $chart_labels_json; ?>, 
        datasets: [{ 
            label: 'Total Spend (৳)', 
            data: <?php echo $chart_data_json; ?>, 
            backgroundColor: 'rgba(0, 123, 255, 0.7)', 
            borderColor: 'rgba(0, 123, 255, 1)', 
            borderWidth: 1 
        }] 
    }, 
    options: { 
        responsive: true,
        scales: { 
            y: { 
                beginAtZero: true, 
                ticks: { 
                    callback: function(value) { 
                        return '৳' + value.toLocaleString(); 
                    } 
                } 
            } 
        } 
    } 
});

<?php if (has_permission('finance_view')): ?>
// Account Types Distribution Chart
const accountTypesCtx = document.getElementById('accountTypesChart');
new Chart(accountTypesCtx, {
    type: 'doughnut',
    data: {
        labels: <?php echo $account_type_labels_json; ?>,
        datasets: [{
            data: <?php echo $account_type_counts_json; ?>,
            backgroundColor: <?php echo $account_type_colors_json; ?>,
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// AR vs AP Trends Chart
const arApCtx = document.getElementById('arApTrendsChart');
new Chart(arApCtx, {
    type: 'line',
    data: {
        labels: <?php echo $ar_ap_labels_json; ?>,
        datasets: [{
            label: 'Accounts Receivable (৳)',
            data: <?php echo $ar_data_json; ?>,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40, 167, 69, 0.1)',
            tension: 0.4,
            fill: true
        }, {
            label: 'Accounts Payable (৳)',
            data: <?php echo $ap_data_json; ?>,
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220, 53, 69, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});
<?php endif; ?>
</script>