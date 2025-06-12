<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('invoice_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: log_invoice.php');
    exit();
}

$required_fields = ['po_id', 'invoice_number', 'invoice_date', 'due_date', 'total_amount'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: log_invoice.php?status=error_missing');
        exit();
    }
}

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

if (!$supplier_id) {
    header('Location: log_invoice.php?status=error_po');
    exit();
}

// File Upload Handling
if (isset($_FILES['invoice_file']) && $_FILES['invoice_file']['error'] == 0) {
    $upload_dir = '../../uploads/invoices/';
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
    
    $file_extension = pathinfo($_FILES['invoice_file']['name'], PATHINFO_EXTENSION);
    $unique_name = 'invoice_' . $po_id . '_' . time() . '.' . $file_extension;
    
    if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $upload_dir . $unique_name)) {
        $file_path = 'uploads/invoices/' . $unique_name;
    }
}

$sql = "INSERT INTO invoices (po_id, supplier_id, invoice_number, invoice_date, due_date, total_amount, file_path) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssds", $po_id, $supplier_id, $invoice_number, $invoice_date, $due_date, $total_amount, $file_path);

if ($stmt->execute()) {
    header("Location: view_invoices.php?status=success");
} else {
    header("Location: log_invoice.php?status=error");
}

$conn->close();
exit();
?>