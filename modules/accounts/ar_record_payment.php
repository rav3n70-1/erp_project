<?php
$page_title = "Record AR Payment";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$invoice_id = isset($_GET['invoice_id']) ? (int)$_GET['invoice_id'] : 0;
if ($invoice_id<=0) { header('Location: /erp_project/modules/accounts/accounts_receivable.php'); exit(); }
$stmt=$conn->prepare("SELECT * FROM ar_invoices WHERE id=?");
$stmt->bind_param("i", $invoice_id);
$stmt->execute();
$invoice=$stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$invoice) { header('Location: /erp_project/modules/accounts/accounts_receivable.php'); exit(); }
$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$payment_date = $_POST['payment_date'];
	$amount = floatval($_POST['amount']);
	$method = trim($_POST['method']);
	$reference = trim($_POST['reference']);
	if ($amount<=0) { $error='Amount must be greater than zero.'; }
	$remaining = max(0, ($invoice['total'] - $invoice['paid']));
	if ($amount > $remaining) { $error='Amount exceeds outstanding balance.'; }
	if (!$error) {
		$p=$conn->prepare("INSERT INTO ar_payments (invoice_id, payment_date, amount, method, reference) VALUES (?,?,?,?,?)");
		$p->bind_param("isdss", $invoice_id, $payment_date, $amount, $method, $reference);
		if ($p->execute()) {
			$p->close();
			// update invoice paid and status
			$upd=$conn->prepare("UPDATE ar_invoices SET paid = paid + ?, status = CASE WHEN paid + ? >= total THEN 'Paid' WHEN paid + ? > 0 THEN 'Partially Paid' ELSE status END WHERE id=?");
			$upd->bind_param("dddi", $amount, $amount, $amount, $invoice_id);
			$upd->execute();
			$upd->close();
			$success='Payment recorded.';
			// reload invoice
			$stmt=$conn->prepare("SELECT * FROM ar_invoices WHERE id=?");
			$stmt->bind_param("i", $invoice_id);
			$stmt->execute();
			$invoice=$stmt->get_result()->fetch_assoc();
			$stmt->close();
		} else { $error='Failed: '.$conn->error; }
	}
}
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1><?php echo $page_title; ?></h1><a href="/erp_project/modules/accounts/accounts_receivable.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a></div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card mb-3"><div class="card-body"><div class="row"><div class="col-md-3"><strong>Invoice #</strong> <?php echo htmlspecialchars($invoice['invoice_no']); ?></div><div class="col-md-3"><strong>Total</strong> $<?php echo number_format($invoice['total'],2); ?></div><div class="col-md-3"><strong>Paid</strong> $<?php echo number_format($invoice['paid'],2); ?></div><div class="col-md-3"><strong>Balance</strong> $<?php echo number_format(max(0,$invoice['total']-$invoice['paid']),2); ?></div></div></div></div>
<div class="card"><div class="card-header"><h5>Payment</h5></div><div class="card-body">
	<form method="POST">
		<div class="row">
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Date *</label><input type="date" name="payment_date" class="form-control" value="<?php echo htmlspecialchars($_POST['payment_date'] ?? date('Y-m-d')); ?>" required></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Amount *</label><input type="number" step="0.01" name="amount" class="form-control" value="<?php echo htmlspecialchars($_POST['amount'] ?? ''); ?>" required></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Method</label><input name="method" class="form-control" value="<?php echo htmlspecialchars($_POST['method'] ?? ''); ?>"></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Reference</label><input name="reference" class="form-control" value="<?php echo htmlspecialchars($_POST['reference'] ?? ''); ?>"></div></div>
		</div>
		<div class="d-grid gap-2 d-md-flex justify-content-md-end"><button type="submit" class="btn btn-primary">Record Payment</button></div>
	</form>
</div></div>
<?php include('../../includes/footer.php'); ?> 