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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
  // Check if pupil ID exists.
  $pupil_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$pupil_id || $pupil_id <= 0) {
    die("Invalid pupil ID provided.");
  }
  
  // Delete the pupil from the database.
  try {
    $stmt = $pdo->prepare("DELETE FROM pupils WHERE pupil_id = :id");
    $stmt->execute(['id' => $pupil_id]);

    $url_params = $_GET;
    unset($url_params['id']);
    $query_string = http_build_query($url_params);

    echo '<script>window.history.back();</script>';
    exit;
  } catch (PDOException $error) {
    die("Unknown error!");
  }
} else {
  echo '<script>window.history.back();</script>';
  exit;
}
?>
