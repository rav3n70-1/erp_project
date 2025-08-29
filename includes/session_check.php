<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user_id session variable is not set.
if (!isset($_SESSION['user_id'])) {
    // Redirect to the login page
    header('Location: /erp_project/login.php');
    exit();
}
?>