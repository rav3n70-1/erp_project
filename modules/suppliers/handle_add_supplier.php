<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

// Since we don't have a specific permission for this yet, we'll check for a general one.
if (!has_permission('po_create')) { // Assuming a procurement officer can add suppliers
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_supplier.php');
    exit();
}

$conn = connect_db();
$conn->begin_transaction();

try {
    $supplier_name = $_POST['supplier_name'];
    $address = $_POST['address'];
    $tax_id = $_POST['tax_id'];

    $sql_supplier = "INSERT INTO suppliers (supplier_name, address, tax_id) VALUES (?, ?, ?)";
    $stmt_supplier = $conn->prepare($sql_supplier);
    $stmt_supplier->bind_param("sss", $supplier_name, $address, $tax_id);
    $stmt_supplier->execute();
    $supplier_id = $conn->insert_id;

    if (!empty($_POST['contact_name'])) {
        $contact_name = $_POST['contact_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $sql_contact = "INSERT INTO supplier_contacts (supplier_id, contact_name, email, phone_number) VALUES (?, ?, ?, ?)";
        $stmt_contact = $conn->prepare($sql_contact);
        $stmt_contact->bind_param("isss", $supplier_id, $contact_name, $email, $phone_number);
        $stmt_contact->execute();
    }
    
    // --- NEW: Log this action to the audit trail ---
    log_audit_trail($conn, "Created new supplier", 'Supplier', $supplier_id);

    $conn->commit();
    header("Location: view_suppliers.php?status=success");

} catch (Exception $e) {
    $conn->rollback();
    header("Location: add_supplier.php?status=error");
}

$conn->close();
exit();
?>