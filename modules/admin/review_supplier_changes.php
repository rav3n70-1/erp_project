<?php
$page_title = "Review Supplier Information Changes";
include('../../includes/header.php');

// This page is for users with the 'supplier_info_approve' permission
if (!has_permission('supplier_info_approve')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch all pending change requests and join with suppliers table
$sql = "SELECT 
            sc.id as change_id, 
            sc.change_data,
            sc.requested_at,
            s.id as supplier_id,
            s.supplier_name
        FROM supplier_info_changes sc
        JOIN suppliers s ON sc.supplier_id = s.id
        WHERE sc.status = 'Pending'
        ORDER BY sc.requested_at ASC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active" aria-current="page">Review Supplier Changes</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'approved') { $message = 'Change request has been approved and applied.'; $alert_type = 'success'; }
    if ($_GET['status'] == 'rejected') { $message = 'Change request has been rejected.'; $alert_type = 'warning'; }
    if ($message) { echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
}
?>

<div class="card">
    <div class="card-header"><h5>Pending Requests</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Requested On</th>
                        <th>Proposed Changes</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): 
                            $changes = json_decode($row['change_data'], true);
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo date("d M, Y H:i", strtotime($row['requested_at'])); ?></td>
                                <td>
                                    <ul class="list-unstyled mb-0">
                                        <?php foreach($changes as $field => $value): ?>
                                            <li><strong><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $field))); ?>:</strong> <?php echo htmlspecialchars($value); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td class="text-end">
                                    <form action="handle_supplier_change_review.php" method="POST" class="d-inline">
                                        <input type="hidden" name="change_id" value="<?php echo $row['change_id']; ?>">
                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-success">Approve</button>
                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-danger">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="text-center text-muted">No pending change requests.</td></tr>
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