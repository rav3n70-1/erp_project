<?php
session_start();

// Security Check: Ensure a supplier is logged in
if (!isset($_SESSION['supplier_id'])) {
    header('Location: /erp_project/supplier_login.php');
    exit();
}

include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: portal.php');
    exit();
}

$required_fields = ['po_id', 'invoice_number', 'invoice_date', 'due_date', 'total_amount'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: portal.php?status=error_missing');
        exit();
    }
}

// Get all data from the form
$po_id = $_POST['po_id'];
$invoice_number = $_POST['invoice_number'];
$invoice_date = $_POST['invoice_date'];
$due_date = $_POST['due_date'];
$total_amount = $_POST['total_amount'];
$file_path = NULL;
$supplier_id = $_SESSION['supplier_id']; // Get supplier ID from the session for security

$conn = connect_db();

// --- File Upload Handling ---
if (isset($_FILES['invoice_file']) && $_FILES['invoice_file']['error'] == 0) {
    $upload_dir = '../../uploads/invoices/';
    if (!is_dir($upload_dir)) { mkdir($upload_dir, 0755, true); }
    
    $file_extension = pathinfo($_FILES['invoice_file']['name'], PATHINFO_EXTENSION);
    $unique_name = 'invoice_supp_' . $supplier_id . '_' . time() . '.' . $file_extension;
    
    // Validate file type and size
    $allowed_types = ['application/pdf', 'image/jpeg', 'image/png'];
    $max_size = 5 * 1024 * 1024; // 5 MB

    if (in_array($_FILES['invoice_file']['type'], $allowed_types) && $_FILES['invoice_file']['size'] <= $max_size) {
        if (move_uploaded_file($_FILES['invoice_file']['tmp_name'], $upload_dir . $unique_name)) {
            $file_path = 'uploads/invoices/' . $unique_name;
        }
    } else {
        header("Location: portal.php?status=error_file");
        exit();
    }
}

// Insert the new invoice into the database
$sql = "INSERT INTO invoices (po_id, supplier_id, invoice_number, invoice_date, due_date, total_amount, file_path, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, 'Submitted')";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iisssds", $po_id, $supplier_id, $invoice_number, $invoice_date, $due_date, $total_amount, $file_path);

if ($stmt->execute()) {
    header("Location: portal.php?status=success_upload");
} else {
    // Check for duplicate invoice number error
    if ($conn->errno == 1062) {
        header("Location: portal.php?status=error_duplicate");
    } else {
        header("Location: portal.php?status=error_db");
    }
}

$stmt->close();
$conn->close();
exit();
?>