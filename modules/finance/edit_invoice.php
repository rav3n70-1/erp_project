<?php
$page_title = "Edit Invoice";
include('../../includes/header.php');

if (!has_permission('invoice_edit')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Invoice ID."); }

$invoice_id = $_GET['id'];
$conn = connect_db();

// Fetch existing invoice data
$sql_invoice = "SELECT * FROM invoices WHERE id = ?";
$stmt_invoice = $conn->prepare($sql_invoice);
$stmt_invoice->bind_param("i", $invoice_id);
$stmt_invoice->execute();
$result_invoice = $stmt_invoice->get_result();
if ($result_invoice->num_rows === 0) { die("Invoice not found."); }
$invoice = $result_invoice->fetch_assoc();

// Fetch POs for the dropdown
$sql_pos = "SELECT po.id, po.po_number, s.supplier_name 
            FROM purchase_orders po
            JOIN suppliers s ON po.supplier_id = s.id
            ORDER BY po.id DESC";
$pos_result = $conn->query($sql_pos);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_invoices.php">Manage Invoices</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit Invoice</li>
  </ol>
</nav>

<h1 class="mt-4">Edit Invoice #<?php echo htmlspecialchars($invoice['invoice_number']); ?></h1>

<div class="card">
    <div class="card-header"><h5>Invoice Details</h5></div>
    <div class="card-body">
        <form action="handle_edit_invoice.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="po_id" class="form-label">Related PO</label>
                    <select class="form-select" id="po_id" name="po_id" required>
                        <?php while($po = $pos_result->fetch_assoc()): 
                            $selected = ($po['id'] == $invoice['po_id']) ? 'selected' : '';
                        ?>
                            <option value="<?php echo $po['id']; ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($po['po_number']) . ' - ' . htmlspecialchars($po['supplier_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="invoice_number" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" id="invoice_number" name="invoice_number" value="<?php echo htmlspecialchars($invoice['invoice_number']); ?>" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="invoice_date" class="form-label">Invoice Date</label>
                    <input type="date" class="form-control" id="invoice_date" name="invoice_date" value="<?php echo htmlspecialchars($invoice['invoice_date']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" value="<?php echo htmlspecialchars($invoice['due_date']); ?>" required>
                </div>
                 <div class="col-md-4 mb-3">
                    <label for="total_amount" class="form-label">Invoice Amount ($)</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" value="<?php echo htmlspecialchars($invoice['total_amount']); ?>" step="0.01" min="0" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="invoice_file" class="form-label">Change Invoice File (Optional)</label>
                <input class="form-control" type="file" id="invoice_file" name="invoice_file">
                <?php if (!empty($invoice['file_path'])): ?>
                    <div class="form-text">Current file: <a href="/erp_project/<?php echo htmlspecialchars($invoice['file_path']); ?>" target="_blank">View</a></div>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="view_invoices.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>