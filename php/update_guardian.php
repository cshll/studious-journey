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
  // Check if guardian ID is valid.
  $guardian_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$guardian_id || $guardian_id <= 0) {
    die("Invalid guardian ID provided.");
  }

  // Check if guardian ID exists.
  $guardian_stmt = $pdo->prepare("SELECT COUNT(*) FROM guardians WHERE guardian_id = :guardian_id");
  $guardian_stmt->execute(['guardian_id' => $guardian_id]);
  $exists = $stmt->fetchColumn();

  if ($exists == 0) {
    die("Invalid guardian ID provided.");
  }

  // Check if full name is valid.
  $full_name = trim(strip_tags($_POST['full_name']));
  if (empty($full_name) || strlen($full_name) > 100 || strlen($full_name) < 2) {
    die("Invalid full name provided.");
  }

  // Check if address is valid.
  $address = trim(strip_tags($_POST['address']));
  if (empty($address)) {
    die("Invalid address provided.")
  }

  // Check if email is valid once sanitized.
  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    die("Invalid email provided.");
  }

  // Check if phone number matches regular expression (UK valid).
  $phone_number = trim($_POST['phone_number']);
  if (!preg_match('/^[0-9\+\-\s\(\)]{10,20}$/', $phone_number)) {
    die("Invalid phone number provided.");
  }

  try {
    // Update guardian on the database.
    $guardian_sql = "UPDATE guardians SET 
    full_name = :full_name, 
    address = :address,
    email = :email, 
    phone_number = :phone_number 
    WHERE guardian_id = :guardian_id";

    $stmt = $pdo->prepare($guardian_sql);
    $stmt->execute([
      'full_name' => $full_name,
      'address' => $address,
      'email' => $email,
      'phone_number' => $phone_number,
      'guardian_id' => $guardian_id
    ]);

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
