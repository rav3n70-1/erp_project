<?php
$page_title = "Log New Invoice";
include('../../includes/header.php');

if (!has_permission('invoice_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');
$conn = connect_db();

// Fetch approved/completed POs to link to an invoice
$sql_pos = "SELECT po.id, po.po_number, s.supplier_name 
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            WHERE po.status IN ('Approved', 'Partially Delivered', 'Completed')
            ORDER BY po.id DESC";
$pos_result = $conn->query($sql_pos);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_invoices.php">Manage Invoices</a></li>
    <li class="breadcrumb-item active" aria-current="page">Log Invoice</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card">
    <div class="card-header"><h5>Invoice Details</h5></div>
    <div class="card-body">
        <form action="handle_log_invoice.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="po_id" class="form-label">Related Purchase Order <span class="text-danger">*</span></label>
                    <select class="form-select" id="po_id" name="po_id" required>
                        <option value="">Select a PO</option>
                        <?php while($po = $pos_result->fetch_assoc()): ?>
                            <option value="<?php echo $po['id']; ?>"><?php echo htmlspecialchars($po['po_number']) . ' - ' . htmlspecialchars($po['supplier_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="invoice_number" class="form-label">Invoice Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="invoice_date" class="form-label">Invoice Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="due_date" class="form-label">Due Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="due_date" required name="due_date">
                </div>
                 <div class="col-md-4 mb-3">
                    <label for="total_amount" class="form-label">Invoice Amount ($) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="invoice_file" class="form-label">Attach Invoice PDF/Image</label>
                <input class="form-control" type="file" id="invoice_file" name="invoice_file">
            </div>
            <button type="submit" class="btn btn-primary">Save Invoice</button>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>