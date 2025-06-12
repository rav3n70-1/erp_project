<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('po_edit')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_products.php');
    exit();
}

$required_fields = ['product_id', 'product_name', 'sku', 'category_id', 'price'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header('Location: view_products.php?status=error');
        exit();
    }
}

$product_id = $_POST['product_id'];
$product_name = $_POST['product_name'];
$sku = $_POST['sku'];
$description = $_POST['description'] ?? NULL;
$category_id = $_POST['category_id'];
$price = $_POST['price'];
// Get the new reorder point value
$reorder_point = !empty($_POST['reorder_point']) ? $_POST['reorder_point'] : NULL;

$conn = connect_db();

$sql_check = "SELECT id FROM products WHERE sku = ? AND id != ?";
$stmt_check = $conn->prepare($sql_check);
$stmt_check->bind_param("si", $sku, $product_id);
$stmt_check->execute();
$stmt_check->store_result();
if ($stmt_check->num_rows > 0) {
    header('Location: edit_product.php?id=' . $product_id . '&status=error_sku_exists');
    exit();
}
$stmt_check->close();

// Update the SQL query to include reorder_point
$sql = "UPDATE products SET product_name = ?, sku = ?, description = ?, category_id = ?, price = ?, reorder_point = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
// Update the bind_param string and variables
$stmt->bind_param("sssidii", $product_name, $sku, $description, $category_id, $price, $reorder_point, $product_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Edited product: " . $product_name, 'Product', $product_id);
    header("Location: view_products.php?status=updated");
} else {
    header("Location: edit_product.php?id=" . $product_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>