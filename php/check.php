<?php
$current_page = basename($_SERVER['PHP_SELF']);

if ($current_page == 'login.php') {
  if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    header('Location: index.php');
    exit;
  }
} else {
  if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: login.php');
    exit;
  }
}
?>
