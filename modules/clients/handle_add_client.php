<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('client_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_clients.php');
    exit();
}

$client_name = $_POST['client_name'];
$contact_person = $_POST['contact_person'] ?? NULL;
$email = $_POST['email'];
$phone_number = $_POST['phone_number'] ?? NULL;
$username = !empty($_POST['username']) ? $_POST['username'] : NULL;
$password = $_POST['password'];

$conn = connect_db();

// Check if email or username already exists
$sql_check = "SELECT id FROM clients WHERE email = ? OR (username IS NOT NULL AND username = ?)";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("ss", $email, $username);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header("Location: add_client.php?status=error_exists");
    exit();
}

$hashed_password = !empty($password) ? password_hash($password, PASSWORD_DEFAULT) : NULL;

$sql = "INSERT INTO clients (client_name, contact_person, email, phone_number, username, password) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssss", $client_name, $contact_person, $email, $phone_number, $username, $hashed_password);

if ($stmt->execute()) {
    header("Location: view_clients.php?status=success");
} else {
    header("Location: add_client.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>