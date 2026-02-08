<?php
session_start();

// Check if user has an active session, save as variable for use later
$is_logged_in = isset($_SESSION['loggedin']) && $_SESSION['loggedin'];

// Database parameters
$db_host = 'db';
$db_user = 'root';
$db_pass = 'busses';
$db_name = 'bus_db';

// Connect to the database or throw error if connection failed.
try {
  $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
  die($error);
}
?>
