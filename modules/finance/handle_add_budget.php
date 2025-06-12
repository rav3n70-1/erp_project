<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

// CORRECTED: Use the new, specific permission key
if (!has_permission('budget_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_budgets.php');
    exit();
}

$required_fields = ['budget_name', 'department_id', 'allocated_amount', 'start_date', 'end_date'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: manage_budgets.php?status=error&msg=missing_fields');
        exit();
    }
}

$budget_name = $_POST['budget_name'];
$department_id = $_POST['department_id'];
$allocated_amount = $_POST['allocated_amount'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$conn = connect_db();

$sql = "INSERT INTO budgets (budget_name, department_id, allocated_amount, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sidss", $budget_name, $department_id, $allocated_amount, $start_date, $end_date);

if ($stmt->execute()) {
    header("Location: manage_budgets.php?status=success");
} else {
    header("Location: manage_budgets.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>