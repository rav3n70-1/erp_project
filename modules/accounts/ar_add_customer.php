<?php
$page_title = "New Customer";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$name = trim($_POST['customer_name']);
	$email = trim($_POST['email']);
	if ($name==='') { $error='Customer name is required.'; }
	if (!$error) {
		$stmt=$conn->prepare("INSERT INTO ar_customers (customer_name,email) VALUES (?,?)");
		$stmt->bind_param("ss", $name, $email);
		if ($stmt->execute()) { $success='Customer created.'; $name=$email=''; } else { $error='Failed: '.$conn->error; }
		$stmt->close();
	}
}
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1><?php echo $page_title; ?></h1><a href="/erp_project/modules/accounts/accounts_receivable.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a></div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card"><div class="card-header"><h5>Customer</h5></div><div class="card-body">
	<form method="POST">
		<div class="row">
			<div class="col-md-6"><div class="mb-3"><label class="form-label">Name *</label><input name="customer_name" class="form-control" value="<?php echo htmlspecialchars($name ?? ''); ?>" required></div></div>
			<div class="col-md-6"><div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email ?? ''); ?>"></div></div>
		</div>
		<div class="d-grid gap-2 d-md-flex justify-content-md-end"><button type="submit" class="btn btn-primary">Create</button></div>
	</form>
</div></div>
<?php include('../../includes/footer.php'); ?> 