<?php
$page_title = "Create New Project";
include('../../includes/header.php');
if (!has_permission('project_create')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
include('../../includes/db.php');
$conn = connect_db();

// Fetch users who can be project managers
$sql_managers = "SELECT u.id, u.username FROM users u JOIN roles r ON u.role_id = r.id WHERE r.role_name IN ('Project Manager', 'Department Manager', 'System Admin')";
$managers_result = $conn->query($sql_managers);

// --- THIS IS THE CORRECTED QUERY ---
// It now calculates the total spent amount (from both POs and other Projects)
// and uses a HAVING clause to only return budgets where the spending is LESS than the allocation.
$sql_budgets = "SELECT 
                    b.id, b.budget_name, b.allocated_amount,
                    (
                        (SELECT COALESCE(SUM(po.total_amount), 0) FROM purchase_orders po WHERE po.budget_id = b.id AND po.status != 'Rejected')
                        +
                        (SELECT COALESCE(SUM(p.project_budget), 0) FROM projects p WHERE p.budget_id = b.id)
                    ) as spent_amount
                FROM budgets b
                WHERE CURDATE() BETWEEN start_date AND end_date
                HAVING spent_amount < b.allocated_amount
                ORDER BY budget_name ASC";
$budgets_result = $conn->query($sql_budgets);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_projects.php">Projects</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create Project</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card">
    <div class="card-header"><h5>Project Details</h5></div>
    <div class="card-body">
        <form action="handle_add_project.php" method="POST" id="project-form">
            <div class="mb-3"><label for="project_name" class="form-label">Project Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="project_name" name="project_name" required></div>
            <div class="mb-3"><label for="description" class="form-label">Description</label><textarea class="form-control" id="description" name="description" rows="3"></textarea></div>
            <div class="row">
                <div class="col-md-6 mb-3"><label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label><input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo date('Y-m-d'); ?>" required></div>
                <div class="col-md-6 mb-3"><label for="end_date" class="form-label">End Date</label><input type="date" class="form-control" id="end_date" name="end_date"></div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="budget_id" class="form-label">Link to Department Budget (Optional)</label>
                    <select class="form-select" id="budget_id" name="budget_id">
                        <option value="" data-remaining="0">None</option>
                        <?php while($budget = $budgets_result->fetch_assoc()): 
                            $remaining = $budget['allocated_amount'] - $budget['spent_amount'];
                        ?>
                            <option value="<?php echo $budget['id']; ?>" data-remaining="<?php echo $remaining; ?>">
                                <?php echo htmlspecialchars($budget['budget_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="budget-info" class="form-text" style="display: none;">
                        Remaining in this budget: <strong id="remaining-budget-amount" class="text-success"></strong>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="project_budget" class="form-label">Amount for this Project ($)</label>
                    <input type="number" class="form-control" id="project_budget" name="project_budget" step="0.01" min="0">
                </div>
            </div>
             <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="manager_id" class="form-label">Project Manager</label>
                    <select class="form-select" id="manager_id" name="manager_id">
                        <option value="">Select a manager</option>
                        <?php while($user = $managers_result->fetch_assoc()): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Save Project</button>
            <a href="view_projects.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>
<?php
$conn->close();
include('../../includes/footer.php');
?>
<script>
// The JavaScript does not need to change, as it reads the data from the options that are rendered by PHP.
document.addEventListener('DOMContentLoaded', function() {
    const budgetSelect = document.getElementById('budget_id');
    const budgetInfoDiv = document.getElementById('budget-info');
    const remainingBudgetElement = document.getElementById('remaining-budget-amount');
    const projectBudgetInput = document.getElementById('project_budget');
    const projectForm = document.getElementById('project-form');

    function updateRemainingDisplay() {
        const selectedOption = budgetSelect.options[budgetSelect.selectedIndex];
        if (!selectedOption || !selectedOption.value) {
            budgetInfoDiv.style.display = 'none';
            return;
        }

        const originalRemaining = parseFloat(selectedOption.dataset.remaining);
        const projectBudget = parseFloat(projectBudgetInput.value) || 0;
        const newRemaining = originalRemaining - projectBudget;

        remainingBudgetElement.textContent = '$' + newRemaining.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        if (newRemaining < 0) {
            remainingBudgetElement.classList.remove('text-success');
            remainingBudgetElement.classList.add('text-danger');
        } else {
            remainingBudgetElement.classList.remove('text-danger');
            remainingBudgetElement.classList.add('text-success');
        }
        
        budgetInfoDiv.style.display = 'block';
    }

    budgetSelect.addEventListener('change', updateRemainingDisplay);
    projectBudgetInput.addEventListener('input', updateRemainingDisplay);

    projectForm.addEventListener('submit', function(e) {
        const selectedOption = budgetSelect.options[budgetSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const remaining = parseFloat(selectedOption.dataset.remaining);
            const projectBudget = parseFloat(projectBudgetInput.value) || 0;

            if (projectBudget > remaining) {
                e.preventDefault(); 
                alert('Error: The Project Budget amount cannot be greater than the remaining funds in the selected Department Budget.');
            }
        }
    });
});
</script>