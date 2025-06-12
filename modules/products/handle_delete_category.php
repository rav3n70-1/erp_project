<?php
include('../../includes/db.php');

// We also need to check permissions on the handler itself
include('../../includes/session_check.php');
include('../../includes/permissions.php');
if (!has_permission('Admin')) {
    // If user is not an Admin, redirect them away.
    header('Location: /erp_project/index.php?status=access_denied');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: manage_categories.php');
    exit();
}

if (!isset($_POST['id']) || !is_numeric($_POST['id'])) {
    header('Location: manage_categories.php?status=error');
    exit();
}

$category_id = $_POST['id'];
$conn = connect_db();

$sql = "DELETE FROM product_categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);

// Use a try-catch block to handle potential database errors
try {
    if ($stmt->execute()) {
        header("Location: manage_categories.php?status=deleted");
    } else {
        // This case might be for other, non-exception errors
        header("Location: manage_categories.php?status=error");
    }
} catch (mysqli_sql_exception $e) {
    // Catch the specific foreign key constraint error
    // MySQL error code 1451 is for foreign key constraint violations
    if ($e->getCode() == 1451) {
        header("Location: manage_categories.php?status=delete_error");
    } else {
        // For any other database error, show a generic error
        // In a real application, you would log the error: error_log($e->getMessage());
        header("Location: manage_categories.php?status=error");
    }
}

$stmt->close();
$conn->close();
exit();
?>