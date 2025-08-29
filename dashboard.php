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

<!-- Modern Dashboard Header -->
<div class="modern-dashboard-header fade-in-up">
    <h1 class="modern-dashboard-title">Dashboard</h1>
    <p class="modern-dashboard-subtitle">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here's your system overview.</p>
</div>

<!-- Main KPI Cards with Modern Design -->
<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-patch-question-fill"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo $pending_pos_count; ?>">0</div>
                    <div class="text-uppercase small opacity-75">POs Awaiting Approval</div>
                    <div class="status-indicator status-warning mt-2">
                        <div class="status-dot"></div>
                        <span>Pending</span>
                    </div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/purchase_orders/view_pos.php?filter_status=Pending">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo $suppliers_count; ?>">0</div>
                    <div class="text-uppercase small opacity-75">Total Suppliers</div>
                    <div class="status-indicator status-info mt-2">
                        <div class="status-dot"></div>
                        <span>Active</span>
                    </div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/suppliers/view_suppliers.php">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-kanban-fill"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo $projects_count; ?>">0</div>
                    <div class="text-uppercase small opacity-75">In-Progress Projects</div>
                    <div class="status-indicator status-success mt-2">
                        <div class="status-dot"></div>
                        <span>Running</span>
                    </div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/projects/view_projects.php">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo (float)$month_spend; ?>" data-currency>0</div>
                    <div class="text-uppercase small opacity-75">Spend This Month</div>
                    <div class="status-indicator status-success mt-2">
                        <div class="status-dot"></div>
                        <span>Budget</span>
                    </div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/reports/purchase_history.php">
                View Reports <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
</div>

<!-- Financial Overview Cards -->
<?php if (has_permission('finance_view')): ?>
<div class="row mb-5">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-bank"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo (float)$total_cash; ?>" data-currency>0</div>
                    <div class="text-uppercase small opacity-75">Total Cash & Bank</div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/accounts/bank_accounts.php">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-receipt"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo (float)$ar_outstanding; ?>" data-currency>0</div>
                    <div class="text-uppercase small opacity-75">Outstanding Receivables</div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/accounts/accounts_receivable.php">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-receipt-cutoff"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo (float)$ap_outstanding; ?>" data-currency>0</div>
                    <div class="text-uppercase small opacity-75">Outstanding Payables</div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/accounts/accounts_payable.php">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="modern-dashboard-card h-100 floating-element reveal">
            <div class="card-body d-flex align-items-center justify-content-between">
                <div>
                    <div class="modern-icon-container">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
                <div class="text-end">
                    <div class="fs-1 fw-bold modern-counter" data-count-up="<?php echo (float)$monthly_revenue; ?>" data-currency>0</div>
                    <div class="text-uppercase small opacity-75">Revenue This Month</div>
                </div>
            </div>
            <a class="card-footer text-white d-flex align-items-center" href="/erp_project/modules/accounts/accounts_receivable.php">
                View Details <i class="bi bi-arrow-right-short ms-auto"></i>
            </a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Modern Quick Actions -->
<div class="modern-chart-container reveal">
    <div class="d-flex align-items-center mb-3">
        <i class="bi bi-lightning-charge-fill me-2"></i>
        <h5 class="mb-0 text-white">Quick Actions</h5>
    </div>
    <div class="modern-quick-actions">
        <?php if (has_permission('po_create')): ?>
        <a href="/erp_project/modules/purchase_orders/create_po.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-receipt-cutoff modern-action-icon"></i>
            <p class="mb-0 text-white">New PO</p>
        </a>
        <?php endif; ?>
        
        <?php if (has_permission('hr_manage')): ?>
        <a href="/erp_project/modules/hr/add_employee.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-person-plus-fill modern-action-icon"></i>
            <p class="mb-0 text-white">Add Employee</p>
        </a>
        <?php endif; ?>
        
        <?php if (has_permission('invoice_manage')): ?>
        <a href="/erp_project/modules/finance/log_invoice.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-journal-plus modern-action-icon"></i>
            <p class="mb-0 text-white">Log Invoice</p>
        </a>
        <?php endif; ?>
        
        <?php if (has_permission('budget_manage')): ?>
        <a href="/erp_project/modules/accounts/add_account.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-plus-circle-fill modern-action-icon"></i>
            <p class="mb-0 text-white">Add Account</p>
        </a>
        <?php endif; ?>
        
        <?php if (has_permission('project_create')): ?>
        <a href="/erp_project/modules/projects/add_project.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-folder-plus modern-action-icon"></i>
            <p class="mb-0 text-white">New Project</p>
        </a>
        <?php endif; ?>
        
        <a href="/erp_project/modules/reports/purchase_history.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-file-earmark-bar-graph-fill modern-action-icon"></i>
            <p class="mb-0 text-white">View Reports</p>
        </a>
        
        <?php if (has_permission('user_manage')): ?>
        <a href="/erp_project/modules/admin/manage_users.php" class="modern-action-item text-decoration-none">
            <i class="bi bi-people-fill modern-action-icon"></i>
            <p class="mb-0 text-white">Manage Users</p>
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Charts Section with Modern Design -->
<div class="row">
    <div class="col-lg-8">
        <div class="modern-chart-container reveal">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-bar-chart-line-fill me-2 text-white"></i>
                <h5 class="mb-0 text-white">Spend Analysis by Supplier</h5>
            </div>
            <canvas id="spendBySupplierChart" width="100%" height="30"></canvas>
        </div>
    </div>
    <div class="col-lg-4">
        <?php if (has_permission('finance_view')): ?>
        <div class="modern-chart-container reveal">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-pie-chart-fill me-2 text-white"></i>
                <h5 class="mb-0 text-white">Account Types Distribution</h5>
            </div>
            <canvas id="accountTypesChart" width="100%" height="60"></canvas>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if (has_permission('finance_view')): ?>
<div class="row">
    <div class="col-lg-12">
        <div class="modern-chart-container reveal">
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-graph-up me-2 text-white"></i>
                <h5 class="mb-0 text-white">Accounts Receivable vs Payable Trends (Last 6 Months)</h5>
            </div>
            <canvas id="arApTrendsChart" width="100%" height="25"></canvas>
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
// Modern Chart Configuration
Chart.defaults.color = '#ffffff';
Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.1)';

// Spend by Supplier Chart
const spendCtx = document.getElementById('spendBySupplierChart');
new Chart(spendCtx, { 
    type: 'bar', 
    data: { 
        labels: <?php echo $chart_labels_json; ?>, 
        datasets: [{ 
            label: 'Total Spend (৳)', 
            data: <?php echo $chart_data_json; ?>, 
            backgroundColor: 'rgba(102, 126, 234, 0.8)', 
            borderColor: 'rgba(102, 126, 234, 1)', 
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }] 
    }, 
    options: { 
        responsive: true,
        plugins: {
            legend: {
                labels: {
                    color: '#ffffff'
                }
            }
        },
        scales: { 
            y: { 
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: { 
                    color: '#ffffff',
                    callback: function(value) { 
                        return '৳' + value.toLocaleString(); 
                    } 
                } 
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#ffffff'
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
            backgroundColor: [
                'rgba(102, 126, 234, 0.8)',
                'rgba(118, 75, 162, 0.8)',
                'rgba(255, 107, 107, 0.8)',
                'rgba(79, 172, 254, 0.8)',
                'rgba(238, 90, 36, 0.8)'
            ],
            borderWidth: 3,
            borderColor: 'rgba(255, 255, 255, 0.2)'
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#ffffff',
                    padding: 20
                }
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
            borderColor: 'rgba(16, 185, 129, 1)',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: 'rgba(16, 185, 129, 1)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6
        }, {
            label: 'Accounts Payable (৳)',
            data: <?php echo $ap_data_json; ?>,
            borderColor: 'rgba(239, 68, 68, 1)',
            backgroundColor: 'rgba(239, 68, 68, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3,
            pointBackgroundColor: 'rgba(239, 68, 68, 1)',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6
        }]
    },
    options: {
        responsive: true,
        interaction: {
            intersect: false,
            mode: 'index'
        },
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#ffffff',
                    callback: function(value) {
                        return '৳' + value.toLocaleString();
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(255, 255, 255, 0.1)'
                },
                ticks: {
                    color: '#ffffff'
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
                labels: {
                    color: '#ffffff',
                    usePointStyle: true,
                    padding: 20
                }
            }
        }
    }
});
<?php endif; ?>

// Initialize all modern dashboard enhancements
document.addEventListener('DOMContentLoaded', function() {
    // Show welcome toast after loading
    setTimeout(() => {
        if (window.erpToast) {
            erpToast('Welcome', 'success');
        }
    }, 2000);
    
    // Initialize modern enhancements functions from script.js
    if (typeof initParticles === 'function') initParticles();
    if (typeof initScrollReveal === 'function') initScrollReveal();
    if (typeof initModernCounters === 'function') initModernCounters();
    if (typeof initFloatingActionButton === 'function') initFloatingActionButton();
    if (typeof initModernCardEffects === 'function') initModernCardEffects();
    if (typeof initProgressRings === 'function') initProgressRings();
    if (typeof initPageTransitions === 'function') initPageTransitions();
    if (typeof initMouseParallax === 'function') initMouseParallax();
    if (typeof initModernNotifications === 'function') initModernNotifications();
    
    // Initialize staggered animations with delay
    setTimeout(() => {
        if (typeof initStaggeredAnimations === 'function') {
            initStaggeredAnimations();
        }
    }, 500);
    
    // Add reveal class to elements that should animate on scroll
    document.querySelectorAll('.modern-dashboard-card, .modern-chart-container').forEach(el => {
        el.classList.add('reveal');
    });
});
</script>