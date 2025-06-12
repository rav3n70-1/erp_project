<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('project_full_access')) { // Only users with full access can delete
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    header('Location: view_projects.php');
    exit();
}

$project_id = $_POST['id'];
$conn = connect_db();

$sql_name = "SELECT project_name FROM projects WHERE id = ?";
$stmt_name = $conn->prepare($sql_name);
$stmt_name->bind_param("i", $project_id);
$stmt_name->execute();
$project_name = $stmt_name->get_result()->fetch_assoc()['project_name'] ?? 'Unknown';

// With ON DELETE CASCADE, deleting a project will also delete all its tasks.
$sql = "DELETE FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Deleted project: " . $project_name, 'Project', $project_id);
    header("Location: view_projects.php?status=deleted");
} else {
    header("Location: view_projects.php?status=error");
}

$conn->close();
exit();
?>