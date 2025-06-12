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

$required_fields = ['asset_id', 'asset_name', 'asset_tag', 'asset_type_id', 'status'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header("Location: edit_asset.php?id=" . $_POST['asset_id'] . "&status=error_missing");
        exit();
    }
}

$asset_id = $_POST['asset_id'];
$asset_name = $_POST['asset_name'];
$asset_tag = $_POST['asset_tag'];
$asset_type_id = $_POST['asset_type_id'];
$status = $_POST['status'];
$purchase_date = !empty($_POST['purchase_date']) ? $_POST['purchase_date'] : NULL;
$purchase_cost = !empty($_POST['purchase_cost']) ? $_POST['purchase_cost'] : NULL;
$assigned_to = !empty($_POST['assigned_to_employee_id']) ? $_POST['assigned_to_employee_id'] : NULL;
$notes = $_POST['notes'] ?? NULL;

$conn = connect_db();

// Check if Asset Tag already exists for another asset
$sql_check = "SELECT id FROM assets WHERE asset_tag = ? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $asset_tag, $asset_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header("Location: edit_asset.php?id=" . $asset_id . "&status=error_exists");
    exit();
}

$sql = "UPDATE assets SET 
            asset_name = ?, asset_tag = ?, asset_type_id = ?, purchase_date = ?, 
            purchase_cost = ?, assigned_to_employee_id = ?, status = ?, notes = ?
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssisdissi", $asset_name, $asset_tag, $asset_type_id, $purchase_date, $purchase_cost, $assigned_to, $status, $notes, $asset_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Edited asset: " . $asset_name, 'Asset', $asset_id);
    header("Location: view_assets.php?status=updated");
} else {
    header("Location: edit_asset.php?id=" . $asset_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>