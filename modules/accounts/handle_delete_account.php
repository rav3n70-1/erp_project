<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/chart_of_accounts.php'); exit(); }

// Fetch account_code for reference checks
$stmt = $conn->prepare("SELECT account_code FROM chart_of_accounts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();

if (!$res || !$res->num_rows) { header('Location: /erp_project/modules/accounts/chart_of_accounts.php'); exit(); }
$row = $res->fetch_assoc();
$account_code = $row['account_code'];

// Check for child accounts
$child_check = $conn->prepare("SELECT COUNT(*) as cnt FROM chart_of_accounts WHERE parent_account_code = ?");
$child_check->bind_param("s", $account_code);
$child_check->execute();
$child_cnt = $child_check->get_result()->fetch_assoc()['cnt'] ?? 0;
$child_check->close();
if ($child_cnt > 0) { header('Location: /erp_project/modules/accounts/chart_of_accounts.php?has_children=1'); exit(); }

// Check if account is referenced in journal entries
$ref_check = $conn->prepare("SELECT COUNT(*) as cnt FROM journal_entry_lines WHERE account_code = ?");
$ref_check->bind_param("s", $account_code);
$ref_check->execute();
$ref_cnt = $ref_check->get_result()->fetch_assoc()['cnt'] ?? 0;
$ref_check->close();
if ($ref_cnt > 0) { header('Location: /erp_project/modules/accounts/chart_of_accounts.php?in_use=1'); exit(); }

// Safe to delete
$del = $conn->prepare("DELETE FROM chart_of_accounts WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();
$del->close();

header('Location: /erp_project/modules/accounts/chart_of_accounts.php?deleted=1');
exit(); 