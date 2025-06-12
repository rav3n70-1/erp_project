<?php
$page_title = "Purchase Order Details";
include('../../includes/header.php');
include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Purchase Order ID."); }

$po_id = $_GET['id'];
$conn = connect_db();

// Fetch PO data, joining with suppliers AND budgets
$sql_po = "SELECT 
            po.*, 
            s.supplier_name, 
            s.address,
            b.budget_name
           FROM purchase_orders po
           JOIN suppliers s ON po.supplier_id = s.id
           LEFT JOIN budgets b ON po.budget_id = b.id
           WHERE po.id = ?";
$stmt_po = $conn->prepare($sql_po);
$stmt_po->bind_param("i", $po_id);
$stmt_po->execute();
$result_po = $stmt_po->get_result();
if ($result_po->num_rows === 0) { die("Purchase Order not found."); }
$po = $result_po->fetch_assoc();

// Fetch all the line items for this PO
$sql_items = "SELECT i.*, p.sku, p.product_name 
              FROM po_items i
              JOIN products p ON i.product_id = p.id
              WHERE i.po_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $po_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// Fetch all delivery records for this PO
$sql_deliveries = "SELECT * FROM deliveries WHERE po_id = ? ORDER BY delivery_date DESC";
$stmt_deliveries = $conn->prepare($sql_deliveries);
$stmt_deliveries->bind_param("i", $po_id);
$stmt_deliveries->execute();
$result_deliveries = $stmt_deliveries->get_result();

// Fetch all payment records for this PO and calculate totals
$sql_payments = "SELECT * FROM payments WHERE po_id = ? ORDER BY payment_date DESC";
$stmt_payments = $conn->prepare($sql_payments);
$stmt_payments->bind_param("i", $po_id);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();
$total_paid = 0;
if ($result_payments->num_rows > 0) {
    while($payment = $result_payments->fetch_assoc()) { $total_paid += $payment['amount_paid']; }
    mysqli_data_seek($result_payments, 0);
}
$balance_due = $po['total_amount'] - $total_paid;
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_pos.php">Purchase Orders</a></li>
    <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($po['po_number']); ?></li>
  </ol>
</nav>

<?php
// Handle various status messages from redirects
if (isset($_GET['status']) || isset($_GET['status_updated'])) {
    $message = '';
    $alert_type = 'info';
     if (isset($_GET['status_updated']) && $_GET['status_updated'] == 'true') {
        $message = 'Purchase Order status has been updated successfully!';
        $alert_type = 'success';
    } elseif (isset($_GET['status'])) {
        if ($_GET['status'] == 'delivery_recorded') {
            $message = 'Delivery has been recorded successfully!';
            $alert_type = 'success';
        } elseif ($_GET['status'] == 'payment_recorded') {
            $message = 'Payment has been recorded successfully!';
            $alert_type = 'success';
        }
    }
    if ($message) {
        echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">
            '. htmlspecialchars($message) .'
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>';
    }
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3>Purchase Order: <?php echo htmlspecialchars($po['po_number']); ?></h3>
        <div class="btn-toolbar">
            <div class="btn-group me-2">
                <a href="print_po.php?id=<?php echo $po['id']; ?>" target="_blank" class="btn btn-secondary"><i class="bi bi-printer"></i> Print</a>
                <button type="button" class="btn btn-secondary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="visually-hidden">Toggle Dropdown</span>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="generate_po_pdf.php?id=<?php echo $po['id']; ?>">Download as PDF</a></li>
                </ul>
            </div>
            <div class="btn-group">
                <?php 
                if ($po['status'] == 'Pending' && has_permission('po_approve')): 
                ?>
                    <form action="handle_po_status.php" method="POST" class="d-inline">
                        <input type="hidden" name="po_id" value="<?php echo $po['id']; ?>">
                        <input type="hidden" name="new_status" value="Approved">
                        <button type="submit" class="btn btn-success"><i class="bi bi-check-circle"></i> Approve</button>
                    </form>
                    <form action="handle_po_status.php" method="POST" class="d-inline">
                        <input type="hidden" name="po_id" value="<?php echo $po['id']; ?>">
                        <input type="hidden" name="new_status" value="Rejected">
                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Reject</button>
                    </form>
                <?php endif; ?>

                <?php if (in_array($po['status'], ['Approved', 'Partially Delivered', 'Completed'])): ?>
                    <a href="/erp_project/modules/deliveries/record_delivery.php?po_id=<?php echo $po['id']; ?>" class="btn btn-primary">
                        <i class="bi bi-box-arrow-in-down"></i> Record Delivery
                    </a>
                    <?php if ($balance_due > 0 && has_permission('payment_manage')): ?>
                        <a href="/erp_project/modules/finance/record_payment.php?po_id=<?php echo $po['id']; ?>" class="btn btn-info text-white">
                            <i class="bi bi-cash-coin"></i> Record Payment
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="card-body">
        <ul class="nav nav-tabs" id="poTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button" role="tab" aria-controls="details-pane" aria-selected="true">Details</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="deliveries-tab" data-bs-toggle="tab" data-bs-target="#deliveries-pane" type="button" role="tab" aria-controls="deliveries-pane" aria-selected="false">Deliveries</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments-pane" type="button" role="tab" aria-controls="payments-pane" aria-selected="false">Payments</button>
            </li>
        </ul>

        <div class="tab-content" id="poTabContent">
            <div class="tab-pane fade show active" id="details-pane" role="tabpanel" aria-labelledby="details-tab">
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="row">
                             <div class="col-md-6">
                                <h5>Supplier Details:</h5>
                                <p><strong><?php echo htmlspecialchars($po['supplier_name']); ?></strong><br><?php echo nl2br(htmlspecialchars($po['address'])); ?></p>
                            </div>
                            <div class="col-md-6">
                                <h5>PO Information:</h5>
                                <p>
                                    <strong>Order Date:</strong> <?php echo date("F j, Y", strtotime($po['order_date'])); ?><br>
                                    <strong>Status:</strong> 
                                    <?php 
                                    $status = htmlspecialchars($po['status']);
                                    $badge_class = 'bg-secondary';
                                    if (in_array($status, ['Approved', 'Partially Delivered'])) $badge_class = 'bg-success';
                                    if ($status == 'Pending') $badge_class = 'bg-warning text-dark';
                                    if ($status == 'Rejected') $badge_class = 'bg-danger';
                                    if ($status == 'Completed') $badge_class = 'bg-info text-dark';
                                    echo '<span class="badge ' . $badge_class . '">' . $status . '</span>';
                                    ?><br>
                                    <strong>Budget:</strong> 
                                    <?php echo $po['budget_name'] ? htmlspecialchars($po['budget_name']) : '<span class="text-muted">N/A</span>'; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header"><h5>Financial Summary</h5></div>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between"><span>Total Amount:</span> <strong>$<?php echo number_format($po['total_amount'], 2); ?></strong></li>
                                <li class="list-group-item d-flex justify-content-between"><span>Amount Paid:</span> <span class="text-success">-$<?php echo number_format($total_paid, 2); ?></span></li>
                                <li class="list-group-item d-flex justify-content-between"><span>Balance Due:</span> <strong class="text-danger">$<?php echo number_format($balance_due, 2); ?></strong></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <h5 class="mt-4">Order Items</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>SKU</th><th>Product Name</th><th class="text-end">Quantity</th><th class="text-end">Unit Price</th><th class="text-end">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php mysqli_data_seek($result_items, 0); ?>
                            <?php while($item = $result_items->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['sku']); ?></td><td><?php echo htmlspecialchars($item['product_name']); ?></td><td class="text-end"><?php echo $item['quantity']; ?></td><td class="text-end">$<?php echo number_format($item['unit_price'], 2); ?></td><td class="text-end">$<?php echo number_format($item['total_price'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Grand Total:</th><th class="text-end h5">$<?php echo number_format($po['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <div class="tab-pane fade" id="deliveries-pane" role="tabpanel" aria-labelledby="deliveries-tab">
                <div class="mt-4">
                    <h4>Delivery History</h4>
                    <?php if ($result_deliveries->num_rows > 0): ?>
                        <?php while($delivery = $result_deliveries->fetch_assoc()): ?>
                            <div class="card mb-3">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Delivery Recorded on: <strong><?php echo date("F j, Y", strtotime($delivery['delivery_date'])); ?></strong></span>
                                    <?php if (!empty($delivery['grn_file_path'])): ?>
                                        <a href="/erp_project/<?php echo htmlspecialchars($delivery['grn_file_path']); ?>" class="btn btn-sm btn-outline-secondary" target="_blank"><i class="bi bi-paperclip"></i> View Attachment</a>
                                    <?php endif; ?>
                                </div>
                                <div class="card-body">
                                    <?php if(!empty($delivery['notes'])): ?><p><strong>Notes:</strong> <?php echo htmlspecialchars($delivery['notes']); ?></p><?php endif; ?>
                                    <table class="table table-sm">
                                        <thead><tr><th>SKU</th><th>Product</th><th class="text-end">Quantity Received</th></tr></thead>
                                        <tbody>
                                            <?php 
                                            $sql_delivery_items = "SELECT di.quantity_received, p.sku, p.product_name FROM delivery_items di JOIN po_items pi ON di.po_item_id = pi.id JOIN products p ON pi.product_id = p.id WHERE di.delivery_id = ?";
                                            $stmt_delivery_items = $conn->prepare($sql_delivery_items);
                                            $stmt_delivery_items->bind_param("i", $delivery['id']);
                                            $stmt_delivery_items->execute();
                                            $result_delivery_items = $stmt_delivery_items->get_result();
                                            while($d_item = $result_delivery_items->fetch_assoc()):
                                            ?>
                                                <tr><td><?php echo htmlspecialchars($d_item['sku']); ?></td><td><?php echo htmlspecialchars($d_item['product_name']); ?></td><td class="text-end"><?php echo $d_item['quantity_received']; ?></td></tr>
                                            <?php endwhile; $stmt_delivery_items->close(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">No deliveries have been recorded for this purchase order yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="tab-pane fade" id="payments-pane" role="tabpanel" aria-labelledby="payments-tab">
                <div class="mt-4">
                    <h4>Payment History</h4>
                    <table class="table table-striped">
                        <thead><tr><th>Payment Date</th><th>Payment Method</th><th>Notes / Reference</th><th class="text-end">Amount Paid</th></tr></thead>
                        <tbody>
                            <?php if ($result_payments->num_rows > 0): ?>
                                <?php while($payment = $result_payments->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date("F j, Y", strtotime($payment['payment_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($payment['notes']); ?></td>
                                    <td class="text-end">$<?php echo number_format($payment['amount_paid'], 2); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="text-center text-muted">No payments have been recorded for this purchase order yet.</td></tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-group-divider">
                            <tr><th colspan="3" class="text-end">Total Paid:</th><th class="text-end">$<?php echo number_format($total_paid, 2); ?></th></tr>
                            <tr><th colspan="3" class="text-end">Balance Due:</th><th class="text-end text-danger">$<?php echo number_format($balance_due, 2); ?></th></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>