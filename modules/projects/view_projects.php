<?php
$page_title = "Manage Projects";
// We must include the session and permission helpers first
include('../../includes/session_check.php');
include('../../includes/permissions.php');

// STEP 1: Check for permission BEFORE any HTML is printed.
if (!has_permission('project_full_access') && !has_permission('project_create')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

// STEP 2: If permission check passes, THEN include the header to draw the page.
include('../../includes/header.php');
include('../../includes/db.php');
$conn = connect_db();

$sql = "SELECT 
            p.*, 
            u.username as manager_name,
            b.budget_name
        FROM projects p
        LEFT JOIN users u ON p.manager_id = u.id
        LEFT JOIN budgets b ON p.budget_id = b.id
        ORDER BY FIELD(p.status, 'Pending Approval', 'In Progress', 'On Hold', 'Not Started', 'Completed', 'Canceled', 'Rejected'), p.start_date DESC";
$result = $conn->query($sql);

$can_create = has_permission('project_create');
$can_manage_full = has_permission('project_full_access');
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Projects</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if ($can_create): ?>
        <a href="add_project.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Create New Project</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header"><h5>All Projects</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead class="table-dark">
                    <tr>
                        <th>Project Name</th>
                        <th>Manager</th>
                        <th>Dates</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['project_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['manager_name'] ?? 'N/A'); ?></td>
                                <td><?php echo date("d M Y", strtotime($row['start_date'])) . " - " . ($row['end_date'] ? date("d M Y", strtotime($row['end_date'])) : 'Ongoing'); ?></td>
                                <td>
                                    <?php
                                        // --- THIS IS THE NEW LOGIC FOR DYNAMIC COLORS ---
                                        $status = htmlspecialchars($row['status']);
                                        $badge_class = '';
                                        switch ($status) {
                                            case 'Approved':
                                                $badge_class = 'bg-success-subtle text-success-emphasis'; // Light Green
                                                break;
                                            case 'In Progress':
                                                $badge_class = 'bg-primary-subtle text-primary-emphasis'; // Sky Blue
                                                break;
                                            case 'On Hold':
                                                $badge_class = 'bg-warning-subtle text-warning-emphasis'; // Amber/Yellow
                                                break;
                                            case 'Completed':
                                                $badge_class = 'bg-success-subtle text-success-emphasis'; // Light Green
                                                break;
                                            case 'Canceled':
                                                $badge_class = 'bg-secondary-subtle text-secondary-emphasis'; // Soft Gray
                                                break;
                                            case 'Rejected':
                                                $badge_class = 'bg-danger-subtle text-danger-emphasis'; // Soft Red
                                                break;
                                            default: // For 'Not Started' and 'Pending Approval'
                                                $badge_class = 'bg-light text-dark'; // Light Gray
                                                break;
                                        }
                                        echo '<span class="badge ' . $badge_class . '">' . $status . '</span>';
                                    ?>
                                </td>
                                <td>
                                    <a href="view_project_details.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info" title="View Details & Tasks"><i class="bi bi-kanban"></i></a>
                                    <?php if ($can_create || $can_manage_full): ?>
                                    <a href="edit_project.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning" title="Edit Project"><i class="bi bi-pencil-square"></i></a>
                                    <?php endif; ?>
                                    <?php if ($can_manage_full): ?>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProjectModal" data-id="<?php echo $row['id']; ?>" title="Delete Project"><i class="bi bi-trash"></i></button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted">No projects found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteProjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Are you sure you want to delete this project? <p class="text-danger small">This will also delete all tasks associated with it. This action cannot be undone.</p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="handle_delete_project.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="delete_project_id">
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>