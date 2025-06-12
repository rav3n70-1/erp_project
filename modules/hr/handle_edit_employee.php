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

$required_fields = ['employee_id', 'first_name', 'last_name', 'email', 'hire_date', 'job_title', 'department_id'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: edit_employee.php?id=' . $_POST['employee_id'] . '&status=error_missing');
        exit();
    }
}

$employee_id = $_POST['employee_id'];
$first_name = $_POST['first_name'];
$last_name = $_POST['last_name'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'] ?? NULL;
$hire_date = $_POST['hire_date'];
$job_title = $_POST['job_title'];
$salary = !empty($_POST['salary']) ? $_POST['salary'] : NULL;
$department_id = $_POST['department_id'];
$user_id = !empty($_POST['user_id']) ? $_POST['user_id'] : NULL;

$conn = connect_db();

// Check if email already exists for another employee
$sql_check = "SELECT id FROM employees WHERE email = ? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $email, $employee_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header("Location: edit_employee.php?id=" . $employee_id . "&status=error_exists");
    exit();
}

$sql = "UPDATE employees SET 
            first_name = ?, last_name = ?, email = ?, phone_number = ?, 
            hire_date = ?, job_title = ?, salary = ?, department_id = ?, user_id = ?
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssdiii", $first_name, $last_name, $email, $phone_number, $hire_date, $job_title, $salary, $department_id, $user_id, $employee_id);

if ($stmt->execute()) {
    header("Location: view_employees.php?status=updated");
} else {
    header("Location: edit_employee.php?id=" . $employee_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>