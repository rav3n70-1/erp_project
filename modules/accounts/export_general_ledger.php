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

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="general_ledger.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['Account Code','Debits','Credits','Balance']);
while ($row = $res->fetch_assoc()) {
    $debits = (float)($row['debits'] ?? 0);
    $credits = (float)($row['credits'] ?? 0);
    $balance = $debits - $credits;
    fputcsv($out, [$row['account_code'], number_format($debits,2,'.',''), number_format($credits,2,'.',''), number_format($balance,2,'.','')]);
}
fclose($out);
exit(); 