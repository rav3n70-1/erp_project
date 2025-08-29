<?php
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$from_date = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';
$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits FROM journal_entry_lines l JOIN journal_entries e ON e.id=l.journal_entry_id";
$where=[]; $params=[]; $types='';
if ($from_date !== '') { $where[]='e.entry_date >= ?'; $types.='s'; $params[]=$from_date; }
if ($to_date !== '') { $where[]='e.entry_date <= ?'; $types.='s'; $params[]=$to_date; }
if ($where) { $sql.=' WHERE '.implode(' AND ',$where); }
$sql.=' GROUP BY l.account_code';
$stmt=$conn->prepare($sql); if ($params){ $stmt->bind_param($types, ...$params);} $stmt->execute(); $res=$stmt->get_result();
$meta=[]; $coas=$conn->query("SELECT account_code, account_name, account_type FROM chart_of_accounts"); while($m=$coas->fetch_assoc()){ $meta[$m['account_code']]=$m; }
$revenues=[]; $expenses=[]; $total_rev=0; $total_exp=0;
while($r=$res->fetch_assoc()){
  $code=$r['account_code']; $bal=((float)($r['debits']??0) - (float)($r['credits']??0));
  $type=$meta[$code]['account_type']??''; $name=$meta[$code]['account_name']??$code;
  if ($type==='Revenue'){ $amt = -$bal; if (abs($amt)<0.0001) continue; $total_rev += $amt; $revenues[] = [$code,$name,$amt]; }
  if ($type==='Expense'){ $amt = $bal; if (abs($amt)<0.0001) continue; $total_exp += $amt; $expenses[] = [$code,$name,$amt]; }
}
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="income_statement.csv"');
$out=fopen('php://output','w');
fputcsv($out,['Income Statement']);
fputcsv($out,['From',$from_date,'To',$to_date]);
fputcsv($out,[]);
fputcsv($out,['Revenue']);
fputcsv($out,['Account Code','Account Name','Amount']);
foreach($revenues as $row){ fputcsv($out,[$row[0],$row[1],number_format($row[2],2,'.','')]); }
fputcsv($out,['Total Revenue','',number_format($total_rev,2,'.','')]);
fputcsv($out,[]);
fputcsv($out,['Expenses']);
fputcsv($out,['Account Code','Account Name','Amount']);
foreach($expenses as $row){ fputcsv($out,[$row[0],$row[1],number_format($row[2],2,'.','')]); }
fputcsv($out,['Total Expenses','',number_format($total_exp,2,'.','')]);
fputcsv($out,[]);
$net=$total_rev-$total_exp; fputcsv($out,['Net Income','',number_format($net,2,'.','')]);
fclose($out);
exit(); 