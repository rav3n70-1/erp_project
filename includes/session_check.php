<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user_id session variable is not set.
if (!isset($_SESSION['user_id'])) {
    // CORRECTED: Redirect to the new portal login page
    header('Location: /erp_project/portal_login.php');
    exit();
}
?>