<?php
session_start();
include('includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: supplier_login.php');
    exit();
}

if (empty($_POST['username']) || empty($_POST['password'])) {
    header('Location: supplier_login.php?error=1');
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$conn = connect_db();

$sql = "SELECT id, supplier_name, password FROM suppliers WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $supplier = $result->fetch_assoc();

    // Verify the password against the stored hash
    if (password_verify($password, $supplier['password'])) {
        // Password is correct, create session variables specific to suppliers
        $_SESSION['supplier_id'] = $supplier['id'];
        $_SESSION['supplier_name'] = $supplier['supplier_name'];

        // Redirect to the supplier portal dashboard
        header('Location: modules/suppliers/portal.php');
        exit();
    }
}

// If we reach here, login was invalid
header('Location: supplier_login.php?error=1');
exit();
?>