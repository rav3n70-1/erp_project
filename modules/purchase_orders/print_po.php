<?php
// This is a print-friendly view of a Purchase Order
include_once('../../includes/session_check.php');
include_once('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid PO ID."); }

$po_id = $_GET['id'];
$conn = connect_db();

// --- Fetch all necessary data for the PO ---

// 1. Fetch main PO data and supplier details
$sql_po = "SELECT po.*, s.supplier_name, s.address 
           FROM purchase_orders po
           JOIN suppliers s ON po.supplier_id = s.id
           WHERE po.id = ?";
$stmt_po = $conn->prepare($sql_po);
$stmt_po->bind_param("i", $po_id);
$stmt_po->execute();
$result_po = $stmt_po->get_result();
if ($result_po->num_rows === 0) { die("Purchase Order not found."); }
$po = $result_po->fetch_assoc();

// 2. Fetch all line items for this PO
$sql_items = "SELECT i.*, p.sku, p.product_name 
              FROM po_items i
              JOIN products p ON i.product_id = p.id
              WHERE i.po_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $po_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();

// 3. Fetch all delivery records for this PO
$sql_deliveries = "SELECT * FROM deliveries WHERE po_id = ? ORDER BY delivery_date ASC";
$stmt_deliveries = $conn->prepare($sql_deliveries);
$stmt_deliveries->bind_param("i", $po_id);
$stmt_deliveries->execute();
$result_deliveries = $stmt_deliveries->get_result();

// 4. Fetch all payment records for this PO
$sql_payments = "SELECT * FROM payments WHERE po_id = ? ORDER BY payment_date ASC";
$stmt_payments = $conn->prepare($sql_payments);
$stmt_payments->bind_param("i", $po_id);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();
$total_paid = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print PO - <?php echo htmlspecialchars($po['po_number']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #fff; }
        .container { max-width: 960px; }
        .header { border-bottom: 2px solid #dee2e6; padding-bottom: 1rem; margin-bottom: 2rem; }
        .section { margin-top: 2rem; page-break-inside: avoid; }
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; } 
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <div class="header d-flex justify-content-between align-items-center">
            <h1>Purchase Order</h1>
            <div class="text-end">
                <h4 class="text-muted mb-0"><?php echo htmlspecialchars($po['po_number']); ?></h4>
                <div>Status: <span class="badge bg-primary"><?php echo htmlspecialchars($po['status']); ?></span></div>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-6">
                <strong>To:</strong><br>
                <h5><?php echo htmlspecialchars($po['supplier_name']); ?></h5>
                <p class="mb-0"><?php echo nl2br(htmlspecialchars($po['address'])); ?></p>
            </div>
            <div class="col-6 text-end">
                <strong>Order Date:</strong> <?php echo date("F j, Y", strtotime($po['order_date'])); ?><br>
                <?php if ($po['expected_delivery_date']): ?>
                    <strong>Expected Delivery:</strong> <?php echo date("F j, Y", strtotime($po['expected_delivery_date'])); ?><br>
                <?php endif; ?>
            </div>
        </div>

        <div class="section">
            <h5>Order Items</h5>
            <table class="table table-bordered">
                <thead class="table-light"><tr><th>SKU</th><th>Product Name</th><th class="text-end">Qty</th><th class="text-end">Unit Price</th><th class="text-end">Total</th></tr></thead>
                <tbody>
                    <?php while($item = $result_items->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['sku']); ?></td>
                            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                            <td class="text-end"><?php echo $item['quantity']; ?></td>
                            <td class="text-end">$<?php echo number_format($item['unit_price'], 2); ?></td>
                            <td class="text-end">$<?php echo number_format($item['total_price'], 2); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="4" class="text-end border-0">Grand Total:</th>
                        <th class="text-end border-0 h5">$<?php echo number_format($po['total_amount'], 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>

        <?php if ($result_deliveries->num_rows > 0): ?>
        <div class="section">
            <h5>Delivery History</h5>
            <table class="table table-sm table-striped">
                <thead class="table-light"><tr><th>Delivery Date</th><th>Notes</th><th>Items Received</th></tr></thead>
                <tbody>
                    <?php while($delivery = $result_deliveries->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo date("d M, Y", strtotime($delivery['delivery_date'])); ?></td>
                        <td><?php echo htmlspecialchars($delivery['notes']); ?></td>
                        <td>
                            <ul class="list-unstyled mb-0">
                            <?php 
                            $sql_d_items = "SELECT di.quantity_received, p.product_name FROM delivery_items di JOIN po_items pi ON di.po_item_id = pi.id JOIN products p ON pi.product_id = p.id WHERE di.delivery_id = ?";
                            $stmt_d_items = $conn->prepare($sql_d_items);
                            $stmt_d_items->bind_param("i", $delivery['id']);
                            $stmt_d_items->execute();
                            $result_d_items = $stmt_d_items->get_result();
                            while($d_item = $result_d_items->fetch_assoc()):
                            ?>
                                <li><?php echo $d_item['quantity_received']; ?> x <?php echo htmlspecialchars($d_item['product_name']); ?></li>
                            <?php endwhile; $stmt_d_items->close(); ?>
                            </ul>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if ($result_payments->num_rows > 0): ?>
        <div class="section">
            <h5>Payment History</h5>
            <table class="table table-sm table-striped">
                <thead class="table-light"><tr><th>Payment Date</th><th>Method</th><th>Notes/Reference</th><th class="text-end">Amount Paid</th></tr></thead>
                <tbody>
                    <?php mysqli_data_seek($result_payments, 0); while($payment = $result_payments->fetch_assoc()): $total_paid += $payment['amount_paid']; ?>
                    <tr>
                        <td><?php echo date("d M, Y", strtotime($payment['payment_date'])); ?></td>
                        <td><?php echo htmlspecialchars($payment['payment_method']); ?></td>
                        <td><?php echo htmlspecialchars($payment['notes']); ?></td>
                        <td class="text-end">$<?php echo number_format($payment['amount_paid'], 2); ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr><th colspan="3" class="text-end">Total Paid:</th><th class="text-end">$<?php echo number_format($total_paid, 2); ?></th></tr>
                    <tr><th colspan="3" class="text-end">Balance Due:</th><th class="text-end text-danger">$<?php echo number_format($po['total_amount'] - $total_paid, 2); ?></th></tr>
                </tfoot>
            </table>
        </div>
        <?php endif; ?>

        <div class="text-center mt-5 no-print">
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Print this page</button>
            <a href="view_po_details.php?id=<?php echo $po_id; ?>" class="btn btn-secondary">Back to PO Details</a>
        </div>
    </div>
</body>
</html>