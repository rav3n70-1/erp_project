<?php
$page_title = "Bank & Cash";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$res = $conn->query("SELECT * FROM bank_accounts ORDER BY account_name ASC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <a href="/erp_project/modules/accounts/add_bank_account.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add Bank Account</a>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h5>Accounts</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr>
            <th>Name</th>
            <th>Bank</th>
            <th>Account #</th>
            <th class="text-end">Balance</th>
            <th>Created</th>
            <?php if (has_permission('budget_manage')): ?>
            <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && $res->num_rows): while($row=$res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['account_name']); ?></td>
            <td><?php echo htmlspecialchars($row['bank_name']); ?></td>
            <td><?php echo htmlspecialchars($row['account_number']); ?></td>
            <td class="text-end">৳<?php echo number_format($row['balance'],2); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <?php if (has_permission('budget_manage')): ?>
            <td>
              <a href="/erp_project/modules/accounts/view_bank_account.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-info" title="View Transactions"><i class="bi bi-list-ul"></i></a>
              <a href="/erp_project/modules/accounts/record_bank_transaction.php?bank_account_id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-success" title="Record Transaction"><i class="bi bi-plus-circle"></i></a>
              <a href="/erp_project/modules/accounts/edit_bank_account.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="<?php echo has_permission('budget_manage') ? '6' : '5'; ?>">
              <div class="empty-state">
                <i class="bi bi-bank"></i>
                <div class="mt-2">No bank accounts yet.</div>
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