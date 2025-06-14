<?php
$page_title = "Manage Clients";
include('../../includes/header.php');

if (!has_permission('client_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

$sql = "SELECT * FROM clients ORDER BY is_active DESC, client_name ASC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Clients</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <a href="add_client.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add New Client</a>
</div>

<?php
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'success') { $message = 'Client added successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'updated') { $message = 'Client updated successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'toggled') { $message = 'Client status has been changed.'; $alert_type = 'success'; }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}
?>

<div class="card">
    <div class="card-header"><h5>All Clients</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead>
                    <tr><th>Client Name</th><th>Contact Person</th><th>Email</th><th>Phone</th><th>Status</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="<?php echo $row['is_active'] ? '' : 'table-secondary text-muted'; ?>">
                                <td><?php echo htmlspecialchars($row['client_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_person']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <td><?php echo $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?></td>
                                <td>
                                    <a href="edit_client.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit Client"><i class="bi bi-pencil-square"></i></a>
                                    <form action="handle_toggle_client_status.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to change this client\'s status?');">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <?php if ($row['is_active']): ?>
                                            <button type="submit" class="btn btn-sm btn-danger" title="Deactivate Client"><i class="bi bi-slash-circle-fill"></i></button>
                                        <?php else: ?>
                                             <button type="submit" class="btn btn-sm btn-success" title="Activate Client"><i class="bi bi-check-circle-fill"></i></button>
                                        <?php endif; ?>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center text-muted">No clients found.</td></tr>
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