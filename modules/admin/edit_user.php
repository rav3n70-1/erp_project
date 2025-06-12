<?php
$page_title = "Edit User";
include('../../includes/header.php');

if (!has_permission('user_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid User ID."); }

$user_id = $_GET['id'];
$conn = connect_db();

// Fetch existing user data
$sql_user = "SELECT id, username, email, role_id FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
if ($result_user->num_rows === 0) { die("User not found."); }
$user = $result_user->fetch_assoc();

// Fetch all roles for the dropdown
$sql_roles = "SELECT id, role_name FROM roles ORDER BY role_name ASC";
$roles_result = $conn->query($sql_roles);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="manage_users.php">Manage Users</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit User</li>
  </ol>
</nav>

<h1 class="mt-4">Edit User: <?php echo htmlspecialchars($user['username']); ?></h1>

<div class="card">
    <div class="card-body">
        <form action="handle_edit_user.php" method="POST">
            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="role_id" class="form-label">Role <span class="text-danger">*</span></label>
                <select class="form-select" id="role_id" name="role_id" required>
                    <?php while($role = $roles_result->fetch_assoc()): 
                        $selected = ($role['id'] == $user['role_id']) ? 'selected' : '';
                    ?>
                        <option value="<?php echo $role['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($role['role_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <hr>
            <p class="text-muted">Only fill out the password fields if you want to change the user's password.</p>
            <div class="mb-3">
                <label for="password" class="form-label">New Password</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="manage_users.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>