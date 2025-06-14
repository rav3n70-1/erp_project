<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('supplier_delete')) { // Use the delete permission for this action
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: view_suppliers.php');
    exit();
}

$supplier_id = $_POST['id'];

$conn = connect_db();
$sql = "UPDATE suppliers SET is_active = !is_active WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Toggled active status for supplier", 'Supplier', $supplier_id);
    header("Location: view_suppliers.php?status=supplier_status_toggled");
} else {
    header("Location: view_suppliers.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>