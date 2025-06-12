<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

// We now check for the specific 'supplier_delete' permission
if (!has_permission('supplier_delete')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_suppliers.php');
    exit();
}

// CORRECTED: The ID now comes from the generic delete modal, which uses name="supplier_id"
if (!isset($_POST['supplier_id']) || !is_numeric($_POST['supplier_id'])) {
    header('Location: view_suppliers.php?status=error');
    exit();
}
$supplier_id = $_POST['supplier_id'];

$conn = connect_db();

$sql = "DELETE FROM suppliers WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $supplier_id);

// Use a try-catch block to handle potential database errors
try {
    if ($stmt->execute()) {
        log_audit_trail($conn, "Deleted supplier", 'Supplier', $supplier_id);
        header("Location: view_suppliers.php?status=deleted");
    } else {
        header("Location: view_suppliers.php?status=error");
    }
} catch (mysqli_sql_exception $e) {
    // Catch the specific foreign key constraint error
    // MySQL error code 1451 is for foreign key violations
    if ($e->getCode() == 1451) {
        // Redirect with a specific error message
        header("Location: view_suppliers.php?status=delete_error_linked");
    } else {
        // For any other database error, show a generic error
        error_log($e->getMessage()); // Log the actual error for the admin
        header("Location: view_suppliers.php?status=error");
    }
}

$stmt->close();
$conn->close();
exit();
?>