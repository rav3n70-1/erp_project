<?php
$page_title = "View Journal Entry";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/journal_entries.php'); exit(); }

$stmt = $conn->prepare("SELECT * FROM journal_entries WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$entry = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$entry) { header('Location: /erp_project/modules/accounts/journal_entries.php'); exit(); }

$lines_stmt = $conn->prepare("SELECT * FROM journal_entry_lines WHERE journal_entry_id=?");
$lines_stmt->bind_param("i", $id);
$lines_stmt->execute();
$lines_res = $lines_stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<div>
		<a href="/erp_project/modules/accounts/journal_entries.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
		<?php if (has_permission('budget_manage')): ?>
		<a href="/erp_project/modules/accounts/edit_journal_entry.php?id=<?php echo (int)$entry['id']; ?>" class="btn btn-warning"><i class="bi bi-pencil-square me-2"></i>Edit</a>
		<?php endif; ?>
	</div>
</div>
<div class="card mb-3">
	<div class="card-body">
		<div class="row">
			<div class="col-md-3"><strong>Date:</strong> <?php echo htmlspecialchars($entry['entry_date']); ?></div>
			<div class="col-md-3"><strong>Reference:</strong> <?php echo htmlspecialchars($entry['reference']); ?></div>
			<div class="col-md-6"><strong>Memo:</strong> <?php echo htmlspecialchars($entry['memo']); ?></div>
		</div>
	</div>
</div>
<div class="card">
	<div class="card-header"><h5>Lines</h5></div>
	<div class="card-body">
		<div class="table-responsive">
			<table class="table">
				<thead><tr><th>Account</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th></tr></thead>
				<tbody>
					<?php $td=0; $tc=0; while($line=$lines_res->fetch_assoc()): $td+=$line['debit']; $tc+=$line['credit']; ?>
					<tr>
						<td><?php echo htmlspecialchars($line['account_code']); ?></td>
						<td><?php echo htmlspecialchars($line['description']); ?></td>
						<td class="text-end">$<?php echo number_format($line['debit'],2); ?></td>
						<td class="text-end">$<?php echo number_format($line['credit'],2); ?></td>
					</tr>
					<?php endwhile; ?>
				</tbody>
				<tfoot>
					<tr class="fw-semibold"><td colspan="2" class="text-end">Total</td><td class="text-end">$<?php echo number_format($td,2); ?></td><td class="text-end">$<?php echo number_format($tc,2); ?></td></tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 