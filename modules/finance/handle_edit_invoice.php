<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('invoice_edit')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_invoices.php');
    exit();
}

$required_fields = ['invoice_id', 'po_id', 'invoice_number', 'invoice_date', 'due_date', 'total_amount'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: view_invoices.php?status=error');
        exit();
    }
}

$invoice_id = $_POST['invoice_id'];
$po_id = $_POST['po_id'];
$invoice_number = $_POST['invoice_number'];
$invoice_date = $_POST['invoice_date'];
$due_date = $_POST['due_date'];
$total_amount = $_POST['total_amount'];
$file_path = NULL;

$conn = connect_db();

// Get supplier_id from the PO
$sql_supplier = "SELECT supplier_id FROM purchase_orders WHERE id = ?";
$stmt_supp = $conn->prepare($sql_supplier);
$stmt_supp->bind_param("i", $po_id);
$stmt_supp->execute();
$supplier_id = $stmt_supp->get_result()->fetch_assoc()['supplier_id'];

// File Upload Handling (if a new file is provided)
if (isset($_FILES['invoice_file']) && $_FILES['invoice_file']['error'] == 0) {
    // (You would add logic here to delete the old file if it exists)
    $upload_dir = '../../uploads/invoices/';
    $file_extension = pathinfo($_FILES['invoice_file']['name'], PATHINFO_EXTENSION);
    $unique_name = 'invoice_' . $po_id . '_' . time() . '.' . $file_extension;
    if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $upload_dir . $unique_name)) {
        $file_path = 'uploads/invoices/' . $unique_name;
    }
}

// Prepare the update query
if ($file_path) {
    // If a new file was uploaded, update the file path
    $sql = "UPDATE invoices SET po_id = ?, supplier_id = ?, invoice_number = ?, invoice_date = ?, due_date = ?, total_amount = ?, file_path = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssdsi", $po_id, $supplier_id, $invoice_number, $invoice_date, $due_date, $total_amount, $file_path, $invoice_id);
} else {
    // Otherwise, do not update the file path
    $sql = "UPDATE invoices SET po_id = ?, supplier_id = ?, invoice_number = ?, invoice_date = ?, due_date = ?, total_amount = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssdi", $po_id, $supplier_id, $invoice_number, $invoice_date, $due_date, $total_amount, $invoice_id);
}

if ($stmt->execute()) {
    header("Location: view_invoices.php?status=updated");
} else {
    header("Location: edit_invoice.php?id=" . $invoice_id . "&status=error");
}

$conn->close();
exit();
?>