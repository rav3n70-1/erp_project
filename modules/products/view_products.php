<?php
$page_title = "Manage Products";
include('../../includes/header.php');
include('../../includes/db.php');

$conn = connect_db();

$sql = "SELECT p.*, pc.category_name 
        FROM products p
        JOIN product_categories pc ON p.category_id = pc.id
        ORDER BY p.product_name ASC";
$result = $conn->query($sql);
?>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="/erp_project/index.php">Home</a></li>
    <li class="breadcrumb-item active" aria-current="page">Products</li>
  </ol>
</nav>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><?php echo $page_title; ?></h1>
    <?php // Only show 'Add New' button to users with permission
    if (has_permission(['Manager', 'Procurement Officer'])): ?>
        <a href="add_product.php" class="btn btn-primary"><i class="bi bi-plus-circle me-2"></i>Add New Product</a>
    <?php endif; ?>
</div>

<?php
// ... (status message handling code is unchanged) ...
?>

<div class="card">
    <div class="card-header">
        <h5>All Products</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th class="text-end">Qty in Stock</th>
                        <th class="text-end">Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['sku']); ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                                <td class="text-end fw-bold"><?php echo $row['quantity_in_stock']; ?></td>
                                <td class="text-end">$<?php echo number_format($row['price'], 2); ?></td>
                                <td>
                                    <?php // Show Edit button to Manager and Officer
                                    if (has_permission(['Manager', 'Procurement Officer'])): ?>
                                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil-square"></i></a>
                                    <?php endif; ?>
                                    
                                    <?php // Show Delete button to Manager only
                                    if (has_permission('Manager')): ?>
                                        <button type="button" class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteProductModal"
                                                data-id="<?php echo $row['id']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted">No products found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$conn->close();
include('../../includes/footer.php');
?>