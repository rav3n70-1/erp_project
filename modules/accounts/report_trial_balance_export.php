<?php
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';
$include_opening = isset($_GET['include_opening']) ? (int)$_GET['include_opening'] : 1;
$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits FROM journal_entry_lines l JOIN journal_entries e ON e.id = l.journal_entry_id";
$where=[]; $params=[]; $types='';
if ($to_date !== '') { $where[]='e.entry_date <= ?'; $types.='s'; $params[]=$to_date; }
if ($where) { $sql.=' WHERE '.implode(' AND ',$where); }
$sql.=' GROUP BY l.account_code';
$stmt=$conn->prepare($sql);
if ($params) { $stmt->bind_param($types, ...$params); }
$stmt->execute();
$res=$stmt->get_result();
$acc=[]; while($r=$res->fetch_assoc()){ $acc[$r['account_code']] = (float)($r['debits']??0) - (float)($r['credits']??0); }
if ($include_opening) { $coas=$conn->query("SELECT account_code, opening_balance FROM chart_of_accounts"); while($c=$coas->fetch_assoc()){ $acc[$c['account_code']] = ($acc[$c['account_code']]??0) + (float)$c['opening_balance']; } }
$meta=[]; $coas2=$conn->query("SELECT account_code, account_name FROM chart_of_accounts"); while($m=$coas2->fetch_assoc()){ $meta[$m['account_code']]=$m['account_name']; }
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="trial_balance.csv"');
$out=fopen('php://output','w');
fputcsv($out,['Account Code','Account Name','Debit','Credit']);
ksort($acc);
$totalD=0; $totalC=0;
foreach($acc as $code=>$bal){ if (abs($bal)<0.0001) continue; if ($bal>=0){ $d=$bal; $c=0; } else { $d=0; $c=-$bal; } $totalD+=$d; $totalC+=$c; fputcsv($out,[$code,$meta[$code]??'',number_format($d,2,'.',''),number_format($c,2,'.','')]); }
fputcsv($out,['','','', '']);
fputcsv($out,['Total','',number_format($totalD,2,'.',''),number_format($totalC,2,'.','')]);
fclose($out);
exit(); 