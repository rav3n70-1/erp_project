<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: client_login.php');
    exit();
}

if (empty($_POST['username']) || empty($_POST['password'])) {
    header('Location: client_login.php?error=1');
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$conn = connect_db();

// Fetch the client but ONLY if their account is active
$sql = "SELECT id, client_name, password FROM clients WHERE username = ? AND is_active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $client = $result->fetch_assoc();

    // Verify the password against the stored hash
    if (password_verify($password, $client['password'])) {
        // Password is correct, create session variables specific to clients
        $_SESSION['client_id'] = $client['id'];
        $_SESSION['client_name'] = $client['client_name'];

        // Redirect to the client portal dashboard
        header('Location: modules/clients/portal.php');
        exit();
    }
}

// If we reach here, login was invalid
header('Location: client_login.php?error=1');
exit();
?>