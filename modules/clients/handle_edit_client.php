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

// Basic validation for required fields
$required_fields = ['client_id', 'client_name', 'email'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: view_clients.php?status=error_missing');
        exit();
    }
}

$client_id = $_POST['client_id'];
$client_name = $_POST['client_name'];
$contact_person = $_POST['contact_person'] ?? NULL;
$email = $_POST['email'];
$phone_number = $_POST['phone_number'] ?? NULL;
$username = !empty($_POST['username']) ? $_POST['username'] : NULL;
$password = $_POST['password'];

$conn = connect_db();

// Logic to update password ONLY if a new one is provided
if (!empty($password)) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE clients SET 
                client_name = ?, 
                contact_person = ?, 
                email = ?, 
                phone_number = ?, 
                username = ?, 
                password = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssi", $client_name, $contact_person, $email, $phone_number, $username, $hashed_password, $client_id);
} else {
    // If password field is blank, update everything else BUT the password
    $sql = "UPDATE clients SET 
                client_name = ?, 
                contact_person = ?, 
                email = ?, 
                phone_number = ?, 
                username = ? 
            WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $client_name, $contact_person, $email, $phone_number, $username, $client_id);
}

if ($stmt->execute()) {
    log_audit_trail($conn, "Edited client: " . $client_name, 'Client', $client_id);
    header("Location: view_clients.php?status=updated");
} else {
    // Check for duplicate email/username error
    if ($conn->errno == 1062) {
         header("Location: edit_client.php?id=" . $client_id . "&status=error_exists");
    } else {
         header("Location: edit_client.php?id=" . $client_id . "&status=error");
    }
}

$stmt->close();
$conn->close();
exit();
?>