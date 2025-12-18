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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['full_name'], $_POST['address'], $_POST['email'], $_POST['phone_number'])) {
  $teacher_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$teacher_id || $teacher_id <= 0) {
    die("Invalid teacher ID provided.");
  }

  $full_name = trim(strip_tags($_POST['full_name']));
  if (empty($full_name) || strlen($full_name) > 100 || strlen($full_name) < 2) {
    die("Invalid full name provided.");
  }

  $address = trim(strip_tags($_POST['address']));
  if (empty($address)) {
    die("Invalid address provided.");
  }

  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    die("Invalid email provided.");
  }
  
  $phone_number = trim($_POST['phone_number']);
  if (!preg_match('/^[0-9\+\-\s\(\)]{10,20}$/', $phone_number)) {
    die("Invalid phone number provided.");
  }
  
  try {
    $teacher_sql = "UPDATE teachers SET 
    full_name = :full_name, 
    address = :address,
    email = :email, 
    phone_number = :phone_number  
    WHERE teacher_id = :teacher_id";

    $stmt = $pdo->prepare($teacher_sql);
    $stmt->execute([
      'full_name' => $full_name,
      'address' => $address,
      'email' => $email,
      'phone_number' => $phone_number,
      'teacher_id' => $teacher_id
    ]);

    echo '<script>window.history.back();</script>';
    exit;
  } catch (PDOException $error) {
    die("Error updating teacher: " . $error->getMessage());
  }
} else {
  echo '<script>window.history.back();</script>';
  exit;
}
?>
