<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('asset_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_assets.php');
    exit();
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: view_assets.php?status=error');
    exit();
}

$asset_id = $_POST['id'];

$conn = connect_db();

// We need to fetch the asset name BEFORE deleting it, for logging purposes.
$sql_name = "SELECT asset_name FROM assets WHERE id = ?";
$stmt_name = $conn->prepare($sql_name);
$stmt_name->bind_param("i", $asset_id);
$stmt_name->execute();
$asset_name = $stmt_name->get_result()->fetch_assoc()['asset_name'] ?? 'Unknown';


$sql = "DELETE FROM assets WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $asset_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Deleted asset: " . $asset_name, 'Asset', $asset_id);
    header("Location: view_assets.php?status=deleted");
} else {
    header("Location: view_assets.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>