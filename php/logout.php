<?php
require 'init.php';

// Clear session array.
$_SESSION = array();

session_destroy();

header("Location: login.php");
exit;
?>
