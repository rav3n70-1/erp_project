<?php
$page_title = "Accounts Payable";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$res = $conn->query("SELECT b.*, v.vendor_name FROM ap_bills b JOIN ap_vendors v ON v.id=b.vendor_id ORDER BY b.bill_date DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <div>
    <a href="/erp_project/modules/accounts/ap_add_vendor.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Vendor</a>
    <a href="/erp_project/modules/accounts/ap_add_bill.php" class="btn btn-success"><i class="bi bi-receipt-cutoff me-2"></i>New Bill</a>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h5>Vendor Bills</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr>
            <th>Bill #</th>
            <th>Vendor</th>
            <th>Date</th>
            <th>Due</th>
            <th class="text-end">Total</th>
            <th class="text-end">Paid</th>
            <th class="text-end">Balance</th>
            <th>Status</th>
            <?php if (has_permission('budget_manage')): ?>
            <th>Actions</th>
            <?php endif; ?>
          </tr>
        </thead>
        <tbody>
          <?php if ($res && $res->num_rows): while($row=$res->fetch_assoc()): $bal = (float)$row['total'] - (float)$row['paid']; ?>
          <tr>
            <td><?php echo htmlspecialchars($row['bill_no']); ?></td>
            <td><?php echo htmlspecialchars($row['vendor_name']); ?></td>
            <td><?php echo htmlspecialchars($row['bill_date']); ?></td>
            <td><?php echo htmlspecialchars($row['due_date']); ?></td>
            <td class="text-end">৳<?php echo number_format($row['total'],2); ?></td>
            <td class="text-end">৳<?php echo number_format($row['paid'],2); ?></td>
            <td class="text-end">৳<?php echo number_format($bal,2); ?></td>
            <td>
              <span class="badge bg-<?php echo $row['status']==='Paid'?'success':($row['status']==='Overdue'?'danger':'warning'); ?>">
                <?php echo htmlspecialchars($row['status']); ?>
              </span>
            </td>
            <?php if (has_permission('budget_manage')): ?>
            <td>
              <a href="/erp_project/modules/accounts/ap_view_bill.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-info" title="View Details"><i class="bi bi-eye"></i></a>
              <a href="/erp_project/modules/accounts/ap_record_payment.php?bill_id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-success" title="Record Payment"><i class="bi bi-cash"></i></a>
              <a href="/erp_project/modules/accounts/ap_edit_bill.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="<?php echo has_permission('budget_manage') ? '9' : '8'; ?>">
              <div class="empty-state">
                <i class="bi bi-receipt-cutoff"></i>
                <div class="mt-2">No AP bills yet.</div>
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