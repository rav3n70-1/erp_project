<?php
$page_title = "Edit Purchase Order";
include('../../includes/header.php');
if (!has_permission('po_edit')) { header('Location: /erp_project/index.php?status=access_denied'); exit(); }
include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid PO ID."); }
$po_id = $_GET['id'];
$conn = connect_db();

// Fetch PO Header
$sql_po = "SELECT * FROM purchase_orders WHERE id = ?";
$stmt_po = $conn->prepare($sql_po);
$stmt_po->bind_param("i", $po_id);
$stmt_po->execute();
$po = $stmt_po->get_result()->fetch_assoc();

// Fetch PO Line Items
$sql_items = "SELECT * FROM po_items WHERE po_id = ?";
$stmt_items = $conn->prepare($sql_items);
$stmt_items->bind_param("i", $po_id);
$stmt_items->execute();
$po_items = $stmt_items->get_result()->fetch_all(MYSQLI_ASSOC);

// Fetch data for dropdowns
$sql_suppliers = "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
$suppliers_result = $conn->query($sql_suppliers);
$sql_products = "SELECT id, product_name, price FROM products ORDER BY product_name ASC";
$products_result = $conn->query($sql_products);
$products = $products_result->fetch_all(MYSQLI_ASSOC);
$products_json = json_encode($products);
$sql_budgets = "SELECT id, budget_name FROM budgets WHERE CURDATE() BETWEEN start_date AND end_date ORDER BY budget_name ASC";
$budgets_result = $conn->query($sql_budgets);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="view_pos.php">Purchase Orders</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit PO #<?php echo htmlspecialchars($po['po_number']); ?></li>
  </ol>
</nav>

<h1 class="mt-4">Edit Purchase Order</h1>

<form action="handle_edit_po.php" method="POST" id="po-form">
    <input type="hidden" name="po_id" value="<?php echo $po['id']; ?>">
    <div class="card">
        <div class="card-header"><h5>PO Details</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3"><label for="supplier_id" class="form-label">Supplier</label><select class="form-select" name="supplier_id" required><?php mysqli_data_seek($suppliers_result, 0); while($s = $suppliers_result->fetch_assoc()){ $sel = $s['id']==$po['supplier_id']?'selected':''; echo "<option value='{$s['id']}' {$sel}>".htmlspecialchars($s['supplier_name'])."</option>"; } ?></select></div>
                <div class="col-md-4 mb-3"><label for="order_date" class="form-label">Order Date</label><input type="date" class="form-control" name="order_date" value="<?php echo htmlspecialchars($po['order_date']); ?>" required></div>
                <div class="col-md-4 mb-3"><label for="budget_id" class="form-label">Budget</label><select class="form-select" name="budget_id"><option value="">None</option><?php while($b = $budgets_result->fetch_assoc()){ $sel = $b['id']==$po['budget_id']?'selected':''; echo "<option value='{$b['id']}' {$sel}>".htmlspecialchars($b['budget_name'])."</option>"; } ?></select></div>
            </div>
        </div>
    </div>
    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Order Items</h5>
            <button type="button" class="btn btn-success btn-sm" id="add-row-btn"><i class="bi bi-plus-circle"></i> Add Row</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="po-items-table">
                    <thead><tr><th>Product</th><th>Quantity</th><th>Unit Price ($)</th><th>Total ($)</th><th>Action</th></tr></thead>
                    <tbody>
                        <?php foreach($po_items as $item): ?>
                        <tr>
                            <td><select name="product_id[]" class="form-select product-select" required><option value="">Select</option><?php foreach ($products as $p) { $sel = $p['id']==$item['product_id']?'selected':''; echo "<option value='{$p['id']}' data-price='{$p['price']}' {$sel}>".htmlspecialchars($p['product_name'])."</option>"; } ?></select></td>
                            <td><input type="number" name="quantity[]" class="form-control quantity" min="1" value="<?php echo $item['quantity']; ?>" required></td>
                            <td><input type="number" name="unit_price[]" class="form-control unit-price" step="0.01" min="0" value="<?php echo $item['unit_price']; ?>" required></td>
                            <td><input type="text" class="form-control line-total" value="<?php echo $item['total_price']; ?>" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-btn"><i class="bi bi-trash"></i></button></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end"><h4>Grand Total: $<span id="grand-total">0.00</span></h4></div>
    </div>
    <div class="mt-4 text-center">
        <button type="submit" name="action" value="save_draft" class="btn btn-secondary btn-lg">Save as Draft</button>
        <button type="submit" name="action" value="submit_approval" class="btn btn-primary btn-lg">Submit for Approval</button>
    </div>
</form>

<?php
$conn->close();
include('../../includes/footer.php');
?>
<script>
// The same dynamic row JavaScript from create_po.php
const products = <?php echo $products_json; ?>;
// (The full script to handle dynamic rows and totals should be included here)
// For brevity, we are omitting it, but it's the same as in create_po.php
// Crucially, we must call updateTotals() on page load to set the initial grand total.
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('po-items-table').querySelector('tbody');
    const addRowBtn = document.getElementById('add-row-btn');
    function addRow() {
        // Your existing addRow function logic
    }
    function updateTotals() {
        let grandTotal = 0;
        tableBody.querySelectorAll('tr').forEach(row => {
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const lineTotal = quantity * unitPrice;
            row.querySelector('.line-total').value = lineTotal.toFixed(2);
            grandTotal += lineTotal;
        });
        document.getElementById('grand-total').textContent = grandTotal.toFixed(2);
    }
    addRowBtn.addEventListener('click', addRow);
    tableBody.addEventListener('input', function (e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            updateTotals();
        }
    });
    // Add other listeners from create_po.php here...
    updateTotals(); // This line is important
});
</script>