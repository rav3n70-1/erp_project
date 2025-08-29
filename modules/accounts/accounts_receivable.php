<?php
$page_title = "Accounts Receivable";
include('../../includes/header.php');
include('../../includes/db.php');
include('../../includes/accounts_schema.php');

if (!has_permission('finance_view')) { header('Location: /erp_project/dashboard.php?status=access_denied'); exit(); }

$conn = connect_db();
ensure_accounts_schema($conn);

$res = $conn->query("SELECT i.*, c.customer_name FROM ar_invoices i JOIN ar_customers c ON c.id=i.customer_id ORDER BY i.invoice_date DESC");
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h1><?php echo $page_title; ?></h1>
  <?php if (has_permission('budget_manage')): ?>
  <div>
    <a href="/erp_project/modules/accounts/ar_add_customer.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>New Customer</a>
    <a href="/erp_project/modules/accounts/ar_add_invoice.php" class="btn btn-success"><i class="bi bi-receipt me-2"></i>New Invoice</a>
  </div>
  <?php endif; ?>
</div>

<div class="card">
  <div class="card-header"><h5>Customer Invoices</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-hover data-table">
        <thead class="table-dark">
          <tr>
            <th>Invoice #</th>
            <th>Customer</th>
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
            <td><?php echo htmlspecialchars($row['invoice_no']); ?></td>
            <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
            <td><?php echo htmlspecialchars($row['invoice_date']); ?></td>
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
              <a href="/erp_project/modules/accounts/ar_view_invoice.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-info" title="View Details"><i class="bi bi-eye"></i></a>
              <a href="/erp_project/modules/accounts/ar_record_payment.php?invoice_id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-success" title="Record Payment"><i class="bi bi-cash"></i></a>
              <a href="/erp_project/modules/accounts/ar_edit_invoice.php?id=<?php echo (int)$row['id']; ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil-square"></i></a>
            </td>
            <?php endif; ?>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="<?php echo has_permission('budget_manage') ? '9' : '8'; ?>">
              <div class="empty-state">
                <i class="bi bi-receipt"></i>
                <div class="mt-2">No AR invoices yet.</div>
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