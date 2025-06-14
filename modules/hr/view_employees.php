<?php
$page_title = "Manage Employees";
include('../../includes/header.php');

if (!has_permission('hr_manage') && !has_permission('hr_view_department')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

$sql = "SELECT e.*, d.department_name 
        FROM employees e
        JOIN departments d ON e.department_id = d.id 
        ORDER BY e.is_active DESC, e.last_name ASC";
$result = $conn->query($sql);

$can_manage_hr = has_permission('hr_manage');
$can_view_payroll = has_permission('payroll_view');
// Check for the highest level permission for permanent delete
$is_super_admin = ($_SESSION['role_name'] == 'System Admin');
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">HR</li>
    <li class="breadcrumb-item active" aria-current="page">Manage Employees</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php if ($can_manage_hr): ?>
        <a href="add_employee.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add New Employee</a>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-header"><h5>All Employees</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover data-table">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th><th>Job Title</th><th>Department</th><th>Status</th>
                        <?php if ($can_view_payroll): ?><th class="text-end">Salary</th><?php endif; ?>
                        <?php if ($can_manage_hr): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="<?php echo $row['is_active'] ? '' : 'table-secondary text-muted'; ?>">
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                <td><?php echo $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?></td>
                                <?php if ($can_view_payroll): ?><td class="text-end">$<?php echo number_format($row['salary'], 2); ?></td><?php endif; ?>
                                <?php if ($can_manage_hr): ?>
                                    <td class="d-flex">
                                        <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning me-1" title="Edit"><i class="bi bi-pencil-square"></i></a>
                                        <form action="handle_toggle_employee_status.php" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to change this employee\'s status?');">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <?php if ($row['is_active']): ?>
                                                <button type="submit" class="btn btn-sm btn-secondary" title="Deactivate Employee"><i class="bi bi-pause-circle-fill"></i></button>
                                            <?php else: ?>
                                                 <button type="submit" class="btn btn-sm btn-success" title="Activate Employee"><i class="bi bi-play-circle-fill"></i></button>
                                            <?php endif; ?>
                                        </form>
                                        <?php if ($is_super_admin): ?>
                                        <button type="button" class="btn btn-sm btn-danger ms-1" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal" data-id="<?php echo $row['id']; ?>" title="Permanently Delete"><i class="bi bi-trash"></i></button>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white"><h5 class="modal-title">Confirm Permanent Deletion</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">
        <p class="h5">Are you sure you want to permanently delete this employee record?</p>
        <p class="text-danger"><strong>Warning:</strong> This action cannot be undone and will permanently remove the employee from the system.</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="handle_delete_employee.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="delete_employee_id">
            <button type="submit" class="btn btn-danger">Yes, Permanently Delete</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>