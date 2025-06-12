<?php
session_start();
$_SESSION = array();
session_destroy();

// CORRECTED: Redirect to the new portal login page
header("location: portal_login.php");
exit;
?>