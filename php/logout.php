<?php
require 'init.php';

// Clear session array.
$_SESSION = array();

session_destroy();

// Send user back to login page
header("Location: login.php");
exit;
?>
