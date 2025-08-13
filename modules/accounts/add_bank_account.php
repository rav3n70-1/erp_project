<?php
$page_title = "Add Bank Account";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$account_name = trim($_POST['account_name']);
	$bank_name = trim($_POST['bank_name']);
	$account_number = trim($_POST['account_number']);
	$balance = floatval($_POST['balance'] ?? 0);
	if ($account_name==='') { $error='Account name is required.'; }
	if (!$error) {
		$stmt=$conn->prepare("INSERT INTO bank_accounts (account_name, bank_name, account_number, balance) VALUES (?,?,?,?)");
		$stmt->bind_param("sssd", $account_name, $bank_name, $account_number, $balance);
		if ($stmt->execute()) { $success='Bank account added.'; $account_name=$bank_name=$account_number=''; $balance=0; } else { $error='Failed: '.$conn->error; }
		$stmt->close();
	}
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/bank_accounts.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card">
	<div class="card-header"><h5>Bank Account</h5></div>
	<div class="card-body">
		<form method="POST">
			<div class="row">
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Account Name *</label><input name="account_name" class="form-control" value="<?php echo htmlspecialchars($account_name ?? ''); ?>" required></div></div>
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Bank Name</label><input name="bank_name" class="form-control" value="<?php echo htmlspecialchars($bank_name ?? ''); ?>"></div></div>
			</div>
			<div class="row">
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Account Number</label><input name="account_number" class="form-control" value="<?php echo htmlspecialchars($account_number ?? ''); ?>"></div></div>
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Opening Balance</label><input type="number" step="0.01" name="balance" class="form-control" value="<?php echo htmlspecialchars($balance ?? 0); ?>"></div></div>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end"><button type="submit" class="btn btn-primary">Create</button></div>
		</form>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 