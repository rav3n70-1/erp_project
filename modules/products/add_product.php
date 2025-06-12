<?php
$page_title = "Add New Product";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();
$sql = "SELECT id, category_name FROM product_categories ORDER BY category_name ASC";
$categories_result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item"><a href="view_products.php">Products</a></li>
    <li class="breadcrumb-item active" aria-current="page">Add New Product</li>
  </ol>
</nav>

<h1 class="mt-4"><?php echo $page_title; ?></h1>

<div class="card">
    <div class="card-header"><h5>Product Details</h5></div>
    <div class="card-body">
        <form action="handle_add_product.php" method="POST">
            <div class="row">
                <div class="col-md-6 mb-3"><label for="product_name" class="form-label">Product Name <span class="text-danger">*</span></label><input type="text" class="form-control" id="product_name" name="product_name" required></div>
                <div class="col-md-6 mb-3"><label for="sku" class="form-label">SKU <span class="text-danger">*</span></label><input type="text" class="form-control" id="sku" name="sku" required></div>
            </div>
            <div class="mb-3"><label for="description" class="form-label">Description</label><textarea class="form-control" id="description" name="description" rows="3"></textarea></div>
            <div class="row">
                <div class="col-md-4 mb-3"><label for="category_id" class="form-label">Category <span class="text-danger">*</span></label><select class="form-select" id="category_id" name="category_id" required><option value="">Select a category</option><?php if ($categories_result->num_rows > 0) { while($row = $categories_result->fetch_assoc()) { echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['category_name']) . "</option>"; } } ?></select></div>
                <div class="col-md-4 mb-3"><label for="price" class="form-label">Price ($) <span class="text-danger">*</span></label><input type="number" class="form-control" id="price" name="price" step="0.01" min="0" required></div>
                <div class="col-md-4 mb-3">
                    <label for="reorder_point" class="form-label">Reorder Point</label>
                    <input type="number" class="form-control" id="reorder_point" name="reorder_point" min="0">
                    <div class="form-text">The system will suggest a new PO when stock falls to this level.</div>
                </div>
            </div>
            <hr>
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a href="view_products.php" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>