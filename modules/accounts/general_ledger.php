<?php
$page_title = "General Ledger";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$from_date = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';
$account_code = isset($_GET['account_code']) ? trim($_GET['account_code']) : '';

$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits
        FROM journal_entry_lines l
        JOIN journal_entries e ON e.id = l.journal_entry_id";
$where = [];
$params = [];
$types = '';
if ($from_date !== '') { $where[] = 'e.entry_date >= ?'; $types .= 's'; $params[] = $from_date; }
if ($to_date !== '') { $where[] = 'e.entry_date <= ?'; $types .= 's'; $params[] = $to_date; }
if ($account_code !== '') { $where[] = 'l.account_code = ?'; $types .= 's'; $params[] = $account_code; }
if (count($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' GROUP BY l.account_code ORDER BY l.account_code';

$stmt = $conn->prepare($sql);
if (count($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <div>
    <form class="d-flex align-items-end" method="GET" action="">
      <div class="me-2">
        <label class="form-label mb-1">From</label>
        <input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>">
      </div>
      <div class="me-2">
        <label class="form-label mb-1">To</label>
        <input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>">
      </div>
      <div class="me-2">
        <label class="form-label mb-1">Account</label>
        <input type="text" name="account_code" class="form-control" placeholder="e.g., 1010" value="<?php echo htmlspecialchars($account_code); ?>">
      </div>
      <div class="me-2">
        <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-2"></i>Filter</button>
      </div>
      <div>
        <a class="btn btn-success" href="/erp_project/modules/accounts/export_general_ledger.php?from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>&account_code=<?php echo urlencode($account_code); ?>">
          <i class="bi bi-download me-2"></i>Export
        </a>
      </div>
    </form>
  </div>
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
          <?php if ($res && $res->num_rows): while($row=$res->fetch_assoc()): $bal = (float)($row['debits'] ?? 0) - (float)($row['credits'] ?? 0); ?>
            <tr>
              <td><?php echo htmlspecialchars($row['account_code']); ?></td>
              <td class="text-end">৳<?php echo number_format($row['debits'] ?? 0,2); ?></td>
              <td class="text-end">৳<?php echo number_format($row['credits'] ?? 0,2); ?></td>
              <td class="text-end fw-semibold <?php echo $bal<0?'text-danger':'text-success'; ?>">৳<?php echo number_format($bal,2); ?></td>
              <?php if (has_permission('budget_manage')): ?>
              <td>
                <a href="/erp_project/modules/accounts/view_ledger_account.php?account_code=<?php echo urlencode($row['account_code']); ?>&from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>" class="btn btn-sm btn-info" title="View Account Details"><i class="bi bi-eye"></i></a>
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