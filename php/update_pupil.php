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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['full_name'], $_POST['birthday'], $_POST['address'])) {
  $pupil_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$pupil_id || $pupil_id <= 0) {
    die("Invalid pupil ID provided.");
  }

  $full_name = trim(strip_tags($_POST['full_name']));
  if (empty($full_name) || strlen($full_name) > 100 || strlen($full_name) < 2) {
    die("Invalid full name provided.");
  }

  $birthday = $_POST['birthday'];
  $date_object = DateTime::createFromFormat('Y-m-d', $birthday);
  if (!$date_object || $date_object->format('Y-m-d') !== $birthday) {
    die("Invalid birthday provided.");
  }

  $today = new DateTime();
  if ($date_object > $today) {
    die("Invalid birthday provided.");
  }

  $address = trim(strip_tags($_POST['address']));
  if (empty($address)) {
    die("Invalid address provided.");
  }
  
  $medical_info = !empty($_POST['medical_info']) ? trim(strip_tags($_POST['medical_info'])) : null;

  try {
    $pupil_sql = "UPDATE pupils SET 
    full_name = :full_name, 
    address = :address,
    birthday = :birthday, 
    medical_info = :medical_info 
    WHERE pupil_id = :pupil_id";

    $stmt = $pdo->prepare($pupil_sql);
    $stmt->execute([
      'full_name' => $full_name,
      'address' => $address,
      'birthday' => $birthday,
      'medical_info' => $medical_info,
      'pupil_id' => $pupil_id
    ]);

    echo '<script>window.history.back();</script>';
    exit;
  } catch (PDOException $error) {
    die("Error updating pupil: " . $error->getMessage());
  }
} else {
  echo '<script>window.history.back();</script>';
  exit;
}
?>
