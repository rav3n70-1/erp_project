<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) {
	header('Location: /erp_project/index.php?status=access_denied');
	exit();
}

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$state = isset($_GET['state']) ? (int)$_GET['state'] : 0;

if ($id > 0) {
	$stmt = $conn->prepare("UPDATE tax_codes SET is_active=? WHERE id=?");
	$stmt->bind_param("ii", $state, $id);
	$stmt->execute();
	$stmt->close();
}

header('Location: /erp_project/modules/accounts/taxes.php');
exit(); 