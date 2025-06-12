<?php
$page_title = "Edit Product";
include('../../includes/header.php');

// Use a permission key that a Procurement Officer or Manager would have.
// The System Admin has all permissions by default.
if (!has_permission('po_edit')) { 
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

include('../../includes/db.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) { die("Invalid Product ID."); }

$product_id = $_GET['id'];
$conn = connect_db();

// 1. Fetch the product's existing data
$sql_product = "SELECT * FROM products WHERE id = ?";
$stmt_product = $conn->prepare($sql_product);
$stmt_product->bind_param("i", $product_id);
$stmt_product->execute();
$product_result = $stmt_product->get_result();
if ($product_result->num_rows === 0) { die("Product not found."); }
$product = $product_result->fetch_assoc();

// 2. Fetch categories for the 'Details' tab dropdown
$sql_categories = "SELECT id, category_name FROM product_categories ORDER BY category_name ASC";
$categories_result = $conn->query($sql_categories);

// 3. Fetch currently linked suppliers for the 'Suppliers' tab list
$sql_linked = "SELECT sp.id, s.supplier_name, sp.supplier_item_code 
               FROM supplier_products sp
               JOIN suppliers s ON sp.supplier_id = s.id
               WHERE sp.product_id = ?";
$stmt_linked = $conn->prepare($sql_linked);
$stmt_linked->bind_param("i", $product_id);
$stmt_linked->execute();
$linked_suppliers_result = $stmt_linked->get_result();

// 4. Fetch suppliers who are NOT yet linked to this product for the 'Suppliers' tab dropdown
$sql_unlinked = "SELECT id, supplier_name 
                 FROM suppliers 
                 WHERE id NOT IN (SELECT supplier_id FROM supplier_products WHERE product_id = ?)";
$stmt_unlinked = $conn->prepare($sql_unlinked);
$stmt_unlinked->bind_param("i", $product_id);
$stmt_unlinked->execute();
$unlinked_suppliers_result = $stmt_unlinked->get_result();

?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_products.php">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit: <?php echo htmlspecialchars($product['product_name']); ?></li>
  </ol>
</nav>

<h1 class="mt-4">Edit Product: <?php echo htmlspecialchars($product['product_name']); ?></h1>

<?php
// Status message handler
if (isset($_GET['status'])) {
    $message = ''; $alert_type = 'info';
    if ($_GET['status'] == 'supplier_linked') { $message = 'Supplier linked successfully!'; $alert_type = 'success'; }
    if ($_GET['status'] == 'supplier_unlinked') { $message = 'Supplier unlinked successfully.'; $alert_type = 'warning'; }
    if ($message) { echo '<div class="alert alert-'. $alert_type .' alert-dismissible fade show" role="alert">'. htmlspecialchars($message) .'<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>'; }
}
?>

<div class="card">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs" id="product-edit-tabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-pane" type="button">Product Details</button>
            </li>
            <?php if (has_permission('product_supplier_manage')): ?>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="suppliers-tab" data-bs-toggle="tab" data-bs-target="#suppliers-pane" type="button">Suppliers</button>
            </li>
            <?php endif; ?>
        </ul>
    </div>
    <div class="card-body">
        <div class="tab-content" id="product-edit-tabs-content">
            <div class="tab-pane fade show active" id="details-pane" role="tabpanel">
                <form action="handle_edit_product.php" method="POST">
                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                    <div class="row"><div class="col-md-6 mb-3"><label for="product_name" class="form-label">Product Name</label><input type="text" class="form-control" id="product_name" name="product_name" value="<?php echo htmlspecialchars($product['product_name']); ?>" required></div><div class="col-md-6 mb-3"><label for="sku" class="form-label">SKU</label><input type="text" class="form-control" id="sku" name="sku" value="<?php echo htmlspecialchars($product['sku']); ?>" required></div></div>
                    <div class="mb-3"><label for="description" class="form-label">Description</label><textarea class="form-control" id="description" name="description" rows="3"><?php echo htmlspecialchars($product['description']); ?></textarea></div>
                    <div class="row">
                        <div class="col-md-4 mb-3"><label for="category_id" class="form-label">Category</label><select class="form-select" id="category_id" name="category_id" required><?php while($cat_row = $categories_result->fetch_assoc()): $selected = ($cat_row['id'] == $product['category_id']) ? 'selected' : ''; echo "<option value='" . $cat_row['id'] . "' " . $selected . ">" . htmlspecialchars($cat_row['category_name']) . "</option>"; endwhile; ?></select></div>
                        <div class="col-md-4 mb-3"><label for="price" class="form-label">Price ($)</label><input type="number" class="form-control" id="price" name="price" value="<?php echo htmlspecialchars($product['price']); ?>" step="0.01" min="0" required></div>
                        <div class="col-md-4 mb-3">
                             <label for="reorder_point" class="form-label">Reorder Point</label>
                             <input type="number" class="form-control" id="reorder_point" name="reorder_point" value="<?php echo htmlspecialchars($product['reorder_point']); ?>" min="0">
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="view_products.php" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
            <div class="tab-pane fade" id="suppliers-pane" role="tabpanel">
                <div class="row">
                    <div class="col-md-8">
                        <h6>Linked Suppliers</h6>
                        <table class="table table-bordered">
                            <thead><tr><th>Supplier Name</th><th>Supplier's Item Code</th><th>Action</th></tr></thead>
                            <tbody>
                                <?php if ($linked_suppliers_result->num_rows > 0): ?>
                                    <?php while($row = $linked_suppliers_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                        <td><?php echo htmlspecialchars($row['supplier_item_code']); ?></td>
                                        <td>
                                            <form action="handle_remove_product_supplier.php" method="POST" onsubmit="return confirm('Are you sure you want to unlink this supplier?');">
                                                <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                                                <input type="hidden" name="link_id" value="<?php echo $row['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Unlink Supplier"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="3" class="text-center text-muted">No suppliers linked to this product yet.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-4">
                        <h6>Add New Supplier Link</h6>
                        <form action="handle_add_product_supplier.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
                            <div class="mb-3">
                                <label for="supplier_id" class="form-label">Supplier</label>
                                <select name="supplier_id" class="form-select" required>
                                    <option value="">Select a supplier to link</option>
                                    <?php while($row = $unlinked_suppliers_result->fetch_assoc()): ?>
                                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['supplier_name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="supplier_item_code" class="form-label">Supplier's Item Code (Optional)</label>
                                <input type="text" name="supplier_item_code" class="form-control">
                            </div>
                            <button type="submit" class="btn btn-success">Link Supplier</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>