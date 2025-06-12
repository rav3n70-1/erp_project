<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('Admin')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_budgets.php');
    exit();
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: manage_budgets.php?status=error');
    exit();
}

$budget_id = $_POST['id'];
$conn = connect_db();

// We set ON DELETE SET NULL for the foreign key, so deleting a budget will
// not fail but will instead set the budget_id on related POs to NULL.
$sql = "DELETE FROM budgets WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $budget_id);

if ($stmt->execute()) {
    header("Location: manage_budgets.php?status=deleted");
} else {
    header("Location: manage_budgets.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>