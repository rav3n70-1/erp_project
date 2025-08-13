<?php
$page_title = "New Journal Entry";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { 
	header('Location: /erp_project/index.php?status=access_denied'); 
	exit(); 
}

$conn = connect_db();
ensure_accounts_schema($conn);

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$entry_date = $_POST['entry_date'];
	$reference = trim($_POST['reference'] ?? '');
	$memo = trim($_POST['memo'] ?? '');
	$account_code = $_POST['account_code'] ?? [];
	$description = $_POST['description'] ?? [];
	$debit = $_POST['debit'] ?? [];
	$credit = $_POST['credit'] ?? [];

	$lines = [];
	$total_debits = 0.0;
	$total_credits = 0.0;
	for ($i = 0; $i < count($account_code); $i++) {
		$ac = trim($account_code[$i] ?? '');
		$desc = trim($description[$i] ?? '');
		$dr = floatval($debit[$i] ?? 0);
		$cr = floatval($credit[$i] ?? 0);
		if ($ac === '' && $dr == 0 && $cr == 0) { continue; }
		if ($ac === '') { $error = 'Account code is required for all lines with an amount.'; break; }
		if ($dr > 0 && $cr > 0) { $error = 'A line cannot have both debit and credit.'; break; }
		$lines[] = [$ac, $desc, $dr, $cr];
		$total_debits += $dr;
		$total_credits += $cr;
	}
	if (!$error) {
		if (count($lines) < 2) {
			$error = 'At least two lines are required.';
		} elseif (abs($total_debits - $total_credits) > 0.0001 || $total_debits <= 0) {
			$error = 'Debits must equal credits and be greater than zero.';
		}
	}
	if (!$error) {
		$stmt = $conn->prepare("INSERT INTO journal_entries (entry_date, reference, memo) VALUES (?, ?, ?)");
		$stmt->bind_param("sss", $entry_date, $reference, $memo);
		if ($stmt->execute()) {
			$journal_id = $conn->insert_id;
			$stmt->close();
			$line_stmt = $conn->prepare("INSERT INTO journal_entry_lines (journal_entry_id, account_code, description, debit, credit) VALUES (?, ?, ?, ?, ?)");
			for ($i=0; $i<count($lines); $i++) {
				list($ac,$desc,$dr,$cr) = $lines[$i];
				$line_stmt->bind_param("issdd", $journal_id, $ac, $desc, $dr, $cr);
				$line_stmt->execute();
			}
			$line_stmt->close();
			$success = 'Journal entry created successfully.';
			// Clear form
			$entry_date = $reference = $memo = '';
			$account_code = $description = $debit = $credit = [];
		} else {
			$error = 'Failed to save journal entry: ' . $conn->error;
		}
	}
}
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/journal_entries.php" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
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
	<div class="card-header"><h5>Journal Entry</h5></div>
	<div class="card-body">
		<form method="POST">
			<div class="row">
				<div class="col-md-3">
					<div class="mb-3"><label class="form-label">Date *</label><input type="date" name="entry_date" class="form-control" value="<?php echo htmlspecialchars($entry_date ?? date('Y-m-d')); ?>" required></div>
				</div>
				<div class="col-md-3">
					<div class="mb-3"><label class="form-label">Reference</label><input type="text" name="reference" class="form-control" value="<?php echo htmlspecialchars($reference ?? ''); ?>"></div>
				</div>
				<div class="col-md-6">
					<div class="mb-3"><label class="form-label">Memo</label><input type="text" name="memo" class="form-control" value="<?php echo htmlspecialchars($memo ?? ''); ?>"></div>
				</div>
			</div>
			<div class="table-responsive">
				<table class="table align-middle">
					<thead>
						<tr><th>Account Code</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th></tr>
					</thead>
					<tbody>
						<?php for ($i=0; $i<6; $i++): ?>
						<tr>
							<td><input type="text" name="account_code[]" class="form-control" value="<?php echo htmlspecialchars($account_code[$i] ?? ''); ?>" placeholder="e.g., 1010"></td>
							<td><input type="text" name="description[]" class="form-control" value="<?php echo htmlspecialchars($description[$i] ?? ''); ?>"></td>
							<td><input type="number" step="0.01" name="debit[]" class="form-control text-end" value="<?php echo htmlspecialchars($debit[$i] ?? ''); ?>"></td>
							<td><input type="number" step="0.01" name="credit[]" class="form-control text-end" value="<?php echo htmlspecialchars($credit[$i] ?? ''); ?>"></td>
						</tr>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end">
				<button type="submit" class="btn btn-primary">Save Entry</button>
			</div>
		</form>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 