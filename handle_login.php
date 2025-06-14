<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit();
}

if (empty($_POST['username']) || empty($_POST['password'])) {
    header('Location: login.php?error=empty');
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$conn = connect_db();

// UPDATED QUERY: Now checks if the user is active (is_active = 1)
$sql = "SELECT u.*, r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.id 
        WHERE u.username = ? AND u.is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        // Fetch all permission keys for this user's role
        $sql_permissions = "SELECT permission_key FROM role_permissions WHERE role_id = ?";
        $stmt_perms = $conn->prepare($sql_permissions);
        $stmt_perms->bind_param("i", $user['role_id']);
        $stmt_perms->execute();
        $permissions_result = $stmt_perms->get_result();
        
        $permissions = [];
        while ($row = $permissions_result->fetch_assoc()) {
            $permissions[] = $row['permission_key'];
        }

        // Create session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role_name'] = $user['role_name'];
        $_SESSION['permissions'] = $permissions;

        header('Location: index.php');
        exit();
    }
}

// If we reach here, login was invalid (user not found, password wrong, or account inactive)
header('Location: login.php?error=invalid');
exit();
?>