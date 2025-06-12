<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('product_supplier_manage')) {
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_products.php');
    exit();
}

$required_fields = ['product_id', 'supplier_id'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        header("Location: edit_product.php?id=" . $_POST['product_id'] . "&status=error_missing");
        exit();
    }
}

$product_id = $_POST['product_id'];
$supplier_id = $_POST['supplier_id'];
$item_code = $_POST['supplier_item_code'] ?? NULL;

$conn = connect_db();

$sql = "INSERT INTO supplier_products (product_id, supplier_id, supplier_item_code) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $product_id, $supplier_id, $item_code);

if ($stmt->execute()) {
    header("Location: edit_product.php?id=" . $product_id . "&status=supplier_linked");
} else {
    // This could fail if the link already exists due to the UNIQUE constraint
    header("Location: edit_product.php?id=" . $product_id . "&status=error_exists");
}

$stmt->close();
$conn->close();
exit();
?>