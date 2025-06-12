<?php
$page_title = "Manage Budgets";
include('../../includes/header.php');
include('../../includes/db.php');

// We need to check if the user can either manage budgets OR view finance in general
if (!has_permission('budget_manage') && !has_permission('finance_view')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

$conn = connect_db();

// Fetch all departments for the 'Add Budget' form dropdown
$sql_departments = "SELECT id, department_name FROM departments ORDER BY department_name ASC";
$departments_result = $conn->query($sql_departments);

// CORRECTED QUERY: Now only sums POs that are Approved/Partial/Completed and Projects that are Approved
$sql_budgets = "SELECT 
                    b.id, b.budget_name, b.allocated_amount, b.start_date, b.end_date, d.department_name,
                    (
                        (SELECT COALESCE(SUM(po.total_amount), 0) FROM purchase_orders po WHERE po.budget_id = b.id AND po.status IN ('Approved', 'Partially Delivered', 'Completed'))
                        +
                        (SELECT COALESCE(SUM(p.project_budget), 0) FROM projects p WHERE p.budget_id = b.id AND p.status = 'Approved')
                    ) as spent_amount
                FROM budgets b
                JOIN departments d ON b.department_id = d.id
                ORDER BY b.start_date DESC";
$budgets_result = $conn->query($sql_budgets);

// We define this variable once to use in multiple places
$can_manage_budget = has_permission('budget_manage');
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Finance</li>
    <li class="breadcrumb-item active" aria-current="page">Manage Budgets</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'success') { $message = 'New budget added successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'updated') { $message = 'Budget updated successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'deleted') { $message = 'Budget has been deleted.'; $alert_type = 'warning'; }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>';
    }
}
?>

<div class="row">
    <?php if ($can_manage_budget): ?>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5>Add New Budget</h5></div>
            <div class="card-body">
                <form action="handle_add_budget.php" method="POST">
                    <div class="mb-3"><label for="budget_name" class="form-label">Budget Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="budget_name" name="budget_name" placeholder="e.g., Q4 Marketing Campaign" required></div>
                    <div class="mb-3"><label for="department_id" class="form-label">Department <span class="text-danger">*</span></label><select class="form-select" id="department_id" name="department_id" required><option value="">Select Department</option><?php mysqli_data_seek($departments_result, 0); while($dept = $departments_result->fetch_assoc()): ?><option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option><?php endwhile; ?></select></div>
                    <div class="mb-3"><label for="allocated_amount" class="form-label">Allocated Amount ($) <span class="text-danger">*</span></label><input type="number" class="form-control" id="allocated_amount" name="allocated_amount" step="0.01" min="0" required></div>
                    <div class="row"><div class="col-md-6 mb-3"><label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label><input type="date" class="form-control" id="start_date" name="start_date" required></div><div class="col-md-6 mb-3"><label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label><input type="date" class="form-control" id="end_date" name="end_date" required></div></div>
                    <button type="submit" class="btn btn-primary">Save Budget</button>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="<?php echo $can_manage_budget ? 'col-lg-8' : 'col-lg-12'; ?>">
        <div class="card">
            <div class="card-header"><h5>Existing Budgets</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Budget Name</th><th>Department</th><th>Period</th><th>Spending</th><?php if ($can_manage_budget): ?><th>Actions</th><?php endif; ?></tr></thead>
                        <tbody>
                            <?php if ($budgets_result->num_rows > 0): ?>
                                <?php while($b = $budgets_result->fetch_assoc()): 
                                    $remaining = $b['allocated_amount'] - $b['spent_amount'];
                                    $percent_spent = ($b['allocated_amount'] > 0) ? ($b['spent_amount'] / $b['allocated_amount']) * 100 : 0;
                                    $progress_bar_class = 'bg-success';
                                    $tooltip_attributes = '';
                                    if ($percent_spent > 80 && $percent_spent < 100) { $progress_bar_class = 'bg-warning'; } 
                                    elseif ($percent_spent >= 100) { $progress_bar_class = 'bg-danger'; $tooltip_attributes = 'data-bs-toggle="tooltip" data-bs-placement="top" title="Budget Exceeded!"'; }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($b['budget_name']); ?></td>
                                    <td><?php echo htmlspecialchars($b['department_name']); ?></td>
                                    <td><?php echo date("d M Y", strtotime($b['start_date'])) . " - " . date("d M Y", strtotime($b['end_date'])); ?></td>
                                    <td>
                                        <div class="d-flex justify-content-between"><span>$<?php echo number_format($b['spent_amount'], 2); ?></span><span class="text-muted">$<?php echo number_format($b['allocated_amount'], 2); ?></span></div>
                                        <div class="progress mt-1" style="height: 8px;" <?php echo $tooltip_attributes; ?>><div class="progress-bar <?php echo $progress_bar_class; ?>" role="progressbar" style="width: <?php echo min($percent_spent, 100); ?>%;"></div></div>
                                    </td>
                                    <?php if ($can_manage_budget): ?>
                                    <td>
                                        <a href="edit_budget.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteBudgetModal" data-id="<?php echo $b['id']; ?>"><i class="bi bi-trash"></i></button>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="<?php echo $can_manage_budget ? '5' : '4'; ?>" class="text-center text-muted">No budgets found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteBudgetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Are you sure you want to delete this budget? <p class="text-warning small">This will not delete any POs, but will unlink them from this budget.</p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="handle_delete_budget.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="delete_budget_id">
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