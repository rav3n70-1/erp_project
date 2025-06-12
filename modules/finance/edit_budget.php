<?php
$page_title = "Edit Budget";
include('../../includes/header.php');
include('../../includes/db.php');
if (!has_permission('Manager')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

// 1. Check for a valid Budget ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Budget ID."); }

$budget_id = $_GET['id'];
$conn = connect_db();

// 2. Fetch the budget's existing data
$sql_budget = "SELECT * FROM budgets WHERE id = ?";
$stmt_budget = $conn->prepare($sql_budget);
$stmt_budget->bind_param("i", $budget_id);
$stmt_budget->execute();
$budget_result = $stmt_budget->get_result();
if ($budget_result->num_rows === 0) { die("Budget not found."); }
$budget = $budget_result->fetch_assoc();

// 3. Fetch all departments for the dropdown
$sql_departments = "SELECT id, department_name FROM departments ORDER BY department_name ASC";
$departments_result = $conn->query($sql_departments);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="manage_budgets.php">Manage Budgets</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Budget</li>
  </ol>
</nav>

<h1 class="mt-4">Edit Budget</h1>

<div class="card">
    <div class="card-body">
        <form action="handle_edit_budget.php" method="POST">
            <input type="hidden" name="budget_id" value="<?php echo $budget['id']; ?>">
            <div class="mb-3">
                <label for="budget_name" class="form-label">Budget Name <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="budget_name" name="budget_name" value="<?php echo htmlspecialchars($budget['budget_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                <select class="form-select" id="department_id" name="department_id" required>
                    <?php while($dept = $departments_result->fetch_assoc()): 
                        $selected = ($dept['id'] == $budget['department_id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $dept['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($dept['department_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="allocated_amount" class="form-label">Allocated Amount ($) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="allocated_amount" name="allocated_amount" value="<?php echo htmlspecialchars($budget['allocated_amount']); ?>" step="0.01" min="0" required>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo htmlspecialchars($budget['start_date']); ?>" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo htmlspecialchars($budget['end_date']); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="manage_budgets.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>