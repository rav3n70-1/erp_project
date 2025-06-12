<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('Manager')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_budgets.php');
    exit();
}

$required_fields = ['budget_id', 'budget_name', 'department_id', 'allocated_amount', 'start_date', 'end_date'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: manage_budgets.php?status=error');
        exit();
    }
}

$budget_id = $_POST['budget_id'];
$budget_name = $_POST['budget_name'];
$department_id = $_POST['department_id'];
$allocated_amount = $_POST['allocated_amount'];
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

$conn = connect_db();
$sql = "UPDATE budgets SET budget_name = ?, department_id = ?, allocated_amount = ?, start_date = ?, end_date = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sidssi", $budget_name, $department_id, $allocated_amount, $start_date, $end_date, $budget_id);

if ($stmt->execute()) {
    header("Location: manage_budgets.php?status=updated");
} else {
    header("Location: manage_budgets.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>