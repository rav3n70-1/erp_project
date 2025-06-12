<?php
include_once('../../includes/session_check.php');
include_once('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Project ID."); }
$project_id = $_GET['id'];
$conn = connect_db();

// 1. Fetch project details
$sql_project = "SELECT p.*, u.username as manager_name, b.budget_name 
                FROM projects p 
                LEFT JOIN users u ON p.manager_id = u.id 
                LEFT JOIN budgets b ON p.budget_id = b.id 
                WHERE p.id = ?";
$stmt_project = $conn->prepare($sql_project);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$project = $stmt_project->get_result()->fetch_assoc();
if (!$project) { die("Project not found."); }

// 2. Fetch tasks for this project
$sql_tasks = "SELECT t.*, u.username as assigned_to_name 
              FROM project_tasks t 
              LEFT JOIN users u ON t.assigned_to_user_id = u.id 
              WHERE t.project_id = ? ORDER BY t.due_date ASC";
$stmt_tasks = $conn->prepare($sql_tasks);
$stmt_tasks->bind_param("i", $project_id);
$stmt_tasks->execute();
$tasks_result = $stmt_tasks->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Project - <?php echo htmlspecialchars($project['project_name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; }
        .container { max-width: 960px; }
        .header { border-bottom: 2px solid #dee2e6; padding-bottom: 1rem; margin-bottom: 2rem; }
        .section { margin-top: 2rem; page-break-inside: avoid; }
        @media print {
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="header d-flex justify-content-between align-items-center">
            <h1>Project Summary</h1>
            <h4 class="text-muted"><?php echo htmlspecialchars($project['project_name']); ?></h4>
        </div>

        <div class="row mb-4">
            <div class="col-8">
                <h5>Description</h5>
                <p><?php echo nl2br(htmlspecialchars($project['description'])); ?></p>
            </div>
            <div class="col-4 text-end">
                <strong>Start Date:</strong> <?php echo date("F j, Y", strtotime($project['start_date'])); ?><br>
                <strong>End Date:</strong> <?php echo $project['end_date'] ? date("F j, Y", strtotime($project['end_date'])) : 'Ongoing'; ?><br>
                <strong>Status:</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($project['status']); ?></span><br>
                <strong>Manager:</strong> <?php echo htmlspecialchars($project['manager_name'] ?? 'N/A'); ?><br>
                <strong>Budget:</strong> <?php echo htmlspecialchars($project['budget_name'] ?? 'N/A'); ?><br>
                <strong>Project Budget:</strong> $<?php echo $project['project_budget'] ? number_format($project['project_budget'], 2) : 'N/A'; ?>
            </div>
        </div>

        <div class="section">
            <h5>Project Tasks</h5>
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Task Name</th>
                        <th>Assigned To</th>
                        <th>Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tasks_result->num_rows > 0): ?>
                        <?php while($task = $tasks_result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                <td><?php echo htmlspecialchars($task['assigned_to_name'] ?? 'Unassigned'); ?></td>
                                <td><?php echo $task['due_date'] ? date("d M, Y", strtotime($task['due_date'])) : 'N/A'; ?></td>
                                <td><?php echo htmlspecialchars($task['status']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                         <tr><td colspan="4" class="text-center text-muted">No tasks found for this project.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-5 no-print">
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Print this page</button>
            <a href="view_project_details.php?id=<?php echo $project_id; ?>" class="btn btn-secondary">Back to Project Details</a>
        </div>
    </div>
</body>
</html>