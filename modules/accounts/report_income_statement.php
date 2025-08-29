<?php
$page_title = "Income Statement";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$from_date = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';

// Map balances by account code
$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits
        FROM journal_entry_lines l JOIN journal_entries e ON e.id=l.journal_entry_id";
$where=[]; $params=[]; $types='';
if ($from_date !== '') { $where[]='e.entry_date >= ?'; $types.='s'; $params[]=$from_date; }
if ($to_date !== '') { $where[]='e.entry_date <= ?'; $types.='s'; $params[]=$to_date; }
if ($where) { $sql.=' WHERE '.implode(' AND ',$where); }
$sql.=' GROUP BY l.account_code';
$stmt=$conn->prepare($sql); if ($params){ $stmt->bind_param($types, ...$params);} $stmt->execute(); $res=$stmt->get_result();
$balances=[]; while($r=$res->fetch_assoc()){ $balances[$r['account_code']] = (float)($r['debits']??0) - (float)($r['credits']??0); }

// Load account meta
$meta=[]; $coas=$conn->query("SELECT account_code, account_name, account_type FROM chart_of_accounts");
while($m=$coas->fetch_assoc()){ $meta[$m['account_code']]=$m; }

$revenues=[]; $expenses=[]; $total_rev=0; $total_exp=0;
foreach($balances as $code=>$bal){
    $type=$meta[$code]['account_type']??''; $name=$meta[$code]['account_name']??$code;
    if ($type==='Revenue') { $amount = -$bal; if (abs($amount)<0.0001) continue; $total_rev += $amount; $revenues[]=['code'=>$code,'name'=>$name,'amount'=>$amount]; }
    if ($type==='Expense') { $amount = $bal; if (abs($amount)<0.0001) continue; $total_exp += $amount; $expenses[]=['code'=>$code,'name'=>$name,'amount'=>$amount]; }
}
usort($revenues, fn($a,$b)=>strcmp($a['code'],$b['code']));
usort($expenses, fn($a,$b)=>strcmp($a['code'],$b['code']));
$net_income = $total_rev - $total_exp;
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <form class="d-flex align-items-end" method="GET">
    <div class="me-2"><label class="form-label mb-1">From</label><input type="date" name="from_date" class="form-control" value="<?php echo htmlspecialchars($from_date); ?>"></div>
    <div class="me-2"><label class="form-label mb-1">To</label><input type="date" name="to_date" class="form-control" value="<?php echo htmlspecialchars($to_date); ?>"></div>
    <div class="me-2"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-2"></i>Apply</button></div>
    <div><a class="btn btn-success" href="/erp_project/modules/accounts/report_income_statement_export.php?from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>"><i class="bi bi-download me-2"></i>Export</a></div>
  </form>
</div>
<div class="card mb-3"><div class="card-header"><h5>Revenue</h5></div><div class="card-body">
  <div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Account</th><th>Name</th><th class="text-end">Amount</th></tr></thead><tbody>
    <?php if (count($revenues)): foreach($revenues as $r): ?>
    <tr><td><?php echo htmlspecialchars($r['code']); ?></td><td><?php echo htmlspecialchars($r['name']); ?></td><td class="text-end">৳<?php echo number_format($r['amount'],2); ?></td></tr>
    <?php endforeach; else: ?><tr><td colspan="3"><div class="text-secondary">No revenue.</div></td></tr><?php endif; ?>
  </tbody><tfoot><tr class="fw-semibold"><td colspan="2" class="text-end">Total Revenue</td><td class="text-end">৳<?php echo number_format($total_rev,2); ?></td></tr></tfoot></table></div>
</div></div>
<div class="card"><div class="card-header"><h5>Expenses</h5></div><div class="card-body">
  <div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Account</th><th>Name</th><th class="text-end">Amount</th></tr></thead><tbody>
    <?php if (count($expenses)): foreach($expenses as $r): ?>
    <tr><td><?php echo htmlspecialchars($r['code']); ?></td><td><?php echo htmlspecialchars($r['name']); ?></td><td class="text-end">৳<?php echo number_format($r['amount'],2); ?></td></tr>
    <?php endforeach; else: ?><tr><td colspan="3"><div class="text-secondary">No expenses.</div></td></tr><?php endif; ?>
  </tbody><tfoot><tr class="fw-semibold"><td colspan="2" class="text-end">Total Expenses</td><td class="text-end">৳<?php echo number_format($total_exp,2); ?></td></tr>
  <tr class="fw-semibold"><td colspan="2" class="text-end">Net Income</td><td class="text-end <?php echo $net_income<0?'text-danger':'text-success'; ?>">৳<?php echo number_format($net_income,2); ?></td></tr></tfoot></table></div>
</div></div>
<?php include('../../includes/footer.php'); ?> 