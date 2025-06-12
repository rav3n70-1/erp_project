<?php
$page_title = "My Profile & Settings";
include('../../includes/header.php');
include('../../includes/db.php');

$current_user_id = $_SESSION['user_id'];
$conn = connect_db();

// Fetch user's login and role info
$sql_user = "SELECT u.username, u.email, r.role_name 
             FROM users u
             JOIN roles r ON u.role_id = r.id
             WHERE u.id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $current_user_id);
$stmt_user->execute();
$user = $stmt_user->get_result()->fetch_assoc();

// Fetch linked employee info, if it exists
$sql_employee = "SELECT * FROM employees WHERE user_id = ?";
$stmt_employee = $conn->prepare($sql_employee);
$stmt_employee->bind_param("i", $current_user_id);
$stmt_employee->execute();
$employee = $stmt_employee->get_result()->fetch_assoc();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">My Profile</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    $status_map = [
        'success_password' => ['msg' => 'Password changed successfully!', 'type' => 'success'],
        'error_mismatch' => ['msg' => 'Error: New passwords do not match.', 'type' => 'danger'],
        'error_current_password' => ['msg' => 'Error: Your current password was incorrect.', 'type' => 'danger'],
        'error_missing' => ['msg' => 'Error: Please fill out all password fields.', 'type' => 'danger'],
    ];
    if (array_key_exists($_GET['status'], $status_map)) {
        $message = $status_map[$_GET['status']]['msg'];
        $alert_type = $status_map[$_GET['status']]['type'];
    }
    if ($message) { echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
}
?>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="profileTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button">Profile Information</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-pane" type="button">Change Password</button>
            </li>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="profileTabContent">
            <div class="tab-pane fade show active" id="info-pane" role="tabpanel">
                <h4>Account Details</h4>
                <dl class="row">
                    <dt class="col-sm-3">Username</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($user['username']); ?></dd>
                    <dt class="col-sm-3">Email Address</dt>
                    <dd class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></dd>
                    <dt class="col-sm-3">Role</dt>
                    <dd class="col-sm-9"><span class="badge bg-primary"><?php echo htmlspecialchars($user['role_name']); ?></span></dd>
                </dl>
                <hr>
                <h4>Personal Information</h4>
                <?php if ($employee): ?>
                    <dl class="row">
                        <dt class="col-sm-3">Full Name</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?></dd>
                        <dt class="col-sm-3">Job Title</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['job_title']); ?></dd>
                         <dt class="col-sm-3">Phone Number</dt>
                        <dd class="col-sm-9"><?php echo htmlspecialchars($employee['phone_number']); ?></dd>
                    </dl>
                <?php else: ?>
                    <p class="text-muted">No personal employee record is linked to this user account.</p>
                <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="password-pane" role="tabpanel">
                <h4>Change Your Password</h4>
                <form action="handle_change_password.php" method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>