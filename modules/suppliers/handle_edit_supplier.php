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

    // --- UPDATE SUPPLIER LOGIN ---
    $update_parts = [];
    $params = [];
    $types = '';

    if (!empty($username)) {
        $update_parts[] = "username = ?";
        $types .= 's';
        $params[] = $username;
    }
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $update_parts[] = "password = ?";
        $types .= 's';
        $params[] = $hashed_password;
    }
    if (!empty($update_parts)) {
        $sql_login = "UPDATE suppliers SET " . implode(', ', $update_parts) . " WHERE id = ?";
        $types .= 'i';
        $params[] = $supplier_id;
        $stmt_login = $conn->prepare($sql_login);
        $stmt_login->bind_param($types, ...$params);
        $stmt_login->execute();
    }
    
    // --- UPDATE SUPPLIER DETAILS ---
    $sql_supplier = "UPDATE suppliers SET supplier_name = ?, address = ?, tax_id = ? WHERE id = ?";
    $stmt_supplier = $conn->prepare($sql_supplier);
    $stmt_supplier->bind_param("sssi", $supplier_name, $address, $tax_id, $supplier_id);
    $stmt_supplier->execute();

    // --- UPDATE RATINGS (if user has permission) ---
    if (has_permission('supplier_rate')) {
        $rating_delivery_time = !empty($_POST['rating_delivery_time']) ? $_POST['rating_delivery_time'] : NULL;
        $rating_quality = !empty($_POST['rating_quality']) ? $_POST['rating_quality'] : NULL;
        $rating_communication = !empty($_POST['rating_communication']) ? $_POST['rating_communication'] : NULL;
        
        $sql_ratings = "UPDATE suppliers SET rating_delivery_time = ?, rating_quality = ?, rating_communication = ? WHERE id = ?";
        $stmt_ratings = $conn->prepare($sql_ratings);
        $stmt_ratings->bind_param("dddi", $rating_delivery_time, $rating_quality, $rating_communication, $supplier_id);
        $stmt_ratings->execute();
    }

    // --- UPDATE CONTACT DETAILS ---
    if (!empty($_POST['contact_id'])) {
        // ... (contact update logic is unchanged)
    }
    
    log_audit_trail($conn, "Edited supplier details", 'Supplier', $supplier_id);
    
    $conn->commit();
    header("Location: view_supplier_details.php?id=" . $supplier_id . "&status=updated");

} catch (Exception $e) {
    $conn->rollback();
    error_log($e->getMessage());
    header("Location: edit_supplier.php?id=" . $_POST['supplier_id'] . "&status=error");
}

$conn->close();
exit();
?>