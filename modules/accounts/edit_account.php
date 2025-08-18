<?php
$page_title = "Edit Account";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { 
	header('Location: /erp_project/index.php?status=access_denied'); 
	exit(); 
}

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/chart_of_accounts.php'); exit(); }

// Load account
$stmt = $conn->prepare("SELECT * FROM chart_of_accounts WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$account) { header('Location: /erp_project/modules/accounts/chart_of_accounts.php'); exit(); }

// Parent accounts for dropdown (exclude self)
$parent_accounts = $conn->prepare("SELECT account_code, account_name FROM chart_of_accounts WHERE is_active = 1 AND id <> ? ORDER BY account_code");
$parent_accounts->bind_param("i", $id);
$parent_accounts->execute();
$parent_rs = $parent_accounts->get_result();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$account_code = trim($_POST['account_code']);
	$account_name = trim($_POST['account_name']);
	$account_type = $_POST['account_type'];
	$parent_account_code = !empty($_POST['parent_account_code']) ? $_POST['parent_account_code'] : null;
	$is_posting = isset($_POST['is_posting']) ? 1 : 0;
	$opening_balance = floatval($_POST['opening_balance']);
	$is_active = isset($_POST['is_active']) ? 1 : 0;

	if (empty($account_code) || empty($account_name) || empty($account_type)) {
		$error = "Account code, name, and type are required.";
	} else {
		// Prevent circular parent reference
		if ($parent_account_code === $account['account_code']) {
			$error = "Parent account cannot be the same as this account.";
		} else {
			$stmt = $conn->prepare("UPDATE chart_of_accounts SET account_code=?, account_name=?, account_type=?, parent_account_code=?, is_posting=?, opening_balance=?, is_active=? WHERE id=?");
			$stmt->bind_param("ssssidii", $account_code, $account_name, $account_type, $parent_account_code, $is_posting, $opening_balance, $is_active, $id);
			if ($stmt->execute()) {
				$success = "Account updated successfully!";
				// Refresh loaded data
				$account['account_code'] = $account_code;
				$account['account_name'] = $account_name;
				$account['account_type'] = $account_type;
				$account['parent_account_code'] = $parent_account_code;
				$account['is_posting'] = $is_posting;
				$account['opening_balance'] = $opening_balance;
				$account['is_active'] = $is_active;
			} else {
				$error = "Error updating account: " . $conn->error;
			}
			$stmt->close();
		}
	}
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/chart_of_accounts.php" class="btn btn-secondary">
		<i class="bi bi-arrow-left me-2"></i>Back to Accounts
	</a>
</div>

<?php if ($success): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
	<?php echo $success; ?>
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if ($error): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
	<?php echo $error; ?>
	<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="card">
	<div class="card-header"><h5>Account Information</h5></div>
	<div class="card-body">
		<form method="POST">
			<div class="row">
				<div class="col-md-4">
					<div class="mb-3">
						<label class="form-label">Account Code *</label>
						<input type="text" name="account_code" class="form-control" value="<?php echo htmlspecialchars($account['account_code']); ?>" required>
					</div>
				</div>
				<div class="col-md-8">
					<div class="mb-3">
						<label class="form-label">Account Name *</label>
						<input type="text" name="account_name" class="form-control" value="<?php echo htmlspecialchars($account['account_name']); ?>" required>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="mb-3">
						<label class="form-label">Type *</label>
						<select name="account_type" class="form-select" required>
							<?php $types=['Asset','Liability','Equity','Revenue','Expense']; foreach($types as $t): ?>
							<option value="<?php echo $t; ?>" <?php echo ($account['account_type']===$t)?'selected':''; ?>><?php echo $t; ?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="mb-3">
						<label class="form-label">Parent Account</label>
						<select name="parent_account_code" class="form-select">
							<option value="">None</option>
							<?php if ($parent_rs): while($pa=$parent_rs->fetch_assoc()): ?>
							<option value="<?php echo htmlspecialchars($pa['account_code']); ?>" <?php echo (($account['parent_account_code']??'')===$pa['account_code'])?'selected':''; ?>><?php echo htmlspecialchars($pa['account_code'].' - '.$pa['account_name']); ?></option>
							<?php endwhile; endif; ?>
						</select>
					</div>
				</div>
				<div class="col-md-4">
					<div class="mb-3 form-check mt-4">
						<input type="checkbox" name="is_posting" class="form-check-input" id="is_posting" <?php echo (($account['is_posting']??1)?'checked':''); ?>>
						<label for="is_posting" class="form-check-label">Posting Account</label>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="mb-3">
						<label class="form-label">Opening Balance</label>
						<input type="number" step="0.01" name="opening_balance" class="form-control" value="<?php echo htmlspecialchars($account['opening_balance']); ?>">
					</div>
				</div>
				<div class="col-md-4">
					<div class="mb-3 form-check mt-4">
						<input type="checkbox" name="is_active" class="form-check-input" id="is_active" <?php echo (($account['is_active']??1)?'checked':''); ?>>
						<label for="is_active" class="form-check-label">Active</label>
					</div>
				</div>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end">
				<button type="submit" class="btn btn-primary">Save Changes</button>
			</div>
		</form>
	</div>
</div>

<?php include('../../includes/footer.php'); ?> 