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

$required_fields = ['user_id', 'username', 'email', 'role_id'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: manage_users.php?status=error');
        exit();
    }
}

$user_id = $_POST['user_id'];
$username = $_POST['username'];
$email = $_POST['email'];
$role_id = $_POST['role_id'];
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Check if a new password was entered and if it matches the confirmation
if (!empty($password) && $password !== $confirm_password) {
    header("Location: edit_user.php?id=" . $user_id . "&status=error_password_mismatch");
    exit();
}

$conn = connect_db();

// Check if the new username or email already exists for another user
$sql_check = "SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ssi", $username, $email, $user_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header("Location: edit_user.php?id=" . $user_id . "&status=error_exists");
    exit();
}

// Prepare the SQL query. We will update the password conditionally.
if (!empty($password)) {
    // If a new password is set, hash it and update it in the DB
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET username = ?, email = ?, role_id = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssisi", $username, $email, $role_id, $hashed_password, $user_id);
} else {
    // If no new password, update everything else BUT the password
    $sql = "UPDATE users SET username = ?, email = ?, role_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $username, $email, $role_id, $user_id);
}

if ($stmt->execute()) {
    header("Location: manage_users.php?status=updated");
} else {
    header("Location: edit_user.php?id=" . $user_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>