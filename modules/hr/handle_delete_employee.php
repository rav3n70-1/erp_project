<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('hr_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_employees.php');
    exit();
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: view_employees.php?status=error');
    exit();
}

$employee_id = $_POST['id'];

$conn = connect_db();
$sql = "DELETE FROM employees WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $employee_id);

if ($stmt->execute()) {
    header("Location: view_employees.php?status=deleted");
} else {
    header("Location: view_employees.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>