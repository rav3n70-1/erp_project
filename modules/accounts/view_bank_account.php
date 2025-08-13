<?php
$page_title = "Bank Account";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/bank_accounts.php'); exit(); }

$stmt = $conn->prepare("SELECT * FROM bank_accounts WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$account = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$account) { header('Location: /erp_project/modules/accounts/bank_accounts.php'); exit(); }

$tx = $conn->prepare("SELECT * FROM bank_transactions WHERE bank_account_id=? ORDER BY txn_date DESC, id DESC");
$tx->bind_param("i", $id);
$tx->execute();
$tx_res = $tx->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<div>
		<a href="/erp_project/modules/accounts/bank_accounts.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
		<?php if (has_permission('budget_manage')): ?>
		<a href="/erp_project/modules/accounts/edit_bank_account.php?id=<?php echo (int)$account['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Edit</a>
		<a href="/erp_project/modules/accounts/record_bank_transaction.php?bank_account_id=<?php echo (int)$account['id']; ?>" class="btn btn-success"><i class="bi bi-plus-circle me-2"></i>Record Transaction</a>
		<?php endif; ?>
	</div>
</div>
<div class="card mb-3">
	<div class="card-body">
		<div class="row">
			<div class="col-md-4"><strong>Account:</strong> <?php echo htmlspecialchars($account['account_name']); ?></div>
			<div class="col-md-4"><strong>Bank:</strong> <?php echo htmlspecialchars($account['bank_name']); ?></div>
			<div class="col-md-4"><strong>Number:</strong> <?php echo htmlspecialchars($account['account_number']); ?></div>
		</div>
		<div class="row mt-2">
			<div class="col-md-4"><strong>Balance:</strong> $<?php echo number_format($account['balance'],2); ?></div>
			<div class="col-md-8"><strong>Created:</strong> <?php echo htmlspecialchars($account['created_at']); ?></div>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-header"><h5>Transactions</h5></div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table table-hover">
				<thead><tr><th>Date</th><th>Type</th><th class="text-end">Amount</th><th>Memo</th></tr></thead>
				<tbody>
					<?php if ($tx_res->num_rows): while($row=$tx_res->fetch_assoc()): ?>
					<tr>
						<td><?php echo htmlspecialchars($row['txn_date']); ?></td>
						<td><?php echo htmlspecialchars($row['type']); ?></td>
						<td class="text-end">$<?php echo number_format($row['amount'],2); ?></td>
						<td><?php echo htmlspecialchars($row['memo']); ?></td>
					</tr>
					<?php endwhile; else: ?>
					<tr><td colspan="4"><div class="empty-state"><i class="bi bi-list-ul"></i><div class="mt-2">No transactions.</div></div></td></tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 