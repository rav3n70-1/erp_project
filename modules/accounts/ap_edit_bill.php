<?php
$page_title = "Edit Bill";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('budget_manage')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0) { header('Location: /erp_project/modules/accounts/accounts_payable.php'); exit(); }
$vendors = $conn->query("SELECT id, vendor_name FROM ap_vendors ORDER BY vendor_name");
$tax_codes = $conn->query("SELECT id, tax_code, rate FROM tax_codes WHERE is_active=1 ORDER BY tax_code");
$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
	$vendor_id = (int)$_POST['vendor_id'];
	$bill_no = trim($_POST['bill_no']);
	$bill_date = $_POST['bill_date'];
	$due_date = $_POST['due_date'];
	$subtotal = floatval($_POST['subtotal']);
	$tax_code_id = !empty($_POST['tax_code_id']) ? (int)$_POST['tax_code_id'] : NULL;
	$tax_amount = floatval($_POST['tax_amount']);
	$total = floatval($_POST['total']);
	if ($vendor_id<=0 || $bill_no==='' || $subtotal<0 || $total<0) { $error='Please fill required fields and amounts must be valid.'; }
	if (!$error) {
		$stmt=$conn->prepare("UPDATE ap_bills SET vendor_id=?, bill_no=?, bill_date=?, due_date=?, subtotal=?, tax_code_id=?, tax_amount=?, total=? WHERE id=?");
		$stmt->bind_param("isssdddii", $vendor_id, $bill_no, $bill_date, $due_date, $subtotal, $tax_code_id, $tax_amount, $total, $id);
		if ($stmt->execute()) { $success='Saved.'; } else { $error='Failed: '.$conn->error; }
		$stmt->close();
	}
}
// Load bill
$st=$conn->prepare("SELECT * FROM ap_bills WHERE id=?");
$st->bind_param("i", $id);
$st->execute();
$bill=$st->get_result()->fetch_assoc();
$st->close();
if (!$bill) { header('Location: /erp_project/modules/accounts/accounts_payable.php'); exit(); }
?>
<div class="d-flex justify-content-between align-items-center mb-4"><h1><?php echo $page_title; ?></h1><a href="/erp_project/modules/accounts/ap_view_bill.php?id=<?php echo (int)$id; ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a></div>
<?php if ($success): ?><div class="alert alert-success alert-dismissible fade show" role="alert"><?php echo $success; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-danger alert-dismissible fade show" role="alert"><?php echo $error; ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
<div class="card"><div class="card-header"><h5>Bill</h5></div><div class="card-body">
	<form method="POST">
		<div class="row">
			<div class="col-md-4"><div class="mb-3"><label class="form-label">Vendor *</label><select name="vendor_id" class="form-select" required>
				<option value="">Select</option>
				<?php if ($vendors) while($v=$vendors->fetch_assoc()): ?>
				<option value="<?php echo (int)$v['id']; ?>" <?php echo ($bill['vendor_id']==$v['id'])?'selected':''; ?>><?php echo htmlspecialchars($v['vendor_name']); ?></option>
				<?php endwhile; ?>
			</select></div></div>
			<div class="col-md-4"><div class="mb-3"><label class="form-label">Bill # *</label><input name="bill_no" class="form-control" value="<?php echo htmlspecialchars($bill['bill_no']); ?>" required></div></div>
			<div class="col-md-2"><div class="mb-3"><label class="form-label">Date *</label><input type="date" name="bill_date" class="form-control" value="<?php echo htmlspecialchars($bill['bill_date']); ?>" required></div></div>
			<div class="col-md-2"><div class="mb-3"><label class="form-label">Due *</label><input type="date" name="due_date" class="form-control" value="<?php echo htmlspecialchars($bill['due_date']); ?>" required></div></div>
		</div>
		<div class="row">
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Subtotal *</label><input type="number" step="0.01" name="subtotal" id="subtotal" class="form-control" value="<?php echo htmlspecialchars($bill['subtotal']); ?>" required></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Tax Code</label><select name="tax_code_id" id="tax_code_id" class="form-select">
				<option value="">None</option>
				<?php if ($tax_codes) while($t=$tax_codes->fetch_assoc()): ?>
				<option value="<?php echo (int)$t['id']; ?>" data-rate="<?php echo htmlspecialchars($t['rate']); ?>" <?php echo ($bill['tax_code_id']==$t['id'])?'selected':''; ?>><?php echo htmlspecialchars($t['tax_code']); ?> (<?php echo number_format($t['rate'],2); ?>%)</option>
				<?php endwhile; ?>
			</select></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Tax Amount</label><input type="number" step="0.01" name="tax_amount" id="tax_amount" class="form-control" value="<?php echo htmlspecialchars($bill['tax_amount']); ?>" readonly></div></div>
			<div class="col-md-3"><div class="mb-3"><label class="form-label">Total</label><input type="number" step="0.01" name="total" id="total" class="form-control" value="<?php echo htmlspecialchars($bill['total']); ?>" readonly></div></div>
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