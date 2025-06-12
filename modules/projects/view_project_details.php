<?php
$page_title = "Project Details";
include('../../includes/header.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Project ID."); }
$project_id = $_GET['id'];

if (!has_permission('project_full_access') && !has_permission('project_create') && !has_permission('project_my_tasks_view')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch project details
$sql_project = "SELECT p.*, u.username as manager_name, b.budget_name FROM projects p LEFT JOIN users u ON p.manager_id = u.id LEFT JOIN budgets b ON p.budget_id = b.id WHERE p.id = ?";
$stmt_project = $conn->prepare($sql_project);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$project = $stmt_project->get_result()->fetch_assoc();
if (!$project) { die("Project not found."); }

// Fetch tasks for this project
$sql_tasks = "SELECT t.*, u.username as assigned_to_name FROM project_tasks t LEFT JOIN users u ON t.assigned_to_user_id = u.id WHERE t.project_id = ? ORDER BY FIELD(t.status, 'To Do', 'In Progress', 'Blocked', 'Done'), t.due_date ASC";
$stmt_tasks = $conn->prepare($sql_tasks);
$stmt_tasks->bind_param("i", $project_id);
$stmt_tasks->execute();
$tasks_result = $stmt_tasks->get_result();

// Fetch users for the 'assign task' dropdown
$sql_users = "SELECT id, username FROM users ORDER BY username ASC";
$users_result = $conn->query($sql_users);

$task_statuses = ['To Do', 'In Progress', 'Done', 'Blocked'];
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_projects.php">Projects</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($project['project_name']); ?></li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo htmlspecialchars($project['project_name']); ?></h1>
    </div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5>Task Management</h5></div>
            <div class="card-body">
                <h6>Existing Tasks</h6>
                <ul class="list-group mb-4">
                    <?php if ($tasks_result->num_rows > 0): ?>
                        <?php while($task = $tasks_result->fetch_assoc()): 
                            $is_assigned_user = ($task['assigned_to_user_id'] == $_SESSION['user_id']);
                            $can_update_task = has_permission('project_full_access') || $is_assigned_user;
                        ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <?php echo htmlspecialchars($task['task_name']); ?>
                                    <div class="small text-muted">
                                        Due: <?php echo $task['due_date'] ? date("d M Y", strtotime($task['due_date'])) : 'N/A'; ?> | 
                                        Assigned to: <?php echo htmlspecialchars($task['assigned_to_name'] ?? 'Unassigned'); ?>
                                    </div>
                                </div>
                                <div>
                                    <?php if ($can_update_task): ?>
                                        <select class="form-select form-select-sm task-status-select" data-task-id="<?php echo $task['id']; ?>">
                                            <?php foreach($task_statuses as $status): 
                                                $selected = ($status == $task['status']) ? 'selected' : '';
                                            ?>
                                                <option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo $status; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    <?php else: ?>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($task['status']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="list-group-item text-muted">No tasks have been added to this project yet.</li>
                    <?php endif; ?>
                </ul>
                <hr>
                <?php if(has_permission('project_full_access') || has_permission('project_create')): ?>
                <h6>Add New Task</h6>
                <form action="handle_add_task.php" method="POST">
                    <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
                    <div class="row">
                        <div class="col-md-6 mb-3"><label for="task_name" class="form-label">Task Name</label><input type="text" class="form-control" name="task_name" required></div>
                        <div class="col-md-6 mb-3"><label for="assigned_to_user_id" class="form-label">Assign To</label><select class="form-select" name="assigned_to_user_id"><option value="">Unassigned</option><?php mysqli_data_seek($users_result, 0); while($user = $users_result->fetch_assoc()): ?><option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option><?php endwhile; ?></select></div>
                    </div>
                     <div class="row"><div class="col-md-6 mb-3"><label for="due_date" class="form-label">Due Date</label><input type="date" class="form-control" name="due_date"></div></div>
                    <button type="submit" class="btn btn-success">Add Task</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5>Project Summary</h5></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Status:</strong> <span class="badge bg-warning text-dark"><?php echo htmlspecialchars($project['status']); ?></span></li>
                <li class="list-group-item"><strong>Manager:</strong> <?php echo htmlspecialchars($project['manager_name'] ?? 'N/A'); ?></li>
                <li class="list-group-item"><strong>Department Budget:</strong> <?php echo htmlspecialchars($project['budget_name'] ?? 'N/A'); ?></li>
                <li class="list-group-item"><strong>Project Budget:</strong> $<?php echo $project['project_budget'] ? number_format($project['project_budget'], 2) : 'N/A'; ?></li>
                <li class="list-group-item"><strong>Start Date:</strong> <?php echo date("F j, Y", strtotime($project['start_date'])); ?></li>
                <li class="list-group-item"><strong>End Date:</strong> <?php echo $project['end_date'] ? date("F j, Y", strtotime($project['end_date'])) : 'Ongoing'; ?></li>
            </ul>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>