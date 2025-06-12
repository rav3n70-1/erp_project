<?php
$page_title = "Record Payment";
include('../../includes/header.php');
include('../../includes/db.php');

// 1. Check for a valid PO ID
if (!isset($_GET['po_id']) || !is_numeric($_GET['po_id'])) {
    die("Invalid Purchase Order ID.");
}

$po_id = $_GET['po_id'];
$conn = connect_db();

// 2. Fetch PO and Supplier details
$sql_po = "SELECT po.po_number, po.total_amount, s.supplier_name 
           FROM purchase_orders po 
           JOIN suppliers s ON po.supplier_id = s.id 
           WHERE po.id = ?";
$stmt_po = $conn->prepare($sql_po);
$stmt_po->bind_param("i", $po_id);
$stmt_po->execute();
$result_po = $stmt_po->get_result();
if ($result_po->num_rows === 0) {
    die("Purchase Order not found.");
}
$po = $result_po->fetch_assoc();

// 3. Calculate the total amount already paid for this PO
$sql_paid = "SELECT SUM(amount_paid) AS total_paid FROM payments WHERE po_id = ?";
$stmt_paid = $conn->prepare($sql_paid);
$stmt_paid->bind_param("i", $po_id);
$stmt_paid->execute();
$total_paid = $stmt_paid->get_result()->fetch_assoc()['total_paid'] ?? 0;

$balance_due = $po['total_amount'] - $total_paid;
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="/erp_project/modules/purchase_orders/view_pos.php">Purchase Orders</a></li>
    <li class="breadcrumb-item"><a href="/erp_project/modules/purchase_orders/view_po_details.php?id=<?php echo $po_id; ?>"><?php echo htmlspecialchars($po['po_number']); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Payment</li>
  </ol>
</nav>

<h1 class="mt-4">Record Payment for PO: <?php echo htmlspecialchars($po['po_number']); ?></h1>

<div class="row">
    <div class="col-md-8">
        <form action="handle_record_payment.php" method="POST">
            <input type="hidden" name="po_id" value="<?php echo $po_id; ?>">
            <div class="card">
                <div class="card-header"><h5>Payment Details</h5></div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_date" class="form-label">Payment Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="amount_paid" class="form-label">Amount Paid ($) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="amount_paid" name="amount_paid" step="0.01" min="0.01" max="<?php echo $balance_due; ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="payment_method" class="form-label">Payment Method <span class="text-danger">*</span></label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="Credit">Credit</option>
                                <option value="Cash">Cash</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes / Transaction Reference</label>
                        <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-success btn-lg">Save Payment</button>
                <a href="/erp_project/modules/purchase_orders/view_po_details.php?id=<?php echo $po_id; ?>" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </form>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Financial Summary</h5></div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between">
                    <span>Total PO Amount:</span>
                    <strong>$<?php echo number_format($po['total_amount'], 2); ?></strong>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Amount Already Paid:</span>
                    <span class="text-success">-$<?php echo number_format($total_paid, 2); ?></span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                    <span>Balance Due:</span>
                    <strong class="text-danger">$<?php echo number_format($balance_due, 2); ?></strong>
                </li>
            </ul>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>