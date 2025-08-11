<?php
$page_title = "General Ledger";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits
        FROM journal_entry_lines l
        JOIN journal_entries e ON e.id = l.journal_entry_id
        GROUP BY l.account_code ORDER BY l.account_code";
$res = $conn->query($sql);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <div>
    <a href="#" class="btn btn-primary disabled"><i class="bi bi-funnel me-2"></i>Filter Dates</a>
    <a href="#" class="btn btn-success disabled"><i class="bi bi-download me-2"></i>Export</a>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h5>Ledger Summary</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr>
            <th>Account</th>
            <th class="text-end">Debits</th>
            <th class="text-end">Credits</th>
            <th class="text-end">Balance</th>
            <?php if (has_permission('budget_manage')): ?>
            <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && $res->num_rows): while($row=$res->fetch_assoc()): $bal = (float)$row['debits'] - (float)$row['credits']; ?>
            <tr>
              <td><?php echo htmlspecialchars($row['account_code']); ?></td>
              <td class="text-end">$<?php echo number_format($row['debits'] ?? 0,2); ?></td>
              <td class="text-end">$<?php echo number_format($row['credits'] ?? 0,2); ?></td>
              <td class="text-end fw-semibold <?php echo $bal<0?'text-danger':'text-success'; ?>">$<?php echo number_format($bal,2); ?></td>
              <?php if (has_permission('budget_manage')): ?>
              <td>
                <a href="#" class="btn btn-sm btn-info disabled" title="View Account Details"><i class="bi bi-eye"></i></a>
              </td>
              <?php endif; ?>
            </tr>
          <?php endwhile; else: ?>
            <tr>
              <td colspan="<?php echo has_permission('budget_manage') ? '5' : '4'; ?>">
                <div class="empty-state">
                  <i class="bi bi-ledger"></i>
                  <div class="mt-2">No ledger data yet.</div>
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