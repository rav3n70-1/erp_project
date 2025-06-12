<?php
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Only allow POST requests
    header('Location: manage_categories.php');
    exit();
}

if (!isset($_POST['category_id'], $_POST['category_name']) || !is_numeric($_POST['category_id']) || empty($_POST['category_name'])) {
    header('Location: manage_categories.php?status=error');
    exit();
}

$category_id = $_POST['category_id'];
$category_name = $_POST['category_name'];

$conn = connect_db();

$sql = "UPDATE product_categories SET category_name = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $category_name, $category_id);

if ($stmt->execute()) {
    header("Location: manage_categories.php?status=updated");
} else {
    header("Location: manage_categories.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>