<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('project_create') && !has_permission('project_full_access')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['project_id']) || empty($_POST['task_name'])) {
    header('Location: view_projects.php?status=error');
    exit();
}

$project_id = $_POST['project_id'];
$task_name = $_POST['task_name'];
$assigned_to = !empty($_POST['assigned_to_user_id']) ? $_POST['assigned_to_user_id'] : NULL;
$due_date = !empty($_POST['due_date']) ? $_POST['due_date'] : NULL;

$conn = connect_db();

$sql = "INSERT INTO project_tasks (project_id, task_name, assigned_to_user_id, due_date) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("isis", $project_id, $task_name, $assigned_to, $due_date);

if ($stmt->execute()) {
    $task_id = $conn->insert_id;

    // --- NOTIFICATION LOGIC AS REQUESTED ---
    // Find the System Admin user (role_id = 1)
    $sql_admin = "SELECT id FROM users WHERE role_id = 1 LIMIT 1";
    $admin_id = $conn->query($sql_admin)->fetch_assoc()['id'];

    if ($admin_id) {
        // Get project name for the message
        $sql_project = "SELECT project_name FROM projects WHERE id = ?";
        $stmt_project = $conn->prepare($sql_project);
        $stmt_project->bind_param("i", $project_id);
        $stmt_project->execute();
        $project_name = $stmt_project->get_result()->fetch_assoc()['project_name'];
        
        $notification_message = "New task '".htmlspecialchars($task_name)."' added to project '".htmlspecialchars($project_name)."'";
        $notification_link = "/erp_project/modules/projects/view_project_details.php?id=" . $project_id;
        
        $sql_notification = "INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)";
        $stmt_notification = $conn->prepare($sql_notification);
        $stmt_notification->bind_param("iss", $admin_id, $notification_message, $notification_link);
        $stmt_notification->execute();
    }
    
    header("Location: view_project_details.php?id=" . $project_id . "&status=task_added");
} else {
    header("Location: view_project_details.php?id=" . $project_id . "&status=task_error");
}

$conn->close();
exit();
?>