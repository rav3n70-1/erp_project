<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('client_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: view_clients.php');
    exit();
}

$client_id = $_POST['id'];

$conn = connect_db();

// Flips the is_active value (0 to 1, or 1 to 0)
$sql = "UPDATE clients SET is_active = !is_active WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $client_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Toggled active status for client", 'Client', $client_id);
    header("Location: view_clients.php?status=toggled");
} else {
    header("Location: view_clients.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>