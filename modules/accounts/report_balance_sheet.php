<?php
$page_title = "Balance Sheet";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$as_of = isset($_GET['as_of']) && $_GET['as_of'] !== '' ? $_GET['as_of'] : '';

// Build balances up to as_of
$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits
        FROM journal_entry_lines l JOIN journal_entries e ON e.id=l.journal_entry_id";
$where=[]; $params=[]; $types='';
if ($as_of !== '') { $where[]='e.entry_date <= ?'; $types.='s'; $params[]=$as_of; }
if ($where) { $sql.=' WHERE '.implode(' AND ',$where); }
$sql.=' GROUP BY l.account_code';
$stmt=$conn->prepare($sql); if ($params){ $stmt->bind_param($types, ...$params);} $stmt->execute(); $res=$stmt->get_result();
$balances=[]; while($r=$res->fetch_assoc()){ $balances[$r['account_code']] = (float)($r['debits']??0) - (float)($r['credits']??0); }

// Opening balances
$coas=$conn->query("SELECT account_code, account_name, account_type, opening_balance FROM chart_of_accounts");
$meta=[]; while($m=$coas->fetch_assoc()){ $meta[$m['account_code']]=$m; $balances[$m['account_code']] = ($balances[$m['account_code']]??0) + (float)$m['opening_balance']; }

$assets=[]; $liab=[]; $equity=[]; $totA=0; $totL=0; $totE=0;
foreach($balances as $code=>$bal){
  $type=$meta[$code]['account_type']??''; $name=$meta[$code]['account_name']??$code;
  if ($type==='Asset'){ $amt=$bal; if (abs($amt)<0.0001) continue; $totA+=$amt; $assets[]=['code'=>$code,'name'=>$name,'amount'=>$amt]; }
  if ($type==='Liability'){ $amt=-$bal; if (abs($amt)<0.0001) continue; $totL+=$amt; $liab[]=['code'=>$code,'name'=>$name,'amount'=>$amt]; }
  if ($type==='Equity'){ $amt=-$bal; if (abs($amt)<0.0001) continue; $totE+=$amt; $equity[]=['code'=>$code,'name'=>$name,'amount'=>$amt]; }
}
usort($assets, fn($a,$b)=>strcmp($a['code'],$b['code']));
usort($liab, fn($a,$b)=>strcmp($a['code'],$b['code']));
usort($equity, fn($a,$b)=>strcmp($a['code'],$b['code']));
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <form class="d-flex align-items-end" method="GET">
    <div class="me-2"><label class="form-label mb-1">As of</label><input type="date" name="as_of" class="form-control" value="<?php echo htmlspecialchars($as_of); ?>"></div>
    <div class="me-2"><button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-2"></i>Apply</button></div>
    <div><a class="btn btn-success" href="/erp_project/modules/accounts/report_balance_sheet_export.php?as_of=<?php echo urlencode($as_of); ?>"><i class="bi bi-download me-2"></i>Export</a></div>
  </form>
</div>
<div class="row g-3">
  <div class="col-md-4">
    <div class="card h-100"><div class="card-header"><h5>Assets</h5></div><div class="card-body">
      <div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Account</th><th>Name</th><th class="text-end">Amount</th></tr></thead><tbody>
        <?php if (count($assets)): foreach($assets as $a): ?>
        <tr><td><?php echo htmlspecialchars($a['code']); ?></td><td><?php echo htmlspecialchars($a['name']); ?></td><td class="text-end">৳<?php echo number_format($a['amount'],2); ?></td></tr>
        <?php endforeach; else: ?><tr><td colspan="3"><div class="text-secondary">No assets.</div></td></tr><?php endif; ?>
      </tbody><tfoot><tr class="fw-semibold"><td colspan="2" class="text-end">Total Assets</td><td class="text-end">৳<?php echo number_format($totA,2); ?></td></tr></tfoot></table></div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-header"><h5>Liabilities</h5></div><div class="card-body">
      <div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Account</th><th>Name</th><th class="text-end">Amount</th></tr></thead><tbody>
        <?php if (count($liab)): foreach($liab as $a): ?>
        <tr><td><?php echo htmlspecialchars($a['code']); ?></td><td><?php echo htmlspecialchars($a['name']); ?></td><td class="text-end">৳<?php echo number_format($a['amount'],2); ?></td></tr>
        <?php endforeach; else: ?><tr><td colspan="3"><div class="text-secondary">No liabilities.</div></td></tr><?php endif; ?>
      </tbody><tfoot><tr class="fw-semibold"><td colspan="2" class="text-end">Total Liabilities</td><td class="text-end">৳<?php echo number_format($totL,2); ?></td></tr></tfoot></table></div>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card h-100"><div class="card-header"><h5>Equity</h5></div><div class="card-body">
      <div class="table-responsive"><table class="table"><thead class="table-light"><tr><th>Account</th><th>Name</th><th class="text-end">Amount</th></tr></thead><tbody>
        <?php if (count($equity)): foreach($equity as $a): ?>
        <tr><td><?php echo htmlspecialchars($a['code']); ?></td><td><?php echo htmlspecialchars($a['name']); ?></td><td class="text-end">৳<?php echo number_format($a['amount'],2); ?></td></tr>
        <?php endforeach; else: ?><tr><td colspan="3"><div class="text-secondary">No equity.</div></td></tr><?php endif; ?>
      </tbody><tfoot><tr class="fw-semibold"><td colspan="2" class="text-end">Total Equity</td><td class="text-end">৳<?php echo number_format($totE,2); ?></td></tr></tfoot></table></div>
    </div></div>
  </div>
</div>
<div class="card mt-3"><div class="card-body">
  <div class="d-flex justify-content-between"><div class="fw-semibold">Assets</div><div>৳<?php echo number_format($totA,2); ?></div></div>
  <div class="d-flex justify-content-between"><div class="fw-semibold">Liabilities + Equity</div><div>৳<?php echo number_format($totL+$totE,2); ?></div></div>
</div></div>
<?php include('../../includes/footer.php'); ?> 