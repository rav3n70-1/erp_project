<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('user_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_users.php');
    exit();
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: manage_users.php?status=error');
    exit();
}

$user_id_to_delete = $_POST['id'];
$current_user_id = $_SESSION['user_id'];

// *** CRITICAL SAFETY CHECK ***
// Prevent a user from deleting their own account.
if ($user_id_to_delete == $current_user_id) {
    header("Location: manage_users.php?status=error_self_delete");
    exit();
}

$conn = connect_db();

// Since other tables (like audit_log) link to users, we need to handle this gracefully.
// For now, we assume related data can be deleted. A more advanced system might disable the user instead.
$sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id_to_delete);

if ($stmt->execute()) {
    header("Location: manage_users.php?status=deleted");
} else {
    // This could fail if there are foreign key constraints from other tables.
    header("Location: manage_users.php?status=error_fk");
}

$stmt->close();
$conn->close();
exit();
?>