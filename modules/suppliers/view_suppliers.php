<?php
$page_title = "Manage Suppliers";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

// UPDATED QUERY: Fetch is_active status and sort by it to move inactive suppliers to the bottom.
$sql = "SELECT s.*, COUNT(sc.id) as contact_count 
        FROM suppliers s 
        LEFT JOIN supplier_contacts sc ON s.id = sc.supplier_id 
        GROUP BY s.id
        ORDER BY s.is_active DESC, s.supplier_name ASC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if (has_permission('po_create')): // Use a relevant permission ?>
        <a href="add_supplier.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add New Supplier</a>
    <?php endif; ?>
</div>

<?php
if(isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    $status_map = [
        'success' => ['msg' => 'Supplier added successfully!', 'type' => 'success'],
        'updated' => ['msg' => 'Supplier updated successfully!', 'type' => 'success'],
        'supplier_status_toggled' => ['msg' => 'Supplier status has been updated.', 'type' => 'success'],
        'error' => ['msg' => 'An error occurred. Please try again.', 'type' => 'danger'],
    ];
    if (array_key_exists($_GET['status'], $status_map)) {
        $message = $status_map[$_GET['status']]['msg'];
        $alert_type = $status_map[$_GET['status']]['type'];
    }
    if ($message) { echo '<div class="alert alert-'. $alert_type .'">'. htmlspecialchars($message) .'</div>'; }
}
?>

<div class="card fade-in">
    <div class="card-header">All Suppliers</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead class="table-dark">
                    <tr><th>#</th><th>Supplier Name</th><th>Status</th><th>Tax ID</th><th>Contacts</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="<?php echo $row['is_active'] ? '' : 'table-secondary text-muted'; ?>">
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?></td>
                                <td><?php echo htmlspecialchars($row['tax_id']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $row['contact_count']; ?></span></td>
                                <td>
                                    <a href="view_supplier_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                    <?php if (has_permission('po_edit')): ?>
                                        <a href="edit_supplier.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                    <?php endif; ?>
                                    <?php if (has_permission('supplier_delete')): ?>
                                        <form action="handle_toggle_supplier_status.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to change this supplier\'s status?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <?php if ($row['is_active']): ?>
                                                <button type="submit" class="btn btn-sm btn-danger" title="Deactivate Supplier"><i class="bi bi-pause-circle-fill"></i></button>
                                            <?php else: ?>
                                                 <button type="submit" class="btn btn-sm btn-success" title="Activate Supplier"><i class="bi bi-play-circle-fill"></i></button>
                                            <?php endif; ?>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
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