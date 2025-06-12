<?php
$page_title = "Record Delivery";
include('../../includes/header.php');
include('../../includes/db.php');

// 1. Check for a valid PO ID
if (!isset($_GET['po_id']) || !is_numeric($_GET['po_id'])) {
    die("Invalid Purchase Order ID.");
}

$po_id = $_GET['po_id'];
$conn = connect_db();

// 2. Fetch PO and Supplier details
$sql_po = "SELECT po.po_number, s.supplier_name FROM purchase_orders po JOIN suppliers s ON po.supplier_id = s.id WHERE po.id = ?";
$stmt_po = $conn->prepare($sql_po);
$stmt_po->bind_param("i", $po_id);
$stmt_po->execute();
$result_po = $stmt_po->get_result();
if ($result_po->num_rows === 0) {
    die("Purchase Order not found.");
}
$po = $result_po->fetch_assoc();

// 3. Fetch PO items, including how many have been ordered vs. how many have already been received.
$sql_items = "SELECT 
                pi.id AS po_item_id, 
                pi.quantity AS quantity_ordered, 
                p.product_name,
                p.sku,
                (SELECT SUM(di.quantity_received) 
                 FROM delivery_items di 
                 WHERE di.po_item_id = pi.id) AS total_received
              FROM po_items pi
              JOIN products p ON pi.product_id = p.id
              WHERE pi.po_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $po_id);
$stmt_items->execute();
$result_items = $stmt_items->get_result();
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="/erp_project/modules/purchase_orders/view_pos.php">Purchase Orders</a></li>
    <li class="breadcrumb-item"><a href="/erp_project/modules/purchase_orders/view_po_details.php?id=<?php echo $po_id; ?>"><?php echo htmlspecialchars($po['po_number']); ?></a></li>
    <li class="breadcrumb-item active" aria-current="page">Record Delivery</li>
  </ol>
</nav>

<h1 class="mt-4">Record Delivery for PO: <?php echo htmlspecialchars($po['po_number']); ?></h1>
<p class="lead">Supplier: <?php echo htmlspecialchars($po['supplier_name']); ?></p>

<form action="handle_record_delivery.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="po_id" value="<?php echo $po_id; ?>">
    <div class="card">
        <div class="card-header"><h5>Delivery Details</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="delivery_date" class="form-label">Delivery Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="notes" class="form-label">Notes / Remarks</label>
                    <input type="text" class="form-control" id="notes" name="notes">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="grn_file" class="form-label">Attach GRN / Photo</label>
                    <input class="form-control" type="file" id="grn_file" name="grn_file">
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header"><h5>Received Items</h5></div>
        <div class="card-body">
            <p>Enter the quantity received for each item in this delivery. Leave blank or enter 0 if an item was not received.</p>
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Product</th>
                            <th class="text-end">Ordered</th>
                            <th class="text-end">Already Received</th>
                            <th class="text-end">Remaining</th>
                            <th style="width: 15%;">Quantity Received Now <span class="text-danger">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($item = $result_items->fetch_assoc()): 
                            $total_received = (int)$item['total_received'];
                            $remaining = $item['quantity_ordered'] - $total_received;
                            if ($remaining <= 0) continue;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                <td class="text-end"><?php echo $item['quantity_ordered']; ?></td>
                                <td class="text-end"><?php echo $total_received; ?></td>
                                <td class="text-end fw-bold"><?php echo $remaining; ?></td>
                                <td>
                                    <input type="hidden" name="po_item_id[]" value="<?php echo $item['po_item_id']; ?>">
                                    <input type="number" name="quantity_received[]" class="form-control" min="0" max="<?php echo $remaining; ?>" placeholder="0">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="mt-4 text-center">
        <button type="submit" class="btn btn-primary btn-lg">Save Delivery Record</button>
        <a href="/erp_project/modules/purchase_orders/view_po_details.php?id=<?php echo $po_id; ?>" class="btn btn-secondary btn-lg">Cancel</a>
    </div>
</form>

<?php
$conn->close();
include('../../includes/footer.php');
?>