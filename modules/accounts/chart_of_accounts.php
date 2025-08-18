<?php
$page_title = "Chart of Accounts";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$result = $conn->query("SELECT * FROM chart_of_accounts ORDER BY account_code ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <a href="/erp_project/modules/accounts/add_account.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Account</a>
  <?php endif; ?>
</div>

<?php if (isset($_GET['deleted'])): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  Account deleted successfully.
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (isset($_GET['in_use'])): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  Cannot delete account: it is referenced by journal entries.
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if (isset($_GET['has_children'])): ?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  Cannot delete account: it has child accounts. Reassign or delete children first.
  <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
  <div class="card-header"><h5>Accounts</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr><th>Code</th><th>Name</th><th>Type</th><th>Status</th><th>Created</th><?php if (has_permission('budget_manage')): ?><th>Actions</th><?php endif; ?></tr>
        </thead>
        <tbody>
          <?php if ($result && $result->num_rows): while($row = $result->fetch_assoc()): ?>
            <tr>
              <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($row['account_code']); ?></span></td>
              <td><?php echo htmlspecialchars($row['account_name']); ?></td>
              <td><?php echo htmlspecialchars($row['account_type']); ?></td>
              <td><?php echo $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
              <td><?php echo htmlspecialchars($row['created_at']); ?></td>
              <?php if (has_permission('budget_manage')): ?>
              <td>
                <a href="/erp_project/modules/accounts/edit_account.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
                <a href="/erp_project/modules/accounts/handle_delete_account.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this account? This cannot be undone.');"><i class="bi bi-trash"></i></a>
              </td>
              <?php endif; ?>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td><div class="empty-state"><i class="bi bi-inboxes"></i><div class="mt-2">No accounts yet.</div></div></td>
              <td></td>
              <td></td>
              <td></td>
              <td></td>
              <?php if (has_permission('budget_manage')): ?><td></td><?php endif; ?>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include('../../includes/footer.php'); ?> 