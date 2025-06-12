<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('invoice_delete')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_invoices.php');
    exit();
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: view_invoices.php?status=error');
    exit();
}

$invoice_id = $_POST['id'];

$conn = connect_db();

// (Optional: You might want to delete the associated file from the server here)
$sql = "DELETE FROM invoices WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $invoice_id);

if ($stmt->execute()) {
    header("Location: view_invoices.php?status=deleted");
} else {
    header("Location: view_invoices.php?status=error");
}

$conn->close();
exit();
?>