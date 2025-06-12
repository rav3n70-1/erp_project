<?php
$page_title = "Add New Employee";
include('../../includes/header.php');

// This page is for HR Managers/Admins only
if (!has_permission('hr_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch departments for the dropdown
$sql_departments = "SELECT id, department_name FROM departments ORDER BY department_name ASC";
$departments_result = $conn->query($sql_departments);

// Fetch users who are not yet linked to an employee, for the login account dropdown
$sql_users = "SELECT u.id, u.username FROM users u LEFT JOIN employees e ON u.id = e.user_id WHERE e.id IS NULL";
$users_result = $conn->query($sql_users);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_employees.php">Manage Employees</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add Employee</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card">
    <div class="card-header">
        <h5>Employee Information</h5>
    </div>
    <div class="card-body">
        <form action="handle_add_employee.php" method="POST">
            <fieldset class="mb-3">
                <legend>Personal Details</legend>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                </div>
                <div class="row">
                     <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone_number" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number">
                    </div>
                </div>
            </fieldset>

            <fieldset class="mb-3">
                <legend>Employment Details</legend>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="job_title" class="form-label">Job Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="job_title" name="job_title" required>
                    </div>
                     <div class="col-md-6 mb-3">
                        <label for="department_id" class="form-label">Department <span class="text-danger">*</span></label>
                        <select class="form-select" id="department_id" name="department_id" required>
                            <option value="">Select a department</option>
                            <?php while($dept = $departments_result->fetch_assoc()): ?>
                                <option value="<?php echo $dept['id']; ?>"><?php echo htmlspecialchars($dept['department_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                 <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hire_date" class="form-label">Hire Date <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="hire_date" name="hire_date" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="salary" class="form-label">Salary ($)</label>
                        <input type="number" class="form-control" id="salary" name="salary" step="0.01" min="0">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>System Access</legend>
                 <div class="mb-3">
                    <label for="user_id" class="form-label">Link to User Account (Optional)</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">None</option>
                        <?php while($user = $users_result->fetch_assoc()): ?>
                            <option value="<?php echo $user['id']; ?>"><?php echo htmlspecialchars($user['username']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <div class="form-text">Link this employee record to their login account for self-service features later.</div>
                </div>
            </fieldset>

            <hr>
            <button type="submit" class="btn btn-primary">Save Employee</button>
            <a href="view_employees.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>