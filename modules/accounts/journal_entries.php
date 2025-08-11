<?php
$page_title = "Journal Entries";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$res = $conn->query("SELECT * FROM journal_entries ORDER BY entry_date DESC, id DESC");
?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <a href="#" class="btn btn-primary disabled"><i class="bi bi-plus-circle me-2"></i>New Journal Entry</a>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h5>Entries</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr>
            <th>Date</th>
            <th>Reference</th>
            <th>Memo</th>
            <th>Created</th>
            <?php if (has_permission('budget_manage')): ?>
            <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && $res->num_rows): while($row=$res->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($row['entry_date']); ?></td>
            <td><?php echo htmlspecialchars($row['reference']); ?></td>
            <td><?php echo htmlspecialchars($row['memo']); ?></td>
            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
            <?php if (has_permission('budget_manage')): ?>
            <td>
              <a href="#" class="btn btn-sm btn-info disabled" title="View Details"><i class="bi bi-eye"></i></a>
              <a href="#" class="btn btn-sm btn-warning disabled" title="Edit"><i class="bi bi-pencil-square"></i></a>
              <a href="#" class="btn btn-sm btn-danger disabled" title="Delete"><i class="bi bi-trash"></i></a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="<?php echo has_permission('budget_manage') ? '5' : '4'; ?>">
              <div class="empty-state">
                <i class="bi bi-journal-text"></i>
                <div class="mt-2">No journal entries yet.</div>
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include('../../includes/footer.php'); ?> 