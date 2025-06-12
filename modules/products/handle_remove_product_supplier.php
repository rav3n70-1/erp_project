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

if (empty($_POST['product_id']) || empty($_POST['link_id'])) {
    header('Location: view_products.php?status=error');
    exit();
}

$product_id = $_POST['product_id'];
$link_id = $_POST['link_id'];

$conn = connect_db();

$sql = "DELETE FROM supplier_products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $link_id);

if ($stmt->execute()) {
    header("Location: edit_product.php?id=" . $product_id . "&status=supplier_unlinked");
} else {
    header("Location: edit_product.php?id=" . $product_id . "&status=error");
}

$stmt->close();
$conn->close();
exit();
?>