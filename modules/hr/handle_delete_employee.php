<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

// This is a highly destructive action, restrict it to System Admin only.
// Our has_permission() function automatically allows 'System Admin'.
// We check for a specific key that ONLY the admin will have.
if (!has_permission('user_manage')) { // Using 'user_manage' as a proxy for top-level admin actions
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: view_employees.php?status=error');
    exit();
}

$employee_id = $_POST['id'];

$conn = connect_db();

// We need to fetch the employee name BEFORE deleting it, for logging purposes.
$sql_name = "SELECT first_name, last_name FROM employees WHERE id = ?";
$stmt_name = $conn->prepare($sql_name);
$stmt_name->bind_param("i", $employee_id);
$stmt_name->execute();
$employee = $stmt_name->get_result()->fetch_assoc();
$employee_name = $employee ? ($employee['first_name'] . ' ' . $employee['last_name']) : 'Unknown';


// This is a permanent deletion.
$sql = "DELETE FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Permanently deleted employee: " . $employee_name, 'Employee', $employee_id);
    header("Location: view_employees.php?status=deleted");
} else {
    // This could fail if other strict foreign key constraints exist.
    header("Location: view_employees.php?status=error_fk");
}

$stmt->close();
$conn->close();
exit();
?>