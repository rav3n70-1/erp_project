<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
	$stmt = $conn->prepare("DELETE FROM journal_entries WHERE id=?");
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$stmt->close();
}
header('Location: /erp_project/modules/accounts/journal_entries.php');
exit(); 