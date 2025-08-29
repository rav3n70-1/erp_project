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

<nav aria-label="breadcrumb" class="reveal">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/dashboard.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Suppliers</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4 reveal">
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
    if ($message) { echo '<div class="alert alert-'. $alert_type .' reveal">'. htmlspecialchars($message) .'</div>'; }
}
?>

<div class="card fade-in reveal">
    <div class="card-header">All Suppliers</div>
    <div class="card-body">
        <table class="table table-hover data-table">
            <thead>
                <tr>
                    <th>Supplier Name</th>
                    <th>Status</th>
                    <th>Contact Person</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Total Contacts</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr <?php echo $row['is_active'] ? '' : 'class="table-secondary"'; ?>>
                            <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                            <td>
                                <?php if ($row['is_active']): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Inactive</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($row['contact_person'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['phone'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo $row['contact_count']; ?></span>
                            </td>
                            <td>
                                <a href="view_supplier_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="edit_supplier.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-warning me-1" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteConfirmationModal" data-id="<?php echo $row['id']; ?>" data-name="<?php echo htmlspecialchars($row['supplier_name']); ?>" title="Delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No suppliers found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmationModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to delete supplier "<span class="modal-data-name"></span>"?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="delete_supplier.php" style="display: inline;">
          <input type="hidden" name="supplier_id" id="supplierIdToDelete">
          <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Add modern enhancement classes for better animations
document.addEventListener('DOMContentLoaded', function() {
    // Add staggered reveal animation to table rows
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-10px)';
        row.style.transition = 'all 0.4s ease';
        
        setTimeout(() => {
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 50 + 500);
    });

    // Show a subtle welcome message
    setTimeout(() => {
        if (window.erpToast) {
            erpToast('Suppliers page loaded successfully', 'info');
        }
    }, 1000);
});
</script>

<?php
$conn->close();
include('../../includes/footer.php');
?>