<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('user_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: manage_users.php');
    exit();
}

$user_id_to_toggle = $_POST['id'];
$current_user_id = $_SESSION['user_id'];

// CRITICAL SAFETY CHECK: Prevent an admin from deactivating their own account.
if ($user_id_to_toggle == $current_user_id) {
    header("Location: manage_users.php?status=error_self_deactivate");
    exit();
}

$conn = connect_db();

// This query cleverly flips the is_active value (0 becomes 1, 1 becomes 0)
$sql = "UPDATE users SET is_active = !is_active WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id_to_toggle);
$stmt->execute();

// Log this important action
$action_description = "Toggled active status for user ID #" . $user_id_to_toggle;
log_audit_trail($conn, $action_description, 'User', $user_id_to_toggle);

header("Location: manage_users.php?status=user_status_toggled");
$stmt->close();
$conn->close();
exit();
?>