<?php
$page_title = "Edit Project";
include('../../includes/header.php');

if (!has_permission('project_full_access') && !has_permission('project_create')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Project ID."); }
$project_id = $_GET['id'];
$conn = connect_db();

// Fetch existing project data
$sql_project = "SELECT * FROM projects WHERE id = ?";
$stmt_project = $conn->prepare($sql_project);
$stmt_project->bind_param("i", $project_id);
$stmt_project->execute();
$result_project = $stmt_project->get_result();
if ($result_project->num_rows === 0) { die("Project not found."); }
$project = $result_project->fetch_assoc();

// Fetch managers and budgets for dropdowns
$sql_managers = "SELECT u.id, u.username FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name IN ('Project Manager', 'Department Manager', 'System Admin')";
$managers_result = $conn->query($sql_managers);
$sql_budgets = "SELECT id, budget_name FROM budgets ORDER BY budget_name ASC";
$budgets_result = $conn->query($sql_budgets);
$project_statuses = ['Not Started', 'In Progress', 'On Hold', 'Completed', 'Canceled'];
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_projects.php">Projects</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Project</li>
  </ol>
</nav>

<h1 class="mt-4">Edit Project: <?php echo htmlspecialchars($project['project_name']); ?></h1>

<div class="card">
    <div class="card-header"><h5>Project Details</h5></div>
    <div class="card-body">
        <form action="handle_edit_project.php" method="POST">
            <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
            <div class="mb-3"><label for="project_name" class="form-label">Project Name</label><input type="text" class="form-control" id="project_name" name="project_name" value="<?php echo htmlspecialchars($project['project_name']); ?>" required></div>
            <div class="mb-3"><label for="description" class="form-label">Description</label><textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($project['description']); ?></textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label for="start_date" class="form-label">Start Date</label><input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($project['start_date']); ?>" required></div>
                <div class="col-md-6 mb-3"><label for="end_date" class="form-label">End Date</label><input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($project['end_date']); ?>"></div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3"><label for="manager_id" class="form-label">Project Manager</label><select class="form-select" id="manager_id" name="manager_id"><?php while($user = $managers_result->fetch_assoc()): $selected = ($user['id'] == $project['manager_id']) ? 'selected' : ''; ?><option value="<?php echo $user['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($user['username']); ?></option><?php endwhile; ?></select></div>
                <div class="col-md-4 mb-3"><label for="budget_id" class="form-label">Link to Budget</label><select class="form-select" id="budget_id" name="budget_id"><option value="">None</option><?php while($budget = $budgets_result->fetch_assoc()): $selected = ($budget['id'] == $project['budget_id']) ? 'selected' : ''; ?><option value="<?php echo $budget['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($budget['budget_name']); ?></option><?php endwhile; ?></select></div>
                <div class="col-md-4 mb-3"><label for="status" class="form-label">Status</label><select class="form-select" id="status" name="status" required><?php foreach($project_statuses as $status): $selected = ($status == $project['status']) ? 'selected' : ''; ?><option value="<?php echo $status; ?>" <?php echo $selected; ?>><?php echo $status; ?></option><?php endforeach; ?></select></div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="view_projects.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>