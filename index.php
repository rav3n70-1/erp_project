<?php
$page_title = "Dashboard";
include('includes/header.php');
include('includes/db.php');

$conn = connect_db();

// --- Fetch data for all dynamic cards ---
$sql_pending_pos = "SELECT COUNT(id) as pending_count FROM purchase_orders WHERE status = 'Pending'";
$pending_pos_count = $conn->query($sql_pending_pos)->fetch_assoc()['pending_count'];

$sql_suppliers = "SELECT COUNT(id) as supplier_count FROM suppliers";
$suppliers_count = $conn->query($sql_suppliers)->fetch_assoc()['supplier_count'];

$sql_projects = "SELECT COUNT(id) as project_count FROM projects WHERE status = 'In Progress'";
$projects_count = $conn->query($sql_projects)->fetch_assoc()['project_count'];

$sql_spend = "SELECT SUM(total_amount) as month_spend FROM purchase_orders WHERE status IN ('Approved', 'Partially Delivered', 'Completed') AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
$month_spend = $conn->query($sql_spend)->fetch_assoc()['month_spend'] ?? 0;

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
?>

<div class="d-flex justify-content-between align-items-center">
    <h1 class="mt-4">Dashboard</h1>
</div>
<p class="lead mb-4">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here is a summary of system activity.</p>

<div class="row">
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-warning text-dark h-100">
            <div class="card-body"><div><div class="fs-1 fw-bold"><?php echo $pending_pos_count; ?></div><div class="text-uppercase">POs Awaiting Approval</div></div><i class="bi bi-patch-question-fill stat-icon"></i></div>
            <a class="card-footer text-dark d-flex" href="/erp_project/modules/purchase_orders/view_pos.php?filter_status=Pending">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-primary text-white h-100">
             <div class="card-body"><div><div class="fs-1 fw-bold"><?php echo $suppliers_count; ?></div><div class="text-uppercase">Total Suppliers</div></div><i class="bi bi-people-fill stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/suppliers/view_suppliers.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-info text-white h-100">
             <div class="card-body"><div><div class="fs-1 fw-bold"><?php echo $projects_count; ?></div><div class="text-uppercase">In-Progress Projects</div></div><i class="bi bi-kanban-fill stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/projects/view_projects.php">View Details <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="card dashboard-card bg-success text-white h-100">
             <div class="card-body"><div><div class="fs-1 fw-bold">$<?php echo number_format($month_spend, 2); ?></div><div class="text-uppercase">Spend This Month</div></div><i class="bi bi-currency-dollar stat-icon"></i></div>
            <a class="card-footer text-white d-flex" href="/erp_project/modules/reports/purchase_history.php">View Reports <i class="bi bi-arrow-right-short ms-auto"></i></a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-lightning-charge-fill me-1"></i>Quick Actions</div>
    <div class="card-body">
        <div class="row text-center">
            <?php if (has_permission('po_create')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/purchase_orders/create_po.php" class="text-decoration-none text-dark">
                    <i class="bi bi-receipt-cutoff display-6"></i>
                    <p class="mt-1 mb-0">New PO</p>
                </a>
            </div>
            <?php endif; ?>
             <?php if (has_permission('hr_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/hr/add_employee.php" class="text-decoration-none text-dark">
                    <i class="bi bi-person-plus-fill display-6"></i>
                    <p class="mt-1 mb-0">Add Employee</p>
                </a>
            </div>
            <?php endif; ?>
            <?php if (has_permission('invoice_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/finance/log_invoice.php" class="text-decoration-none text-dark">
                    <i class="bi bi-journal-plus display-6"></i>
                    <p class="mt-1 mb-0">Log Invoice</p>
                </a>
            </div>
            <?php endif; ?>
            <?php if (has_permission('project_create')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/projects/add_project.php" class="text-decoration-none text-dark">
                    <i class="bi bi-folder-plus display-6"></i>
                    <p class="mt-1 mb-0">New Project</p>
                </a>
            </div>
            <?php endif; ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/reports/purchase_history.php" class="text-decoration-none text-dark">
                    <i class="bi bi-file-earmark-bar-graph-fill display-6"></i>
                    <p class="mt-1 mb-0">View Reports</p>
                </a>
            </div>
             <?php if (has_permission('user_manage')): ?>
            <div class="col-lg-2 col-md-4 col-6 mb-3">
                <a href="/erp_project/modules/admin/manage_users.php" class="text-decoration-none text-dark">
                    <i class="bi bi-people-fill display-6"></i>
                    <p class="mt-1 mb-0">Manage Users</p>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-header"><i class="bi bi-bar-chart-line-fill me-1"></i>Spend Analysis by Supplier</div>
            <div class="card-body"><canvas id="spendBySupplierChart" width="100%" height="30"></canvas></div>
        </div>
    </div>
</div>

<?php
$conn->close();
include('includes/footer.php');
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart.js script is unchanged
const ctx = document.getElementById('spendBySupplierChart');
new Chart(ctx, { type: 'bar', data: { labels: <?php echo $chart_labels_json; ?>, datasets: [{ label: 'Total Spend ($)', data: <?php echo $chart_data_json; ?>, backgroundColor: 'rgba(0, 123, 255, 0.7)', borderColor: 'rgba(0, 123, 255, 1)', borderWidth: 1 }] }, options: { scales: { y: { beginAtZero: true, ticks: { callback: function(value) { return '$' + value.toLocaleString(); } } } } } });
</script>