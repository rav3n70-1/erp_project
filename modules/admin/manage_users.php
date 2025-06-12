<?php
$page_title = "Manage Users";
include('../../includes/header.php');
if (!has_permission('user_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
include('../../includes/db.php');
$conn = connect_db();
$sql_roles = "SELECT id, role_name FROM roles ORDER BY role_name ASC";
$roles_result = $conn->query($sql_roles);
$sql_users = "SELECT u.id, u.username, u.email, r.role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.username ASC";
$users_result = $conn->query($sql_users);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active" aria-current="page">Manage Users</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<?php
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    $status_map = [
        'success' => ['msg' => 'User created successfully!', 'type' => 'success'],
        'updated' => ['msg' => 'User updated successfully!', 'type' => 'success'],
        'deleted' => ['msg' => 'User has been deleted.', 'type' => 'warning'],
        'error_exists' => ['msg' => 'Error: Username or email already exists.', 'type' => 'danger'],
        'error_password_mismatch' => ['msg' => 'Error: Passwords do not match.', 'type' => 'danger'],
        'error_self_delete' => ['msg' => 'Error: You cannot delete your own account.', 'type' => 'danger'],
        'error' => ['msg' => 'An error occurred.', 'type' => 'danger'],
    ];
    if (array_key_exists($_GET['status'], $status_map)) {
        $message = $status_map[$_GET['status']]['msg'];
        $alert_type = $status_map[$_GET['status']]['type'];
    }
    if ($message) { echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'; }
}
?>

<div class="row">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h5>Create New User</h5></div>
            <div class="card-body">
                <form action="handle_add_user.php" method="POST">
                    <div class="mb-3"><label for="username" class="form-label">Username <span class="text-danger">*</span></label><input type="text" class="form-control" id="username" name="username" required></div>
                    <div class="mb-3"><label for="email" class="form-label">Email Address <span class="text-danger">*</span></label><input type="email" class="form-control" id="email" name="email" required></div>
                    <div class="mb-3"><label for="password" class="form-label">Password <span class="text-danger">*</span></label><input type="password" class="form-control" id="password" name="password" required></div>
                    <div class="mb-3"><label for="role_id" class="form-label">Role <span class="text-danger">*</span></label><select class="form-select" id="role_id" name="role_id" required><option value="">Select a role</option><?php mysqli_data_seek($roles_result, 0); while($role = $roles_result->fetch_assoc()): ?><option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['role_name']); ?></option><?php endwhile; ?></select></div>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h5>Existing Users</h5></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead><tr><th>Username</th><th>Email</th><th>Role</th><th>Actions</th></tr></thead>
                        <tbody>
                            <?php if ($users_result->num_rows > 0): ?>
                                <?php while($user = $users_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                                    <td><span class="badge bg-primary"><?php echo htmlspecialchars($user['role_name']); ?></span></td>
                                    <td>
                                        <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal" data-id="<?php echo $user['id']; ?>"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">No users found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Confirm Deletion</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body">Are you sure you want to delete this user? <p class="text-danger small">This action cannot be undone.</p></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form action="handle_delete_user.php" method="POST" class="d-inline">
            <input type="hidden" name="id" id="delete_user_id">
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