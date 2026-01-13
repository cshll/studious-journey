<?php
$db_host = 'db';
$db_user = 'root';
$db_pass = 'busses';
$db_name = 'bus_db';

// Connect to the database or throw error if connection failed.
try {
  $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
  die("Unknown error!");
}
?>
