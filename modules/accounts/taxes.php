<?php
$page_title = "Taxes";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$res = $conn->query("SELECT * FROM tax_codes ORDER BY tax_code ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <a href="/erp_project/modules/accounts/add_tax_code.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Tax Code</a>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h5>Tax Codes</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr>
            <th>Code</th>
            <th>Description</th>
            <th class="text-end">Rate %</th>
            <th>Status</th>
            <th>Created</th>
            <?php if (has_permission('budget_manage')): ?>
            <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && $res->num_rows): while($row=$res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['tax_code']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td class="text-end"><?php echo number_format($row['rate'],2); ?></td>
            <td><?php echo $row['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>'; ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <?php if (has_permission('budget_manage')): ?>
            <td>
              <a href="#" class="btn btn-sm btn-warning disabled" title="Edit"><i class="bi bi-pencil-square"></i></a>
              <a href="#" class="btn btn-sm btn-<?php echo $row['is_active'] ? 'danger' : 'success'; ?> disabled" title="<?php echo $row['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                <i class="bi bi-<?php echo $row['is_active'] ? 'x-circle' : 'check-circle'; ?>"></i>
              </a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="<?php echo has_permission('budget_manage') ? '6' : '5'; ?>">
              <div class="empty-state">
                <i class="bi bi-percent"></i>
                <div class="mt-2">No tax codes yet.</div>
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include('../../includes/footer.php'); ?> 