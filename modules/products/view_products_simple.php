<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Starting simple products page...<br>";

$page_title = "Manage Products";

echo "Including header...<br>";
include('../../includes/header.php');

echo "Including DB...<br>";
include('../../includes/db.php');

echo "Connecting to database...<br>";
$conn = connect_db();

echo "Executing simple query...<br>";
$sql = "SELECT p.*, pc.category_name 
        FROM products p
        LEFT JOIN product_categories pc ON p.category_id = pc.id
        ORDER BY p.product_name ASC
        LIMIT 10";
        
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}

echo "Query successful, rows: " . $result->num_rows . "<br>";
?>

<div class="container mt-4">
    <h1>Simple Products View</h1>
    
    <div class="card">
        <div class="card-body">
            <table class="table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['sku']); ?></td>
                                <td><?php echo htmlspecialchars($row['product_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['category_name'] ?? 'N/A'); ?></td>
                                <td>৳<?php echo number_format($row['price'], 2); ?></td>
                                <td><?php echo $row['quantity_in_stock']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">No products found</td>
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