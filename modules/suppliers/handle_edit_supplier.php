<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('po_edit')) { 
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_suppliers.php');
    exit();
}

$conn = connect_db();
$conn->begin_transaction();

try {
    $supplier_id = $_POST['supplier_id'];
    $supplier_name = $_POST['supplier_name'];
    $address = $_POST['address'];
    $tax_id = $_POST['tax_id'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    // -- UPDATE SUPPLIER LOGIN --
    if (!empty($password)) {
        // If a new password is set, hash it and update it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql_login = "UPDATE suppliers SET username = ?, password = ? WHERE id = ?";
        $stmt_login = $conn->prepare($sql_login);
        $stmt_login->bind_param("ssi", $username, $hashed_password, $supplier_id);
        $stmt_login->execute();
    } else {
        // Otherwise, just update the username
        $sql_login = "UPDATE suppliers SET username = ? WHERE id = ?";
        $stmt_login = $conn->prepare($sql_login);
        $stmt_login->bind_param("si", $username, $supplier_id);
        $stmt_login->execute();
    }
    
    // -- UPDATE SUPPLIER DETAILS (Existing logic) --
    $sql_supplier = "UPDATE suppliers SET supplier_name = ?, address = ?, tax_id = ? WHERE id = ?";
    $stmt_supplier = $conn->prepare($sql_supplier);
    $stmt_supplier->bind_param("sssi", $supplier_name, $address, $tax_id, $supplier_id);
    $stmt_supplier->execute();

    // -- UPDATE CONTACT DETAILS (Existing logic) --
    if (!empty($_POST['contact_id'])) {
        $contact_id = $_POST['contact_id'];
        $contact_name = $_POST['contact_name'];
        $email = $_POST['email'];
        $phone_number = $_POST['phone_number'];
        $sql_contact = "UPDATE supplier_contacts SET contact_name = ?, email = ?, phone_number = ? WHERE id = ?";
        $stmt_contact = $conn->prepare($sql_contact);
        $stmt_contact->bind_param("sssi", $contact_name, $email, $phone_number, $contact_id);
        $stmt_contact->execute();
    }
    
    log_audit_trail($conn, "Edited supplier details", 'Supplier', $supplier_id);
    
    $conn->commit();
    header("Location: view_supplier_details.php?id=" . $supplier_id . "&status=updated");

} catch (Exception $e) {
    $conn->rollback();
    header("Location: edit_supplier.php?id=" . $_POST['supplier_id'] . "&status=error");
}

$conn->close();
exit();
?>