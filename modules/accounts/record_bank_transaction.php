<?php
$page_title = "Record Bank Transaction";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$bank_account_id = isset($_GET['bank_account_id']) ? (int)$_GET['bank_account_id'] : 0;
if ($bank_account_id <= 0) { header('Location: /erp_project/modules/accounts/bank_accounts.php'); exit(); }

$acc_stmt = $conn->prepare("SELECT * FROM bank_accounts WHERE id=?");
$acc_stmt->bind_param("i", $bank_account_id);
$acc_stmt->execute();
$account = $acc_stmt->get_result()->fetch_assoc();
$acc_stmt->close();
if (!$account) { header('Location: /erp_project/modules/accounts/bank_accounts.php'); exit(); }

$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$txn_date = $_POST['txn_date'];
	$type = $_POST['type'];
	$amount = floatval($_POST['amount']);
	$memo = trim($_POST['memo'] ?? '');
	if ($amount <= 0) { $error = 'Amount must be greater than zero.'; }
	if (!$error) {
		$stmt=$conn->prepare("INSERT INTO bank_transactions (bank_account_id, txn_date, type, amount, memo) VALUES (?,?,?,?,?)");
		$stmt->bind_param("issds", $bank_account_id, $txn_date, $type, $amount, $memo);
		if ($stmt->execute()) {
			$stmt->close();
			// Update balance
			$delta = ($type==='Deposit' || $type==='Transfer In') ? $amount : -$amount;
			$upd=$conn->prepare("UPDATE bank_accounts SET balance = balance + ? WHERE id=?");
			$upd->bind_param("di", $delta, $bank_account_id);
			$upd->execute();
			$upd->close();
			$success='Transaction recorded.';
		} else {
			$error='Failed: '.$conn->error;
		}
	}
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/view_bank_account.php?id=<?php echo (int)$bank_account_id; ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card">
	<div class="card-header"><h5>Transaction for <?php echo htmlspecialchars($account['account_name']); ?></h5></div>
	<div class="card-body">
		<form method="POST">
			<div class="row">
				<div class="col-md-3"><div class="mb-3"><label class="form-label">Date *</label><input type="date" name="txn_date" class="form-control" value="<?php echo htmlspecialchars($_POST['txn_date'] ?? date('Y-m-d')); ?>" required></div></div>
				<div class="col-md-3"><div class="mb-3"><label class="form-label">Type *</label>
					<select name="type" class="form-select" required>
						<?php $types=['Deposit','Withdrawal','Transfer In','Transfer Out']; foreach($types as $t): ?>
						<option value="<?php echo $t; ?>" <?php echo (($_POST['type'] ?? '')===$t)?'selected':''; ?>><?php echo $t; ?></option>
						<?php endforeach; ?>
					</select>
				</div></div>
				<div class="col-md-3"><div class="mb-3"><label class="form-label">Amount *</label><input type="number" step="0.01" name="amount" class="form-control" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" required></div></div>
				<div class="col-md-3"><div class="mb-3"><label class="form-label">Memo</label><input name="memo" class="form-control" value="<?php echo htmlspecialchars($_POST['memo'] ?? ''); ?>"></div></div>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end"><button type="submit" class="btn btn-primary">Record</button></div>
		</form>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 