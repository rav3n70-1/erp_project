<?php
$page_title = "Manage Suppliers";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

$sql = "SELECT s.*, COUNT(sc.id) as contact_count 
        FROM suppliers s 
        LEFT JOIN supplier_contacts sc ON s.id = sc.supplier_id 
        GROUP BY s.id
        ORDER BY s.id ASC";
$result = $conn->query($sql);

$can_add_supplier = has_permission(['Manager', 'Procurement Officer']);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if ($can_add_supplier): ?>
        <a href="add_supplier.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add New Supplier</a>
    <?php endif; ?>
</div>

<?php
if(isset($_GET['status'])) {
    $message = '';
    $alert_type = 'info';
    $status_map = [
        'success'           => ['msg' => 'Supplier added successfully!', 'type' => 'success'],
        'updated'           => ['msg' => 'Supplier updated successfully!', 'type' => 'success'],
        'deleted'           => ['msg' => 'Supplier has been deleted.', 'type' => 'warning'],
        'delete_error_linked' => ['msg' => 'Error: Cannot delete supplier. They are linked to existing POs, invoices, or other records.', 'type' => 'danger'],
        'error'             => ['msg' => 'An error occurred. Please try again.', 'type' => 'danger'],
    ];
    if (array_key_exists($_GET['status'], $status_map)) {
        $message = $status_map[$_GET['status']]['msg'];
        $alert_type = $status_map[$_GET['status']]['type'];
    }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">
            '. htmlspecialchars($message) .'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}
?>

<div class="row">
    <?php if ($can_add_supplier): ?>
    <div class="col-md-4">
        <div class="card fade-in">
            <div class="card-header"><h5>Add New Supplier</h5></div>
            <div class="card-body">
                <p>Click the button on the top right to add a new supplier.</p>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="<?php echo $can_add_supplier ? 'col-md-8' : 'col-md-12'; ?>">
        <div class="card fade-in" style="animation-delay: 0.1s;">
            <div class="card-header">All Suppliers</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover data-table">
                        <thead class="table-dark">
                            <tr><th scope="col">#</th><th scope="col">Supplier Name</th><th scope="col">Tax ID</th><th scope="col">Contacts</th><th scope="col">Date Added</th><th scope="col">Actions</th></tr>
                        </thead>
                        <tbody>
                            <?php if ($result && $result->num_rows > 0): ?>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <th scope="row"><?php echo $row['id']; ?></th>
                                        <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tax_id']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo $row['contact_count']; ?></span></td>
                                        <td><?php echo date("d M, Y", strtotime($row['created_at'])); ?></td>
                                        <td>
                                            <a href="view_supplier_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="View"><i class="bi bi-eye"></i></a>
                                            <?php if (has_permission(['Manager', 'Procurement Officer'])): ?>
                                                <a href="edit_supplier.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                            <?php endif; ?>
                                            <?php if (has_permission('Manager')): ?>
                                                <button type="button" class="btn btn-sm btn-danger"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#deleteConfirmationModal"
                                                        data-id="<?php echo $row['id']; ?>"
                                                        data-name="<?php echo htmlspecialchars($row['supplier_name']); ?>">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="6" class="text-center">No suppliers found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>