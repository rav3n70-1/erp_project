<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('project_create')) { // Using create permission for editing as well
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_projects.php');
    exit();
}

$required_fields = ['project_id', 'project_name', 'start_date', 'status'];
// ... (validation logic) ...

$project_id = $_POST['project_id'];
$project_name = $_POST['project_name'];
$description = $_POST['description'] ?? NULL;
$start_date = $_POST['start_date'];
$end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
$manager_id = !empty($_POST['manager_id']) ? $_POST['manager_id'] : NULL;
$budget_id = !empty($_POST['budget_id']) ? $_POST['budget_id'] : NULL;
$status = $_POST['status'];

$conn = connect_db();

$sql = "UPDATE projects SET 
            project_name = ?, description = ?, start_date = ?, end_date = ?, 
            budget_id = ?, manager_id = ?, status = ?
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssiisi", $project_name, $description, $start_date, $end_date, $budget_id, $manager_id, $status, $project_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Edited project: " . $project_name, 'Project', $project_id);
    header("Location: view_projects.php?status=updated");
} else {
    header("Location: edit_project.php?id=" . $project_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>