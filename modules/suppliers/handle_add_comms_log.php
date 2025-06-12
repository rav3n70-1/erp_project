<?php
include('../../includes/db.php');

$redirect_url = "view_supplier_details.php?id=";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_suppliers.php');
    exit();
}

if (!isset($_POST['supplier_id'], $_POST['log_type'], $_POST['log_date'], $_POST['notes'])) {
    die("Required form data is missing.");
}

$supplier_id = $_POST['supplier_id'];
$log_type = $_POST['log_type'];
$log_date = $_POST['log_date'];
$notes = $_POST['notes'];
$redirect_url .= $supplier_id;

$conn = connect_db();
$sql = "INSERT INTO supplier_communication_logs (supplier_id, log_type, log_date, notes) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isss", $supplier_id, $log_type, $log_date, $notes);

if ($stmt->execute()) {
    header("Location: " . $redirect_url . "&status=log_success");
} else {
    header("Location: " . $redirect_url . "&status=log_error");
}

$stmt->close();
$conn->close();
exit();
?>