<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('project_create')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_projects.php');
    exit();
}

$required_fields = ['project_name', 'start_date'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: add_project.php?status=error_missing');
        exit();
    }
}

$project_name = $_POST['project_name'];
$description = $_POST['description'] ?? NULL;
$start_date = $_POST['start_date'];
$end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : NULL;
$project_budget = !empty($_POST['project_budget']) ? $_POST['project_budget'] : NULL;
$manager_id = !empty($_POST['manager_id']) ? $_POST['manager_id'] : NULL;
$budget_id = !empty($_POST['budget_id']) ? $_POST['budget_id'] : NULL;
$client_id = !empty($_POST['client_id']) ? $_POST['client_id'] : NULL;

$conn = connect_db();

// --- Server-side Budget Validation is unchanged ---
if ($budget_id && $project_budget) {
    $sql_budget = "SELECT (allocated_amount - (SELECT COALESCE(SUM(total_amount), 0) FROM purchase_orders WHERE budget_id = ?)) as remaining FROM budgets WHERE id = ?";
    $stmt_budget = $conn->prepare($sql_budget);
    $stmt_budget->bind_param("ii", $budget_id, $budget_id);
    $stmt_budget->execute();
    $remaining_budget = $stmt_budget->get_result()->fetch_assoc()['remaining'];

    if ($project_budget > $remaining_budget) {
        header("Location: add_project.php?status=error_budget_exceeded");
        exit();
    }
}

// CORRECTED: The 'status' column is removed from the INSERT statement.
// This forces the database to use the "Pending Approval" default value we set.
$sql = "INSERT INTO projects (project_name, description, start_date, end_date, project_budget, budget_id, manager_id, client_id) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssdiis", $project_name, $description, $start_date, $end_date, $project_budget, $budget_id, $manager_id, $client_id);

if ($stmt->execute()) {
    $project_id = $conn->insert_id;
    log_audit_trail($conn, "Created new project (Awaiting Approval): " . $project_name, 'Project', $project_id);

    // --- Notification logic is unchanged ---
    $sql_users = "SELECT DISTINCT u.id FROM users u LEFT JOIN role_permissions rp ON u.role_id = rp.role_id LEFT JOIN roles r ON u.role_id = r.id WHERE rp.permission_key = 'project_approve' OR r.role_name = 'System Admin'";
    $users_result = $conn->query($sql_users);
    
    if ($users_result && $users_result->num_rows > 0) {
        $notification_message = "New project '".htmlspecialchars($project_name)."' requires approval.";
        $notification_link = "/erp_project/modules/projects/view_project_details.php?id=" . $project_id;
        
        $sql_notification = "INSERT INTO notifications (user_id, message, link) VALUES (?, ?, ?)";
        $stmt_notification = $conn->prepare($sql_notification);
        while ($user = $users_result->fetch_assoc()) {
            $stmt_notification->bind_param("iss", $user['id'], $notification_message, $notification_link);
            $stmt_notification->execute();
        }
    }
    
    header("Location: view_projects.php?status=success");
} else {
    header("Location: add_project.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>