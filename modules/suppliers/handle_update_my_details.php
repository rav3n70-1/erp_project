<?php
session_start();

if (!isset($_SESSION['supplier_id'])) {
    header('Location: /erp_project/supplier_login.php');
    exit();
}

include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: portal.php');
    exit();
}

$supplier_id = $_SESSION['supplier_id'];

// Collect all the submitted data into an array
$changes = [
    'bank_name' => $_POST['bank_name'] ?? '',
    'bank_account_number' => $_POST['bank_account_number'] ?? ''
    // In a real system, you would add all other editable fields here
    // e.g., 'address' => $_POST['address'], 'contact_email' => $_POST['contact_email']
];

// Convert the array of changes into a JSON string to store in the database
$change_data_json = json_encode($changes);

$conn = connect_db();

$sql = "INSERT INTO supplier_info_changes (supplier_id, change_data) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $supplier_id, $change_data_json);

if ($stmt->execute()) {
    header("Location: portal.php?status=change_request_success");
} else {
    header("Location: edit_my_details.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>