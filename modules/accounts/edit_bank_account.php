<?php
$page_title = "Edit Bank Account";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/bank_accounts.php'); exit(); }

$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$account_name = trim($_POST['account_name']);
	$bank_name = trim($_POST['bank_name']);
	$account_number = trim($_POST['account_number']);
	if ($account_name==='') { $error='Account name is required.'; }
	if (!$error) {
		$stmt=$conn->prepare("UPDATE bank_accounts SET account_name=?, bank_name=?, account_number=? WHERE id=?");
		$stmt->bind_param("sssi", $account_name, $bank_name, $account_number, $id);
		if ($stmt->execute()) { $success='Saved.'; } else { $error='Failed: '.$conn->error; }
		$stmt->close();
	}
}
$stmt = $conn->prepare("SELECT * FROM bank_accounts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$account) { header('Location: /erp_project/modules/accounts/bank_accounts.php'); exit(); }
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/view_bank_account.php?id=<?php echo (int)$id; ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card">
	<div class="card-header"><h5>Bank Account</h5></div>
	<div class="card-body">
		<form method="POST">
			<div class="row">
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Account Name *</label><input name="account_name" class="form-control" value="<?php echo htmlspecialchars($account['account_name']); ?>" required></div></div>
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Bank Name</label><input name="bank_name" class="form-control" value="<?php echo htmlspecialchars($account['bank_name']); ?>"></div></div>
			</div>
			<div class="row">
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Account Number</label><input name="account_number" class="form-control" value="<?php echo htmlspecialchars($account['account_number']); ?>"></div></div>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end"><button type="submit" class="btn btn-primary">Save</button></div>
		</form>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 