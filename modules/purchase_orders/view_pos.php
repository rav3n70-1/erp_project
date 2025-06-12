<?php
$page_title = "Manage Purchase Orders";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

// --- Fetch data for filters ---
$sql_suppliers = "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
$suppliers_result = $conn->query($sql_suppliers);
// This array was missing from the partial code, it's needed for the status filter dropdown.
$po_statuses = ['Draft', 'Pending', 'Approved', 'Rejected', 'Partially Delivered', 'Completed', 'Canceled'];

// --- Build main query ---
$sql = "SELECT po.*, s.supplier_name 
        FROM purchase_orders po
        JOIN suppliers s ON po.supplier_id = s.id
        WHERE 1=1"; 

$params = [];
$types = '';

// Append filters
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
if (!empty($_GET['filter_status'])) {
    $sql .= " AND po.status = ?";
    $types .= 's';
    $params[] = $_GET['filter_status'];
}

$sql .= " ORDER BY FIELD(po.status, 'Draft', 'Pending', 'Approved', 'Partially Delivered', 'Completed', 'Rejected', 'Canceled'), po.order_date DESC";
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
    <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if (has_permission('po_create')): ?>
        <a href="create_po.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Create New PO</a>
    <?php endif; ?>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-filter"></i> Filter Purchase Orders</div>
    <div class="card-body">
        <form action="view_pos.php" method="GET" class="row g-3">
            <div class="col-md-3"><label for="start_date" class="form-label">From Date</label><input type="date" class="form-control" name="start_date" id="start_date" value="<?php echo isset($_GET['start_date']) ? htmlspecialchars($_GET['start_date']) : ''; ?>"></div>
            <div class="col-md-3"><label for="end_date" class="form-label">To Date</label><input type="date" class="form-control" name="end_date" id="end_date" value="<?php echo isset($_GET['end_date']) ? htmlspecialchars($_GET['end_date']) : ''; ?>"></div>
            <div class="col-md-3"><label for="supplier_id" class="form-label">Supplier</label><select class="form-select" name="supplier_id" id="supplier_id"><option value="">All Suppliers</option><?php mysqli_data_seek($suppliers_result, 0); while($s = $suppliers_result->fetch_assoc()): ?><option value="<?php echo $s['id']; ?>" <?php if(isset($_GET['supplier_id']) && $_GET['supplier_id'] == $s['id']) echo 'selected'; ?>><?php echo htmlspecialchars($s['supplier_name']); ?></option><?php endwhile; ?></select></div>
            <div class="col-md-3"><label for="filter_status" class="form-label">Status</label><select class="form-select" name="filter_status" id="filter_status"><option value="">All Statuses</option><?php foreach($po_statuses as $status): ?><option value="<?php echo $status; ?>" <?php if(isset($_GET['filter_status']) && $_GET['filter_status'] == $status) echo 'selected'; ?>><?php echo $status; ?></option><?php endforeach; ?></select></div>
            <div class="col-12 text-end"><a href="view_pos.php" class="btn btn-secondary">Clear</a><button type="submit" class="btn btn-primary">Apply Filters</button></div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><h5>All Purchase Orders</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead><tr><th>PO Number</th><th>Supplier</th><th>Order Date</th><th>Total Amount</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['po_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo ($row['order_date'] && $row['order_date'] != '0000-00-00') ? date("d M, Y", strtotime($row['order_date'])) : 'N/A'; ?></td>
                                <td>$<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status = htmlspecialchars($row['status']);
                                    $badge_class = 'bg-dark'; // Default for Draft
                                    if (in_array($status, ['Approved', 'Partially Delivered'])) $badge_class = 'bg-success';
                                    if ($status == 'Pending') $badge_class = 'bg-warning text-dark';
                                    if (in_array($status, ['Rejected', 'Canceled'])) $badge_class = 'bg-danger';
                                    if ($status == 'Completed') $badge_class = 'bg-info text-dark';
                                    echo '<span class="badge ' . $badge_class . '">' . $status . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="view_po_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                    <?php // Add an Edit button for Draft and Pending POs ?>
                                    <?php if (in_array($row['status'], ['Draft', 'Pending']) && has_permission('po_edit')): ?>
                                        <a href="edit_po.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No purchase orders found matching your criteria.</td></tr>
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