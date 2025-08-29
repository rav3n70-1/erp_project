<?php
$page_title = "Edit Tax Code";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { 
	header('Location: /erp_project/dashboard.php?status=access_denied'); 
	exit(); 
}

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/taxes.php'); exit(); }

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$tax_code = trim($_POST['tax_code']);
	$description = trim($_POST['description']);
	$rate = floatval($_POST['rate']);
	$is_active = isset($_POST['is_active']) ? 1 : 0;
	if (empty($tax_code) || $rate < 0 || $rate > 100) {
		$error = "Tax code is required and rate must be between 0 and 100.";
	} else {
		$stmt = $conn->prepare("UPDATE tax_codes SET tax_code=?, description=?, rate=?, is_active=? WHERE id=?");
		$stmt->bind_param("ssdii", $tax_code, $description, $rate, $is_active, $id);
		if ($stmt->execute()) {
			$success = "Updated successfully.";
		} else {
			$error = "Failed to update: " . $conn->error;
		}
		$stmt->close();
	}
}

$stmt = $conn->prepare("SELECT * FROM tax_codes WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$tax = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$tax) { header('Location: /erp_project/modules/accounts/taxes.php'); exit(); }
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/taxes.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
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
	<div class="card-header"><h5>Tax Code</h5></div>
	<div class="card-body">
		<form method="POST">
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label class="form-label">Tax Code *</label>
						<input type="text" name="tax_code" class="form-control" value="<?php echo htmlspecialchars($tax['tax_code']); ?>" required>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label class="form-label">Tax Rate (%) *</label>
						<input type="number" step="0.01" min="0" max="100" name="rate" class="form-control" value="<?php echo htmlspecialchars($tax['rate']); ?>" required>
					</div>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">Description</label>
				<textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($tax['description']); ?></textarea>
			</div>
			<div class="mb-3 form-check">
				<input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo $tax['is_active'] ? 'checked' : ''; ?>>
				<label class="form-check-label" for="is_active">Active</label>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end">
				<button type="submit" class="btn btn-primary">Save Changes</button>
			</div>
		</form>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 