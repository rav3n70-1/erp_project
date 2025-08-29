<?php
$page_title = "Bill";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0) { header('Location: /erp_project/modules/accounts/accounts_payable.php'); exit(); }
$st=$conn->prepare("SELECT b.*, v.vendor_name FROM ap_bills b JOIN ap_vendors v ON v.id=b.vendor_id WHERE b.id=?");
$st->bind_param("i", $id);
$st->execute();
$bill=$st->get_result()->fetch_assoc();
$st->close();
if (!$bill) { header('Location: /erp_project/modules/accounts/accounts_payable.php'); exit(); }
$pay=$conn->prepare("SELECT * FROM ap_payments WHERE bill_id=? ORDER BY payment_date DESC, id DESC");
$pay->bind_param("i", $id);
$pay->execute();
$payments=$pay->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<div>
		<a href="/erp_project/modules/accounts/accounts_payable.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
		<?php if (has_permission('budget_manage')): ?>
		<a href="/erp_project/modules/accounts/ap_record_payment.php?bill_id=<?php echo (int)$bill['id']; ?>" class="btn btn-success"><i class="bi bi-cash me-2"></i>Record Payment</a>
		<a href="/erp_project/modules/accounts/ap_edit_bill.php?id=<?php echo (int)$bill['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Edit</a>
		<?php endif; ?>
	</div>
</div>
<div class="card mb-3"><div class="card-body">
	<div class="row">
		<div class="col-md-3"><strong>Bill #</strong> <?php echo htmlspecialchars($bill['bill_no']); ?></div>
		<div class="col-md-3"><strong>Vendor</strong> <?php echo htmlspecialchars($bill['vendor_name']); ?></div>
		<div class="col-md-3"><strong>Date</strong> <?php echo htmlspecialchars($bill['bill_date']); ?></div>
		<div class="col-md-3"><strong>Due</strong> <?php echo htmlspecialchars($bill['due_date']); ?></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3"><strong>Subtotal</strong> ৳<?php echo number_format($bill['subtotal'],2); ?></div>
		<div class="col-md-3"><strong>Tax</strong> ৳<?php echo number_format($bill['tax_amount'],2); ?></div>
		<div class="col-md-3"><strong>Total</strong> ৳<?php echo number_format($bill['total'],2); ?></div>
		<div class="col-md-3"><strong>Status</strong> <span class="badge bg-<?php echo $bill['status']==='Paid'?'success':($bill['status']==='Overdue'?'danger':'warning'); ?>"><?php echo htmlspecialchars($bill['status']); ?></span></div>
	</div>
</div></div>
<div class="card"><div class="card-header"><h5>Payments</h5></div><div class="card-body">
	<div class="table-responsive">
		<table class="table">
			<thead><tr><th>Date</th><th class="text-end">Amount</th><th>Method</th><th>Reference</th></tr></thead>
			<tbody>
				<?php if ($payments->num_rows): while($p=$payments->fetch_assoc()): ?>
				<tr><td><?php echo htmlspecialchars($p['payment_date']); ?></td><td class="text-end">৳<?php echo number_format($p['amount'],2); ?></td><td><?php echo htmlspecialchars($p['method']); ?></td><td><?php echo htmlspecialchars($p['reference']); ?></td></tr>
				<?php endwhile; else: ?>
				<tr><td colspan="4"><div class="empty-state"><i class="bi bi-cash"></i><div class="mt-2">No payments.</div></div></td></tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>
</div></div>
<?php include('../../includes/footer.php'); ?> 