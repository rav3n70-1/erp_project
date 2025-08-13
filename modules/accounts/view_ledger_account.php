<?php
$page_title = "Ledger Account Details";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$account_code = isset($_GET['account_code']) ? trim($_GET['account_code']) : '';
$from_date = isset($_GET['from_date']) && $_GET['from_date'] !== '' ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) && $_GET['to_date'] !== '' ? $_GET['to_date'] : '';
if ($account_code === '') { header('Location: /erp_project/modules/accounts/general_ledger.php'); exit(); }

$sql = "SELECT e.entry_date, e.reference, e.memo, l.description, l.debit, l.credit
        FROM journal_entry_lines l
        JOIN journal_entries e ON e.id = l.journal_entry_id
        WHERE l.account_code = ?";
$params = [$account_code];
$types = 's';
if ($from_date !== '') { $sql .= ' AND e.entry_date >= ?'; $types .= 's'; $params[] = $from_date; }
if ($to_date !== '') { $sql .= ' AND e.entry_date <= ?'; $types .= 's'; $params[] = $to_date; }
$sql .= ' ORDER BY e.entry_date, e.id, l.id';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$res = $stmt->get_result();
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <a href="/erp_project/modules/accounts/general_ledger.php?from_date=<?php echo urlencode($from_date); ?>&to_date=<?php echo urlencode($to_date); ?>&account_code=<?php echo urlencode($account_code); ?>" class="btn btn-secondary"><i class="bi bi-arrow-left me-2"></i>Back</a>
</div>
<div class="card mb-3">
  <div class="card-body">
    <strong>Account:</strong> <?php echo htmlspecialchars($account_code); ?>
    <?php if ($from_date || $to_date): ?>
      <span class="ms-3"><strong>Range:</strong> <?php echo htmlspecialchars($from_date ?: '...'); ?> to <?php echo htmlspecialchars($to_date ?: '...'); ?></span>
    <?php endif; ?>
  </div>
</div>
<div class="card">
  <div class="card-header"><h5>Transactions</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover">
        <thead class="table-dark">
          <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Entry Memo</th>
            <th>Line Description</th>
            <th class="text-end">Debit</th>
            <th class="text-end">Credit</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && $res->num_rows): $td=0; $tc=0; while($row=$res->fetch_assoc()): $td += (float)$row['debit']; $tc += (float)$row['credit']; ?>
          <tr>
            <td><?php echo htmlspecialchars($row['entry_date']); ?></td>
            <td><?php echo htmlspecialchars($row['reference']); ?></td>
            <td><?php echo htmlspecialchars($row['memo']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td class="text-end">৳<?php echo number_format($row['debit'],2); ?></td>
            <td class="text-end">৳<?php echo number_format($row['credit'],2); ?></td>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="6">
              <div class="empty-state">
                <i class="bi bi-journal"></i>
                <div class="mt-2">No transactions found for this account.</div>
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
        <?php if ($res && $res->num_rows): ?>
        <tfoot>
          <tr class="fw-semibold">
            <td colspan="4" class="text-end">Total</td>
            <td class="text-end">৳<?php echo number_format($td,2); ?></td>
            <td class="text-end">৳<?php echo number_format($tc,2); ?></td>
          </tr>
        </tfoot>
        <?php endif; ?>
      </table>
    </div>
  </div>
</div>
<?php include('../../includes/footer.php'); ?> 