<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['usertype'] != 'admin') {
  die("403: You are not authorized to access this resource.");
}

require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['date'], $_POST['description'])) {
  // Check if date provided is a valid date.
  $notice_date = $_POST['date'];
  $date_object = DateTime::createFromFormat('Y-m-d', $notice_date);
  if (!$date_object || $date_object->format('Y-m-d') !== $notice_date) {
    die("Invalid birthday provided.");
  }

  // Validate the title, strip tags and check if it isnt above 100 characters.
  $notice_title = trim(strip_tags($_POST['title']));
  if (strlen($notice_title) > 100) {
    die("Invalid title provided.");
  }

  // Validate the description, check if it isnt empty.
  $notice_description = trim(strip_tags($_POST['description']));
  if (empty($notice_description)) {
    die("Invalid description provided.");
  }

  // Create the notice within the database.
  try {
    $stmt = $pdo->prepare("INSERT INTO notices (description, notice_date, title) VALUES (?, ?, ?)");
    $stmt->execute([$notice_description, $notice_date, $notice_title]);

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
