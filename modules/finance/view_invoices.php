<?php
$page_title = "Manage Invoices";
include('../../includes/header.php');

if (!has_permission('invoice_view') && !has_permission('invoice_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

$sql = "SELECT i.*, s.supplier_name, po.po_number
        FROM invoices i
        JOIN suppliers s ON i.supplier_id = s.id
        JOIN purchase_orders po ON i.po_id = po.id
        ORDER BY i.invoice_date DESC";
$result = $conn->query($sql);

$can_manage = has_permission('invoice_manage');
$can_approve = has_permission('invoice_approve');
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Finance</li>
    <li class="breadcrumb-item active" aria-current="page">Manage Invoices</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if ($can_manage): ?>
        <a href="log_invoice.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Log New Invoice</a>
    <?php endif; ?>
</div>

<?php 
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'success') { $message = 'Invoice logged successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'updated') { $message = 'Invoice status updated successfully!'; $alert_type = 'success'; }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
?>

<div class="card">
    <div class="card-header"><h5>All Invoices</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Invoice #</th>
                        <th>Supplier</th>
                        <th>PO #</th>
                        <th>Invoice Date</th>
                        <th>Due Date</th>
                        <th class="text-end">Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['invoice_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['po_number']); ?></td>
                                <td><?php echo date("d M, Y", strtotime($row['invoice_date'])); ?></td>
                                <td><?php echo date("d M, Y", strtotime($row['due_date'])); ?></td>
                                <td class="text-end">$<?php echo number_format($row['total_amount'], 2); ?></td>
                                <td>
                                    <?php 
                                    $status = htmlspecialchars($row['status']);
                                    $badge_class = 'bg-secondary';
                                    if ($status == 'Submitted') $badge_class = 'bg-warning text-dark';
                                    if ($status == 'Approved for Payment') $badge_class = 'bg-success';
                                    if ($status == 'Paid') $badge_class = 'bg-info text-dark';
                                    if ($status == 'Disputed') $badge_class = 'bg-danger';
                                    echo '<span class="badge ' . $badge_class . '">' . $status . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if (!empty($row['file_path'])): ?>
                                        <a href="/erp_project/<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-sm btn-info" target="_blank" title="View Attachment"><i class="bi bi-eye"></i></a>
                                    <?php endif; ?>
                                    
                                    <?php if ($row['status'] == 'Submitted' && $can_approve): ?>
                                        <form action="handle_invoice_status.php" method="POST" class="d-inline">
                                            <input type="hidden" name="invoice_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="new_status" value="Approved for Payment">
                                            <button type="submit" class="btn btn-sm btn-success" title="Approve for Payment"><i class="bi bi-check-circle"></i></button>
                                        </form>
                                        <form action="handle_invoice_status.php" method="POST" class="d-inline">
                                            <input type="hidden" name="invoice_id" value="<?php echo $row['id']; ?>">
                                            <input type="hidden" name="new_status" value="Disputed">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Dispute Invoice"><i class="bi bi-x-circle"></i></button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">No invoices have been logged yet.</td></tr>
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