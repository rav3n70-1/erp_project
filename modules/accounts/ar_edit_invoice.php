<?php
$page_title = "Edit Invoice";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('budget_manage')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0) { header('Location: /erp_project/modules/accounts/accounts_receivable.php'); exit(); }
$customers = $conn->query("SELECT id, customer_name FROM ar_customers ORDER BY customer_name");
$tax_codes = $conn->query("SELECT id, tax_code, rate FROM tax_codes WHERE is_active=1 ORDER BY tax_code");
$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$customer_id = (int)$_POST['customer_id'];
	$invoice_no = trim($_POST['invoice_no']);
	$invoice_date = $_POST['invoice_date'];
	$due_date = $_POST['due_date'];
	$subtotal = floatval($_POST['subtotal']);
	$tax_code_id = !empty($_POST['tax_code_id']) ? (int)$_POST['tax_code_id'] : NULL;
	$tax_amount = floatval($_POST['tax_amount']);
	$total = floatval($_POST['total']);
	if ($customer_id<=0 || $invoice_no==='' || $subtotal<0 || $total<0) { $error='Please fill required fields and amounts must be valid.'; }
	if (!$error) {
		$stmt=$conn->prepare("UPDATE ar_invoices SET customer_id=?, invoice_no=?, invoice_date=?, due_date=?, subtotal=?, tax_code_id=?, tax_amount=?, total=? WHERE id=?");
		$stmt->bind_param("isssdddii", $customer_id, $invoice_no, $invoice_date, $due_date, $subtotal, $tax_code_id, $tax_amount, $total, $id);
		if ($stmt->execute()) { $success='Saved.'; } else { $error='Failed: '.$conn->error; }
		$stmt->close();
	}
}
// Load invoice
$inv=$conn->prepare("SELECT * FROM ar_invoices WHERE id=?");
$inv->bind_param("i", $id);
$inv->execute();
$invoice=$inv->get_result()->fetch_assoc();
$inv->close();
if (!$invoice) { header('Location: /erp_project/modules/accounts/accounts_receivable.php'); exit(); }
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1><?php echo $page_title; ?></h1><a href="/erp_project/modules/accounts/ar_view_invoice.php?id=<?php echo (int)$id; ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a></div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card"><div class="card-header"><h5>Invoice</h5></div><div class="card-body">
	<form method="POST">
		<div class="row">
			<div class="col-md-4"><div class="mb-3"><label class="form-label">Customer *</label><select name="customer_id" class="form-select" required>
				<option value="">Select</option>
				<?php if ($customers) while($c=$customers->fetch_assoc()): ?>
				<option value="<?php echo (int)$c['id']; ?>" <?php echo ($invoice['customer_id']==$c['id'])?'selected':''; ?>><?php echo htmlspecialchars($c['customer_name']); ?></option>
				<?php endwhile; ?>
			</select></div></div>
			<div class="col-md-4"><div class="mb-3"><label class="form-label">Invoice # *</label><input name="invoice_no" class="form-control" value="<?php echo htmlspecialchars($invoice['invoice_no']); ?>" required></div></div>
			<div class="col-md-2"><div class="mb-3"><label class="form-label">Date *</label><input type="date" name="invoice_date" class="form-control" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" required></div></div>
			<div class="col-md-2"><div class="mb-3"><label class="form-label">Due *</label><input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($invoice['due_date']); ?>" required></div></div>
		</div>
		<div class="row">
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Subtotal *</label><input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" value="<?php echo htmlspecialchars($invoice['subtotal']); ?>" required></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Tax Code</label><select name="tax_code_id" id="tax_code_id" class="form-select">
				<option value="">None</option>
				<?php if ($tax_codes) while($t=$tax_codes->fetch_assoc()): ?>
				<option value="<?php echo (int)$t['id']; ?>" data-rate="<?php echo htmlspecialchars($t['rate']); ?>" <?php echo ($invoice['tax_code_id']==$t['id'])?'selected':''; ?>><?php echo htmlspecialchars($t['tax_code']); ?> (<?php echo number_format($t['rate'],2); ?>%)</option>
				<?php endwhile; ?>
			</select></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Tax Amount</label><input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" value="<?php echo htmlspecialchars($invoice['tax_amount']); ?>" readonly></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Total</label><input type="number" step="0.01" name="total" id="total" class="form-control" value="<?php echo htmlspecialchars($invoice['total']); ?>" readonly></div></div>
		</div>
		<div class="d-grid gap-2 d-md-flex justify-content-md-end"><button type="submit" class="btn btn-primary">Save</button></div>
	</form>
</div></div>
<script>
(function(){
	function recalc(){
		var subtotal=parseFloat(document.getElementById('subtotal').value||0);
		var taxSelect=document.getElementById('tax_code_id');
		var rate=0; if(taxSelect&&taxSelect.selectedOptions.length){var r=taxSelect.selectedOptions[0].getAttribute('data-rate'); rate=parseFloat(r||0);} 
		var tax = +(subtotal * rate/100).toFixed(2);
		document.getElementById('tax_amount').value = tax.toFixed(2);
		document.getElementById('total').value = (subtotal + tax).toFixed(2);
	}
	document.getElementById('subtotal').addEventListener('input', recalc);
	var sel=document.getElementById('tax_code_id'); if(sel){ sel.addEventListener('change', recalc);} 
	recalc();
})();
</script>
<?php include('../../includes/footer.php'); ?> 