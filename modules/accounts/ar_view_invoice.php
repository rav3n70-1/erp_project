<?php
$page_title = "Invoice";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');
if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }
$conn = connect_db();
ensure_accounts_schema($conn);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id<=0) { header('Location: /erp_project/modules/accounts/accounts_receivable.php'); exit(); }
$inv=$conn->prepare("SELECT i.*, c.customer_name FROM ar_invoices i JOIN ar_customers c ON c.id=i.customer_id WHERE i.id=?");
$inv->bind_param("i", $id);
$inv->execute();
$invoice=$inv->get_result()->fetch_assoc();
$inv->close();
if (!$invoice) { header('Location: /erp_project/modules/accounts/accounts_receivable.php'); exit(); }
$pay=$conn->prepare("SELECT * FROM ar_payments WHERE invoice_id=? ORDER BY payment_date DESC, id DESC");
$pay->bind_param("i", $id);
$pay->execute();
$payments=$pay->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<div>
		<a href="/erp_project/modules/accounts/accounts_receivable.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
		<?php if (has_permission('budget_manage')): ?>
		<a href="/erp_project/modules/accounts/ar_record_payment.php?invoice_id=<?php echo (int)$invoice['id']; ?>" class="btn btn-success"><i class="bi bi-cash me-2"></i>Record Payment</a>
		<a href="/erp_project/modules/accounts/ar_edit_invoice.php?id=<?php echo (int)$invoice['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Edit</a>
		<?php endif; ?>
	</div>
</div>
<div class="card mb-3"><div class="card-body">
	<div class="row">
		<div class="col-md-3"><strong>Invoice #</strong> <?php echo htmlspecialchars($invoice['invoice_no']); ?></div>
		<div class="col-md-3"><strong>Customer</strong> <?php echo htmlspecialchars($invoice['customer_name']); ?></div>
		<div class="col-md-3"><strong>Date</strong> <?php echo htmlspecialchars($invoice['invoice_date']); ?></div>
		<div class="col-md-3"><strong>Due</strong> <?php echo htmlspecialchars($invoice['due_date']); ?></div>
	</div>
	<div class="row mt-2">
		<div class="col-md-3"><strong>Subtotal</strong> ৳<?php echo number_format($invoice['subtotal'],2); ?></div>
		<div class="col-md-3"><strong>Tax</strong> ৳<?php echo number_format($invoice['tax_amount'],2); ?></div>
		<div class="col-md-3"><strong>Total</strong> ৳<?php echo number_format($invoice['total'],2); ?></div>
		<div class="col-md-3"><strong>Status</strong> <span class="badge bg-<?php echo $invoice['status']==='Paid'?'success':($invoice['status']==='Overdue'?'danger':'warning'); ?>"><?php echo htmlspecialchars($invoice['status']); ?></span></div>
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