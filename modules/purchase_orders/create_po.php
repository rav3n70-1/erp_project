<?php
$page_title = "Create New Purchase Order";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

// Fetch Suppliers
$sql_suppliers = "SELECT id, supplier_name FROM suppliers ORDER BY supplier_name ASC";
$suppliers_result = $conn->query($sql_suppliers);

// Fetch Products for JavaScript
$sql_products = "SELECT id, product_name, price FROM products ORDER BY product_name ASC";
$products_result = $conn->query($sql_products);
$products = [];
while ($row = $products_result->fetch_assoc()) {
    $products[] = $row;
}
$products_json = json_encode($products);

// Fetch Budgets with their spending data
$sql_budgets = "SELECT 
                    b.id, b.budget_name, b.allocated_amount,
                    ((SELECT COALESCE(SUM(po.total_amount), 0) FROM purchase_orders po WHERE po.budget_id = b.id AND po.status != 'Rejected') +
                     (SELECT COALESCE(SUM(p.project_budget), 0) FROM projects p WHERE p.budget_id = b.id)) as spent_amount
                FROM budgets b
                WHERE CURDATE() BETWEEN start_date AND end_date
                ORDER BY budget_name ASC";
$budgets_result = $conn->query($sql_budgets);

// Generate PO Number
$sql_last_po = "SELECT id FROM purchase_orders ORDER BY id DESC LIMIT 1";
$last_po_result = $conn->query($sql_last_po);
$last_po_id = 0;
if ($last_po_result->num_rows > 0) {
    $last_po_id = $last_po_result->fetch_assoc()['id'];
}
$new_po_number = 'PO-' . date('Y') . '-' . str_pad($last_po_id + 1, 4, '0', STR_PAD_LEFT);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_pos.php">Purchase Orders</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create PO</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<form action="handle_create_po.php" method="POST" id="po-form">
    <div class="card">
        <div class="card-header"><h5>Purchase Order Details</h5></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="supplier_id" class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select class="form-select" id="supplier_id" name="supplier_id" required>
                        <option value="">Select Supplier</option>
                        <?php while($row = $suppliers_result->fetch_assoc()): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['supplier_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="order_date" class="form-label">Order Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" id="order_date" name="order_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="budget_id" class="form-label">Assign to Budget (Optional)</label>
                    <select class="form-select" id="budget_id" name="budget_id">
                        <option value="" data-remaining="0">None</option>
                         <?php while($row = $budgets_result->fetch_assoc()): 
                            $remaining_amount = $row['allocated_amount'] - $row['spent_amount'];
                         ?>
                            <option value="<?php echo $row['id']; ?>" data-remaining="<?php echo $remaining_amount; ?>">
                                <?php echo htmlspecialchars($row['budget_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                    <div id="budget-info" class="form-text" style="display: none;">
                        Remaining Budget: <strong id="remaining-budget-amount" class="text-success"></strong>
                    </div>
                </div>
            </div>
             <input type="hidden" name="po_number" value="<?php echo $new_po_number; ?>">
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
                    <thead><tr><th style="width: 40%;">Product <span class="text-danger">*</span></th><th style="width: 15%;">Quantity <span class="text-danger">*</span></th><th style="width: 15%;">Unit Price ($) <span class="text-danger">*</span></th><th style="width: 20%;">Total ($)</th><th style="width: 10%;">Action</th></tr></thead>
                    <tbody>
                        <tr>
                            <td><select name="product_id[]" class="form-select product-select" required><option value="">Select a product</option><?php foreach ($products as $product): ?><option value="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>"><?php echo htmlspecialchars($product['product_name']); ?></option><?php endforeach; ?></select></td>
                            <td><input type="number" name="quantity[]" class="form-control quantity" min="1" required></td>
                            <td><input type="number" name="unit_price[]" class="form-control unit-price" step="0.01" min="0" required></td>
                            <td><input type="text" class="form-control line-total" readonly></td>
                            <td><button type="button" class="btn btn-danger btn-sm delete-row-btn"><i class="bi bi-trash"></i></button></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-end">
            <h4>Grand Total: $<span id="grand-total">0.00</span></h4>
        </div>
    </div>

    <div class="mt-4 text-center">
        <button type="submit" class="btn btn-primary btn-lg">Submit for Approval</button>
    </div>
</form>

<?php
$conn->close();
include('../../includes/footer.php');
?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const tableBody = document.getElementById('po-items-table').querySelector('tbody');
    const addRowBtn = document.getElementById('add-row-btn');
    const budgetSelect = document.getElementById('budget_id');
    const budgetInfoDiv = document.getElementById('budget-info');
    const remainingBudgetElement = document.getElementById('remaining-budget-amount');
    const poForm = document.getElementById('po-form');

    function addRow() {
        const newRow = tableBody.rows[0].cloneNode(true);
        newRow.querySelector('select').value = '';
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        tableBody.appendChild(newRow);
    }

    // THIS FUNCTION IS NEW/UPDATED
    function updateRemainingDisplay() {
        const selectedBudget = budgetSelect.options[budgetSelect.selectedIndex];
        if (!selectedBudget || !selectedBudget.value) {
            budgetInfoDiv.style.display = 'none';
            return;
        }

        const originalRemaining = parseFloat(selectedBudget.dataset.remaining);
        const grandTotal = parseFloat(document.getElementById('grand-total').textContent);
        
        // Calculate what the budget will be *after* this PO
        const newRemaining = originalRemaining - grandTotal;

        remainingBudgetElement.textContent = '$' + newRemaining.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});

        if (newRemaining < 0) {
            remainingBudgetElement.classList.remove('text-success');
            remainingBudgetElement.classList.add('text-danger');
        } else {
            remainingBudgetElement.classList.remove('text-danger');
            remainingBudgetElement.classList.add('text-success');
        }
        
        budgetInfoDiv.style.display = 'block';
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
        
        // THIS IS THE KEY CHANGE: Update the budget display every time the total changes
        updateRemainingDisplay();
    }

    addRowBtn.addEventListener('click', addRow);
    
    tableBody.addEventListener('input', function(e) {
        if (e.target.classList.contains('quantity') || e.target.classList.contains('unit-price')) {
            updateTotals();
        }
    });

    tableBody.addEventListener('change', function (e) {
        if (e.target.classList.contains('product-select')) {
            const selectedOption = e.target.options[e.target.selectedIndex];
            const price = selectedOption.dataset.price || 0;
            e.target.closest('tr').querySelector('.unit-price').value = parseFloat(price).toFixed(2);
            updateTotals(); 
        }
    });
    
    tableBody.addEventListener('click', function(e) {
        if (e.target.closest('.delete-row-btn')) {
            if (tableBody.rows.length > 1) {
                e.target.closest('tr').remove();
                updateTotals();
            } else {
                alert('You must have at least one item in the purchase order.');
            }
        }
    });

    budgetSelect.addEventListener('change', updateRemainingDisplay);
    
    poForm.addEventListener('submit', function(e) {
        const selectedBudget = budgetSelect.options[budgetSelect.selectedIndex];
        if (selectedBudget && selectedBudget.value) {
            const remaining = parseFloat(selectedBudget.dataset.remaining);
            const grandTotal = parseFloat(document.getElementById('grand-total').textContent);
            
            if (grandTotal > remaining) {
                e.preventDefault(); 
                alert('Error: The Grand Total of this PO exceeds the remaining funds in the selected budget.');
            }
        }
    });
});
</script>