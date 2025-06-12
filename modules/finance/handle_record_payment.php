<?php
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /erp_project/modules/purchase_orders/view_pos.php');
    exit();
}

// Validation
if (empty($_POST['po_id']) || empty($_POST['payment_date']) || empty($_POST['amount_paid']) || empty($_POST['payment_method'])) {
    die("Missing required data.");
}

$po_id = $_POST['po_id'];
$payment_date = $_POST['payment_date'];
$amount_paid = $_POST['amount_paid'];
$payment_method = $_POST['payment_method'];
$notes = $_POST['notes'] ?? NULL;

$conn = connect_db();

$sql = "INSERT INTO payments (po_id, payment_date, amount_paid, payment_method, notes) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isdss", $po_id, $payment_date, $amount_paid, $payment_method, $notes);

if ($stmt->execute()) {
    // Success
    header("Location: /erp_project/modules/purchase_orders/view_po_details.php?id=" . $po_id . "&status=payment_recorded");
} else {
    // Failure - you could log $stmt->error for debugging
    header("Location: /erp_project/modules/finance/record_payment.php?po_id=" . $po_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>