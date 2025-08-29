<?php
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$as_of = isset($_GET['as_of']) && $_GET['as_of'] !== '' ? $_GET['as_of'] : '';
$sql = "SELECT l.account_code, SUM(l.debit) AS debits, SUM(l.credit) AS credits FROM journal_entry_lines l JOIN journal_entries e ON e.id=l.journal_entry_id";
$where=[]; $params=[]; $types='';
if ($as_of !== '') { $where[]='e.entry_date <= ?'; $types.='s'; $params[]=$as_of; }
if ($where) { $sql.=' WHERE '.implode(' AND ',$where); }
$sql.=' GROUP BY l.account_code';
$stmt=$conn->prepare($sql); if ($params){ $stmt->bind_param($types, ...$params);} $stmt->execute(); $res=$stmt->get_result();
$balances=[]; while($r=$res->fetch_assoc()){ $balances[$r['account_code']] = (float)($r['debits']??0) - (float)($r['credits']??0); }
$coas=$conn->query("SELECT account_code, account_name, account_type, opening_balance FROM chart_of_accounts");
$meta=[]; while($m=$coas->fetch_assoc()){ $meta[$m['account_code']]=$m; $balances[$m['account_code']] = ($balances[$m['account_code']]??0) + (float)$m['opening_balance']; }
$assets=[]; $liab=[]; $equity=[]; $totA=0; $totL=0; $totE=0;
foreach($balances as $code=>$bal){ $type=$meta[$code]['account_type']??''; $name=$meta[$code]['account_name']??$code; if ($type==='Asset'){ $amt=$bal; if (abs($amt)<0.0001) continue; $totA+=$amt; $assets[]=[$code,$name,$amt]; } if ($type==='Liability'){ $amt=-$bal; if (abs($amt)<0.0001) continue; $totL+=$amt; $liab[]=[$code,$name,$amt]; } if ($type==='Equity'){ $amt=-$bal; if (abs($amt)<0.0001) continue; $totE+=$amt; $equity[]=[$code,$name,$amt]; } }
header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename="balance_sheet.csv"'); $out=fopen('php://output','w');
fputcsv($out,['Balance Sheet']); fputcsv($out,['As of',$as_of]); fputcsv($out,[]);
fputcsv($out,['Assets']); fputcsv($out,['Account Code','Account Name','Amount']); foreach($assets as $row){ fputcsv($out,[$row[0],$row[1],number_format($row[2],2,'.','')]); } fputcsv($out,['Total Assets','',number_format($totA,2,'.','')]); fputcsv($out,[]);
fputcsv($out,['Liabilities']); fputcsv($out,['Account Code','Account Name','Amount']); foreach($liab as $row){ fputcsv($out,[$row[0],$row[1],number_format($row[2],2,'.','')]); } fputcsv($out,['Total Liabilities','',number_format($totL,2,'.','')]); fputcsv($out,[]);
fputcsv($out,['Equity']); fputcsv($out,['Account Code','Account Name','Amount']); foreach($equity as $row){ fputcsv($out,[$row[0],$row[1],number_format($row[2],2,'.','')]); } fputcsv($out,['Total Equity','',number_format($totE,2,'.','')]); fputcsv($out,[]);
$f = $totL+$totE; fputcsv($out,['Check: Assets vs Liabilities+Equity','',number_format($totA,2,'.','').' vs '.number_format($f,2,'.','')]); fclose($out); exit(); 