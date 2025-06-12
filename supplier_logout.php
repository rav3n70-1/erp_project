<?php
session_start();
session_destroy();
header("location: supplier_login.php");
exit;
?>