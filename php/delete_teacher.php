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
  $teacher_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$teacher_id || $teacher_id <= 0) {
    die("Invalid teacher ID provided.");
  }

  try {
    $stmt = $pdo->prepare("DELETE FROM teachers WHERE teacher_id = :id");
    $stmt->execute(['id' => $teacher_id]);

    $url_params = $_GET;
    unset($url_params['id']);
    $query_string = http_build_query($url_params);

    echo '<script>window.history.back();</script>';
    exit;
  } catch (PDOException $error) {
    die("Error deleting teacher from teachers: " . $error->getMessage());
  }
} else {
  echo '<script>window.history.back();</script>';
  exit;
}
?>
