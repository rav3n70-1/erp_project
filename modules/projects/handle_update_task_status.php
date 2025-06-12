<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

$data = json_decode(file_get_contents('php://input'), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($data['task_id'], $data['new_status'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    exit();
}

$task_id = $data['task_id'];
$new_status = $data['new_status'];
$current_user_id = $_SESSION['user_id'];

$conn = connect_db();

// --- Permission Check ---
$sql_check = "SELECT assigned_to_user_id FROM project_tasks WHERE id = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("i", $task_id);
$stmt_check->execute();
$task = $stmt_check->get_result()->fetch_assoc();

$is_assigned_user = ($task && $task['assigned_to_user_id'] == $current_user_id);

// A user can update the task if they have 'project_full_access' OR if the task is assigned to them.
if (!has_permission('project_full_access') && !$is_assigned_user) {
    http_response_code(403); // Forbidden
    echo json_encode(['status' => 'error', 'message' => 'Permission denied.']);
    exit();
}

// If permission check passes, update the status
$sql = "UPDATE project_tasks SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $task_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Updated task status to '" . $new_status . "'", 'Project Task', $task_id);
    http_response_code(200);
    echo json_encode(['status' => 'success', 'message' => 'Task status updated.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database update failed.']);
}

$stmt->close();
$conn->close();
?>