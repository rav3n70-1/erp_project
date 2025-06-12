<?php
$page_title = "Dashboard";
include('includes/header.php');
include('includes/db.php');

$conn = connect_db();

// --- Fetch data for all 5 dynamic cards ---

// 1. POs Awaiting Approval
$sql_pending_pos = "SELECT COUNT(id) as pending_count FROM purchase_orders WHERE status = 'Pending'";
$pending_pos_count = $conn->query($sql_pending_pos)->fetch_assoc()['pending_count'];

// 2. Total Suppliers
$sql_suppliers = "SELECT COUNT(id) as supplier_count FROM suppliers";
$suppliers_count = $conn->query($sql_suppliers)->fetch_assoc()['supplier_count'];

// 3. Total Products (Restored)
$sql_products = "SELECT COUNT(id) as product_count FROM products";
$products_count = $conn->query($sql_products)->fetch_assoc()['product_count'];

// 4. Deliveries This Month
$sql_deliveries = "SELECT COUNT(id) as delivery_count FROM deliveries WHERE MONTH(delivery_date) = MONTH(CURDATE()) AND YEAR(delivery_date) = YEAR(CURDATE())";
$deliveries_count = $conn->query($sql_deliveries)->fetch_assoc()['delivery_count'];

// 5. Spend This Month
$sql_spend = "SELECT SUM(total_amount) as month_spend FROM purchase_orders WHERE status IN ('Approved', 'Partially Delivered', 'Completed') AND MONTH(order_date) = MONTH(CURDATE()) AND YEAR(order_date) = YEAR(CURDATE())";
$month_spend = $conn->query($sql_spend)->fetch_assoc()['month_spend'] ?? 0;


// --- Fetch data for the Spend Analysis Chart ---
$sql_chart = "SELECT s.supplier_name, SUM(po.total_amount) as total_spent
              FROM purchase_orders po
              JOIN suppliers s ON po.supplier_id = s.id
              WHERE po.status IN ('Approved', 'Partially Delivered', 'Completed')
              GROUP BY s.supplier_name
              ORDER BY total_spent DESC
              LIMIT 7"; 
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

<h1 class="mt-4">Dashboard</h1>
<p class="lead">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here is a summary of the system activity.</p>

<div class="row">
    <div class="col-lg col-md-6 mb-4">
        <div class="card bg-warning text-dark h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between"><div><div class="fs-1 fw-bold"><?php echo $pending_pos_count; ?></div><div>POs Awaiting Approval</div></div><i class="bi bi-patch-question-fill fs-1 text-black-50"></i></div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between"><a class="small text-dark stretched-link" href="/erp_project/modules/purchase_orders/view_pos.php">View Details</a><div class="small text-dark"><i class="bi bi-chevron-right"></i></div></div>
        </div>
    </div>
    <div class="col-lg col-md-6 mb-4">
        <div class="card bg-primary text-white h-100">
             <div class="card-body">
                <div class="d-flex justify-content-between"><div><div class="fs-1 fw-bold"><?php echo $suppliers_count; ?></div><div>Total Suppliers</div></div><i class="bi bi-people-fill fs-1 text-white-50"></i></div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between"><a class="small text-white stretched-link" href="/erp_project/modules/suppliers/view_suppliers.php">View Details</a><div class="small text-white"><i class="bi bi-chevron-right"></i></div></div>
        </div>
    </div>
    <div class="col-lg col-md-6 mb-4">
        <div class="card bg-info text-white h-100">
             <div class="card-body">
                <div class="d-flex justify-content-between"><div><div class="fs-1 fw-bold"><?php echo $products_count; ?></div><div>Total Products</div></div><i class="bi bi-box-seam fs-1 text-white-50"></i></div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between"><a class="small text-white stretched-link" href="/erp_project/modules/products/view_products.php">View Details</a><div class="small text-white"><i class="bi bi-chevron-right"></i></div></div>
        </div>
    </div>
    <div class="col-lg col-md-6 mb-4">
        <div class="card bg-secondary text-white h-100">
             <div class="card-body">
                <div class="d-flex justify-content-between"><div><div class="fs-1 fw-bold"><?php echo $deliveries_count; ?></div><div>Deliveries This Month</div></div><i class="bi bi-truck fs-1 text-white-50"></i></div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between"><a class="small text-white stretched-link" href="/erp_project/modules/deliveries/view_deliveries.php">View Details</a><div class="small text-white"><i class="bi bi-chevron-right"></i></div></div>
        </div>
    </div>
    <div class="col-lg col-md-6 mb-4">
        <div class="card bg-success text-white h-100">
             <div class="card-body">
                <div class="d-flex justify-content-between"><div><div class="fs-1 fw-bold">$<?php echo number_format($month_spend, 2); ?></div><div>Spend This Month</div></div><i class="bi bi-currency-dollar fs-1 text-white-50"></i></div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between"><a class="small text-white stretched-link" href="/erp_project/modules/reports/purchase_history.php">View Reports</a><div class="small text-white"><i class="bi bi-chevron-right"></i></div></div>
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