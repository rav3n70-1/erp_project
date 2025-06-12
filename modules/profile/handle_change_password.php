<?php
include('../../includes/db.php');
include('../../includes/session_check.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

$required_fields = ['current_password', 'new_password', 'confirm_password'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: index.php?status=error_missing');
        exit();
    }
}

$current_user_id = $_SESSION['user_id'];
$current_password = $_POST['current_password'];
$new_password = $_POST['new_password'];
$confirm_password = $_POST['confirm_password'];

// 1. Check if the new password and confirmation match
if ($new_password !== $confirm_password) {
    header("Location: index.php?status=error_mismatch");
    exit();
}

$conn = connect_db();

// 2. Fetch the user's current hashed password from the database
$sql_user = "SELECT password FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $current_user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// 3. Verify the submitted "current password" against the stored hash
if (!password_verify($current_password, $user['password'])) {
    header("Location: index.php?status=error_current_password");
    exit();
}

// 4. If everything is correct, hash the new password
$new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

// 5. Update the database with the new hashed password
$sql_update = "UPDATE users SET password = ? WHERE id = ?";
$stmt_update = $conn->prepare($sql_update);
$stmt_update->bind_param("si", $new_hashed_password, $current_user_id);

if ($stmt_update->execute()) {
    log_audit_trail($conn, "User changed their own password", 'User', $current_user_id);
    header("Location: index.php?status=success_password");
} else {
    header("Location: index.php?status=error_db");
}

$stmt_update->close();
$conn->close();
exit();
?>