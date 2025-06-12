<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('hr_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: add_employee.php');
    exit();
}

$required_fields = ['first_name', 'last_name', 'email', 'hire_date', 'job_title', 'department_id'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: add_employee.php?status=error_missing');
        exit();
    }
}

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

// Check if email already exists
$sql_check = "SELECT id FROM employees WHERE email = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header("Location: add_employee.php?status=error_exists");
    exit();
}

$sql = "INSERT INTO employees (first_name, last_name, email, phone_number, hire_date, job_title, salary, department_id, user_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssdii", $first_name, $last_name, $email, $phone_number, $hire_date, $job_title, $salary, $department_id, $user_id);

if ($stmt->execute()) {
    header("Location: view_employees.php?status=success");
} else {
    header("Location: add_employee.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>