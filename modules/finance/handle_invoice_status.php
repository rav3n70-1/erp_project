<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('invoice_approve')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_invoices.php');
    exit();
}

if (!isset($_POST['invoice_id']) || !is_numeric($_POST['invoice_id']) || !isset($_POST['new_status'])) {
    header('Location: view_invoices.php?status=error');
    exit();
}

$invoice_id = $_POST['invoice_id'];
$new_status = $_POST['new_status'];

$allowed_statuses = ['Approved for Payment', 'Disputed'];
if (!in_array($new_status, $allowed_statuses)) {
    header("Location: view_invoices.php?status=invalid_status");
    exit();
}

$conn = connect_db();

$sql = "UPDATE invoices SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $invoice_id);

if ($stmt->execute()) {
    $action_description = "Invoice #" . $invoice_id . " status changed to " . $new_status;
    log_audit_trail($conn, $action_description, 'Invoice', $invoice_id);
    header("Location: view_invoices.php?status=updated");
} else {
    header("Location: view_invoices.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>