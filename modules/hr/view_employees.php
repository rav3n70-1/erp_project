<?php
$page_title = "Manage Employees";
include('../../includes/header.php');

if (!has_permission('hr_manage') && !has_permission('hr_view_department') && !has_permission('payroll_view')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

$sql = "SELECT e.*, d.department_name FROM employees e JOIN departments d ON e.department_id = d.id ORDER BY e.last_name ASC";
$result = $conn->query($sql);

$can_manage_hr = has_permission('hr_manage');
$can_view_payroll = has_permission('payroll_view');
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

<?php 
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'success') { $message = 'Employee added successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'updated') { $message = 'Employee updated successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'deleted') { $message = 'Employee has been deleted.'; $alert_type = 'warning'; }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
    }
}
?>

<div class="card">
    <div class="card-header"><h5>All Employees</h5></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Name</th><th>Job Title</th><th>Department</th><th>Email</th><th>Phone</th>
                        <?php if ($can_view_payroll): ?><th class="text-end">Salary</th><?php endif; ?>
                        <?php if ($can_manage_hr): ?><th>Actions</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($row['department_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['phone_number']); ?></td>
                                <?php if ($can_view_payroll): ?><td class="text-end">$<?php echo number_format($row['salary'], 2); ?></td><?php endif; ?>
                                <?php if ($can_manage_hr): ?>
                                    <td>
                                        <a href="edit_employee.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteEmployeeModal" data-id="<?php echo $row['id']; ?>"><i class="bi bi-trash"></i></button>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?php echo $can_view_payroll ? ($can_manage_hr ? 7 : 6) : ($can_manage_hr ? 6 : 5); ?>" class="text-center text-muted">No employees found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteEmployeeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Are you sure you want to delete this employee record? <p class="text-danger small">This action cannot be undone.</p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="handle_delete_employee.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="delete_employee_id">
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