<?php
$page_title = "Audit Log";
include('../../includes/header.php'); // This will automatically check for login and include permissions

// This entire page is for Admins only.
if (!has_permission('Admin')) {
    // Redirect non-admins to the dashboard.
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch all audit log entries, joining with the users table to get the username
$sql = "SELECT 
            al.log_timestamp,
            al.action,
            al.target_type,
            al.target_id,
            u.username
        FROM audit_log al
        JOIN users u ON al.user_id = u.id
        ORDER BY al.log_timestamp DESC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item">Administration</li>
    <li class="breadcrumb-item active" aria-current="page">Audit Log</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>
<p class="lead">A record of all key activities performed by users in the system.</p>

<div class="card">
    <div class="card-header">
        <h5>Activity History</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Target</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo date("d M, Y, g:i:s A", strtotime($row['log_timestamp'])); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['action']); ?></td>
                                <td><?php echo htmlspecialchars($row['target_type']) . ' #' . $row['target_id']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted">No activities have been logged yet.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>