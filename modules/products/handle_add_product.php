<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('po_create')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_products.php');
    exit();
}

$required_fields = ['product_name', 'sku', 'category_id', 'price'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: add_product.php?status=error_missing');
        exit();
    }
}

$product_name = $_POST['product_name'];
$sku = $_POST['sku'];
$description = $_POST['description'] ?? NULL;
$category_id = $_POST['category_id'];
$price = $_POST['price'];
// Get the new reorder point value
$reorder_point = !empty($_POST['reorder_point']) ? $_POST['reorder_point'] : NULL;

$conn = connect_db();

$sql_check = "SELECT id FROM products WHERE sku = ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("s", $sku);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header("Location: add_product.php?status=error_sku_exists");
    exit();
}
$stmt_check->close();

// Update the SQL query to include reorder_point
$sql = "INSERT INTO products (product_name, sku, description, category_id, price, reorder_point) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
// Update the bind_param string and variables
$stmt->bind_param("sssidi", $product_name, $sku, $description, $category_id, $price, $reorder_point);

if ($stmt->execute()) {
    $product_id = $conn->insert_id;
    log_audit_trail($conn, "Created new product: " . $product_name, 'Product', $product_id);
    header("Location: view_products.php?status=success");
} else {
    header("Location: add_product.php?status=error&msg=db_error");
}

$stmt->close();
$conn->close();
exit();
?>