<?php
$db_host = 'db';
$db_user = 'root';
$db_pass = 'school';
$db_name = 'school_db';

try {
  $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $error) {
  die("Database connection failed with error: " . $e->getMessage());
}
?>
