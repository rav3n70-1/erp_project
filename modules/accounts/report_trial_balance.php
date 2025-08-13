<?php
$page_title = "Trial Balance";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);

$to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';
$include_opening = isset($_GET['include_opening']) ? (int)$_GET['include_opening'] : 1;

// Build sums from journal lines up to to_date
$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits
        FROM journal_entry_lines l
        JOIN journal_entries e ON e.id = l.journal_entry_id";
$where = [];
$params = [];
$types = '';
if ($to_date !== '') { $where[] = 'e.entry_date <= ?'; $types .= 's'; $params[] = $to_date; }
if (count($where)) { $sql .= ' WHERE ' . implode(' AND ', $where); }
$sql .= ' GROUP BY l.account_code';
$stmt = $conn->prepare($sql);
if (count($params)) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res = $stmt->get_result();

// Fetch opening balances if requested
$openings = [];
if ($include_opening) {
    $coas = $conn->query("SELECT account_code, opening_balance FROM chart_of_accounts");
    while ($row = $coas->fetch_assoc()) { $openings[$row['account_code']] = (float)$row['opening_balance']; }
}

// Aggregate balances by account
$acc = [];
while ($row = $res->fetch_assoc()) {
    $code = $row['account_code'];
    $debits = (float)($row['debits'] ?? 0);
    $credits = (float)($row['credits'] ?? 0);
    $acc[$code] = ($acc[$code] ?? 0) + ($debits - $credits);
}
if ($include_opening) {
    foreach ($openings as $code => $ob) { $acc[$code] = ($acc[$code] ?? 0) + $ob; }
}

// Load account metadata
$meta = [];
$coas2 = $conn->query("SELECT account_code, account_name, account_type FROM chart_of_accounts");
while ($row = $coas2->fetch_assoc()) { $meta[$row['account_code']] = $row; }

// Prepare rows: account, name, debit balance, credit balance
$rows = [];
$total_debit = 0; $total_credit = 0;
foreach ($acc as $code => $balance) {
    if (abs($balance) < 0.0001) continue; // skip zeros
    if ($balance >= 0) { $debit = $balance; $credit = 0; } else { $debit = 0; $credit = -$balance; }
    $total_debit += $debit; $total_credit += $credit;
    $rows[] = [
        'code' => $code,
        'name' => $meta[$code]['account_name'] ?? '',
        'type' => $meta[$code]['account_type'] ?? '',
        'debit' => $debit,
        'credit' => $credit,
    ];
}
usort($rows, function($a,$b){ return strcmp($a['code'],$b['code']); });
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <form class="d-flex align-items-end" method="GET">
    <div class="me-2"><label class="form-label mb-1">As of</label><input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>"></div>
    <div class="form-check me-2 mb-1 ms-2">
      <input class="form-check-input" type="checkbox" value="1" name="include_opening" id="include_opening" <?php echo $include_opening? 'checked':''; ?>>
      <label class="form-check-label" for="include_opening">Include opening balances</label>
    </div>
    <div class="me-2"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-2"></i>Apply</button></div>
    <div><a class="btn btn-success" href="/erp_project/modules/accounts/report_trial_balance_export.php?to_date=<?php echo urlencode($to_date); ?>&include_opening=<?php echo (int)$include_opening; ?>"><i class="bi bi-download me-2"></i>Export</a></div>
  </form>
</div>
<div class="card">
  <div class="card-header"><h5>Trial Balance</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-dark"><tr><th>Account</th><th>Name</th><th class="text-end">Debit</th><th class="text-end">Credit</th></tr></thead>
        <tbody>
          <?php if (count($rows)): foreach ($rows as $r): ?>
          <tr>
            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($r['code']); ?></span></td>
            <td><?php echo htmlspecialchars($r['name']); ?></td>
            <td class="text-end">$<?php echo number_format($r['debit'],2); ?></td>
            <td class="text-end">$<?php echo number_format($r['credit'],2); ?></td>
          </tr>
          <?php endforeach; else: ?>
          <tr><td colspan="4"><div class="empty-state"><i class="bi bi-table"></i><div class="mt-2">No data.</div></div></td></tr>
          <?php endif; ?>
        </tbody>
        <?php if (count($rows)): ?>
        <tfoot>
          <tr class="fw-semibold"><td colspan="2" class="text-end">Total</td><td class="text-end">$<?php echo number_format($total_debit,2); ?></td><td class="text-end">$<?php echo number_format($total_credit,2); ?></td></tr>
        </tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>
<?php include('../../includes/footer.php'); ?> 