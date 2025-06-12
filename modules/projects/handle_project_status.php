<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

// Check if the user has the specific permission to approve projects
if (!has_permission('project_approve')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_projects.php');
    exit();
}

if (!isset($_POST['project_id']) || !is_numeric($_POST['project_id']) || !isset($_POST['new_status'])) {
    header('Location: view_projects.php?status=error');
    exit();
}

$project_id = $_POST['project_id'];
$new_status = $_POST['new_status'];

$allowed_statuses = ['Approved', 'Rejected'];
if (!in_array($new_status, $allowed_statuses)) {
    header("Location: view_project_details.php?id=" . $project_id . "&status=invalid_status");
    exit();
}

$conn = connect_db();

$sql = "UPDATE projects SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $project_id);

if ($stmt->execute()) {
    $action_description = "Project #" . $project_id . " status changed to " . $new_status;
    log_audit_trail($conn, $action_description, 'Project', $project_id);
    header("Location: view_project_details.php?id=" . $project_id . "&status_updated=true");
} else {
    header("Location: view_project_details.php?id=" . $project_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>