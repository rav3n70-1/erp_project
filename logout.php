<?php
session_start();
$_SESSION = array();
session_destroy();

// Redirect to the landing page
header("location: index.html");
exit;
?>