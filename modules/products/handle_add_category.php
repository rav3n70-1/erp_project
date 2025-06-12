<?php
include('../../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Only allow POST requests
    header('Location: manage_categories.php');
    exit();
}

if (!isset($_POST['category_name']) || empty($_POST['category_name'])) {
    // Redirect with an error if the name is missing
    header('Location: manage_categories.php?status=error');
    exit();
}

$category_name = $_POST['category_name'];
$conn = connect_db();

$sql = "INSERT INTO product_categories (category_name) VALUES (?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $category_name);

if ($stmt->execute()) {
    // Success
    header("Location: manage_categories.php?status=success");
} else {
    // Failure
    header("Location: manage_categories.php?status=error");
}

$stmt->close();
$conn->close();
exit();
?>