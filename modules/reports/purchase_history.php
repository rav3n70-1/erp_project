<?php
$page_title = "Purchase History Report";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

$sql_suppliers = "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
$suppliers_result = $conn->query($sql_suppliers);

$sql = "SELECT 
            po.order_date, po.po_number, s.supplier_name, p.product_name,
            p.sku, poi.quantity, poi.unit_price, poi.total_price
        FROM po_items poi
        JOIN purchase_orders po ON poi.po_id = po.id
        JOIN products p ON poi.product_id = p.id
        JOIN suppliers s ON po.supplier_id = s.id
        WHERE po.status IN ('Approved', 'Partially Delivered', 'Completed')";
$params = [];
$types = '';

if (!empty($_GET['start_date'])) {
    $sql .= " AND po.order_date >= ?";
    $types .= 's';
    $params[] = $_GET['start_date'];
}
if (!empty($_GET['end_date'])) {
    $sql .= " AND po.order_date <= ?";
    $types .= 's';
    $params[] = $_GET['end_date'];
}
if (!empty($_GET['supplier_id'])) {
    $sql .= " AND po.supplier_id = ?";
    $types .= 'i';
    $params[] = $_GET['supplier_id'];
}

$sql .= " ORDER BY po.order_date DESC";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Reports</li>
    <li class="breadcrumb-item active" aria-current="page">Purchase History</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-filter"></i> Filter Report</div>
    <div class="card-body">
        <form action="purchase_history.php" method="GET">
            <div class="row">
                <div class="col-md-3"><label for="start_date" class="form-label">Start Date</label><input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>"></div>
                <div class="col-md-3"><label for="end_date" class="form-label">End Date</label><input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>"></div>
                <div class="col-md-4"><label for="supplier_id" class="form-label">Supplier</label><select class="form-select" name="supplier_id" id="supplier_id"><option value="">All Suppliers</option><?php while($s = $suppliers_result->fetch_assoc()): ?><option value="<?php echo $s['id']; ?>" <?php if(isset($_GET['supplier_id']) && $_GET['supplier_id'] == $s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['supplier_name']); ?></option><?php endwhile; ?></select></div>
                <div class="col-md-2 d-flex align-items-end"><button type="submit" class="btn btn-primary w-100">Apply</button></div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-table"></i> Report Results</span>
        <a href="export_purchase_history.php?<?php echo http_build_query($_GET); ?>" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-spreadsheet-fill"></i> Export to CSV</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Order Date</th><th>PO Number</th><th>Supplier</th><th>SKU</th><th>Product</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total Price</th></tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo ($row['order_date'] && $row['order_date'] != '0000-00-00') ? date("d M, Y", strtotime($row['order_date'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($row['po_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['sku']); ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td class="text-end"><?php echo $row['quantity']; ?></td>
                                <td class="text-end">$<?php echo number_format($row['unit_price'], 2); ?></td>
                                <td class="text-end">$<?php echo number_format($row['total_price'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">No records found for the selected filters.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>