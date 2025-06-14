<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('asset_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: view_assets.php');
    exit();
}

$asset_id = $_POST['id'];

$conn = connect_db();

// This query cleverly flips the is_active value (0 becomes 1, 1 becomes 0)
$sql = "UPDATE assets SET is_active = !is_active WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $asset_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Toggled active status for asset", 'Asset', $asset_id);
    header("Location: view_assets.php?status=toggled");
} else {
    header("Location: view_assets.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>