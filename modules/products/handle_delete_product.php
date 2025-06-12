<?php
include('../../includes/db.php');
include('../../includes/session_check.php');
include('../../includes/permissions.php');

if (!has_permission('Manager')) { // Example permission
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: view_products.php');
    exit();
}

$product_id = $_POST['id'];
$conn = connect_db();

$sql = "DELETE FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    log_audit_trail($conn, "Deleted product", 'Product', $product_id);
    header("Location: view_products.php?status=deleted");
} else {
    header("Location: view_products.php?status=error");
}

$conn->close();
exit();
?>