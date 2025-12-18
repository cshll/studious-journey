<?php
session_start();

require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['usertype'] != 'admin') {
  die("403: You are not authorized to access this resource.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
  $guardian_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$guardian_id || $guardian_id <= 0) {
    die("Invalid guardian ID provided.");
  }

  try {
    $stmt = $pdo->prepare("DELETE FROM guardians WHERE guardian_id = :id");
    $stmt->execute(['id' => $guardian_id]);

    $url_params = $_GET;
    unset($url_params['id']);
    $query_string = http_build_query($url_params);

    echo '<script>window.history.back();</script>';
    exit;
  } catch (PDOException $error) {
    die("Error deleting guardian from guardian: " . $error->getMessage());
  }
} else {
  echo '<script>window.history.back();</script>';
  exit;
}
?>
