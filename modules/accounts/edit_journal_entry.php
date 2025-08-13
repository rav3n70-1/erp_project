<?php
$page_title = "Edit Journal Entry";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('budget_manage')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: /erp_project/modules/accounts/journal_entries.php'); exit(); }

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
	$total_debits=0; $total_credits=0;
	for ($i=0; $i<count($account_code); $i++) {
		$ac = trim($account_code[$i] ?? '');
		$desc = trim($description[$i] ?? '');
		$dr = floatval($debit[$i] ?? 0);
		$cr = floatval($credit[$i] ?? 0);
		if ($ac === '' && $dr == 0 && $cr == 0) continue;
		if ($ac === '') { $error = 'Account code required on all non-empty lines.'; break; }
		if ($dr > 0 && $cr > 0) { $error = 'A line cannot have both debit and credit.'; break; }
		$lines[] = [$ac,$desc,$dr,$cr];
		$total_debits += $dr; $total_credits += $cr;
	}
	if (!$error) {
		if (count($lines) < 2 || abs($total_debits-$total_credits)>0.0001 || $total_debits<=0) {
			$error = 'Debits must equal credits and be greater than zero (at least two lines).';
		}
	}
	if (!$error) {
		$stmt = $conn->prepare("UPDATE journal_entries SET entry_date=?, reference=?, memo=? WHERE id=?");
		$stmt->bind_param("sssi", $entry_date, $reference, $memo, $id);
		if ($stmt->execute()) {
			$stmt->close();
			$conn->query("DELETE FROM journal_entry_lines WHERE journal_entry_id=".(int)$id);
			$line_stmt = $conn->prepare("INSERT INTO journal_entry_lines (journal_entry_id, account_code, description, debit, credit) VALUES (?, ?, ?, ?, ?)");
			for ($i=0; $i<count($lines); $i++) {
				list($ac,$desc,$dr,$cr) = $lines[$i];
				$line_stmt->bind_param("issdd", $id, $ac, $desc, $dr, $cr);
				$line_stmt->execute();
			}
			$line_stmt->close();
			$success = 'Changes saved.';
		} else {
			$error = 'Failed to save: '.$conn->error;
		}
	}
}

// Load entry and lines
$stmt = $conn->prepare("SELECT * FROM journal_entries WHERE id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$entry = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$entry) { header('Location: /erp_project/modules/accounts/journal_entries.php'); exit(); }

$lines_stmt = $conn->prepare("SELECT * FROM journal_entry_lines WHERE journal_entry_id=?");
$lines_stmt->bind_param("i", $id);
$lines_stmt->execute();
$lines = $lines_stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$lines_stmt->close();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
	<h1><?php echo $page_title; ?></h1>
	<a href="/erp_project/modules/accounts/view_journal_entry.php?id=<?php echo (int)$id; ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
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
				<div class="col-md-3"><div class="mb-3"><label class="form-label">Date *</label><input type="date" name="entry_date" class="form-control" value="<?php echo htmlspecialchars($entry['entry_date']); ?>" required></div></div>
				<div class="col-md-3"><div class="mb-3"><label class="form-label">Reference</label><input type="text" name="reference" class="form-control" value="<?php echo htmlspecialchars($entry['reference']); ?>"></div></div>
				<div class="col-md-6"><div class="mb-3"><label class="form-label">Memo</label><input type="text" name="memo" class="form-control" value="<?php echo htmlspecialchars($entry['memo']); ?>"></div></div>
			</div>
			<div class="table-responsive">
				<table class="table align-middle">
					<thead><tr><th>Account Code</th><th>Description</th><th class="text-end">Debit</th><th class="text-end">Credit</th></tr></thead>
					<tbody>
						<?php $maxRows = max(6, count($lines)+2); for ($i=0; $i<$maxRows; $i++): $ln=$lines[$i] ?? ['account_code'=>'','description'=>'','debit'=>'','credit'=>'']; ?>
						<tr>
							<td><input type="text" name="account_code[]" class="form-control" value="<?php echo htmlspecialchars($ln['account_code']); ?>"></td>
							<td><input type="text" name="description[]" class="form-control" value="<?php echo htmlspecialchars($ln['description']); ?>"></td>
							<td><input type="number" step="0.01" name="debit[]" class="form-control text-end" value="<?php echo htmlspecialchars($ln['debit']); ?>"></td>
							<td><input type="number" step="0.01" name="credit[]" class="form-control text-end" value="<?php echo htmlspecialchars($ln['credit']); ?>"></td>
						</tr>
						<?php endfor; ?>
					</tbody>
				</table>
			</div>
			<div class="d-grid gap-2 d-md-flex justify-content-md-end">
				<button type="submit" class="btn btn-primary">Save Changes</button>
			</div>
		</form>
	</div>
</div>
<?php include('../../includes/footer.php'); ?> 