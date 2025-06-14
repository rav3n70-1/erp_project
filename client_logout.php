<?php
session_start();
session_destroy();
header("location: portal_login.php");
exit();
?>