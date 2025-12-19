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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['full_name'], $_POST['birthday'], $_POST['address'], $_POST['class_id'])) {
  // Check if pupil ID is valid.
  $pupil_id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
  if (!$pupil_id || $pupil_id <= 0) {
    die("Invalid pupil ID provided.");
  }

  try {
    // Grab the class belonging to the pupil from the database.
    $pupil_stmt = $pdo->prepare("SELECT class_id FROM pupils WHERE pupil_id = :pupil_id");
    $pupil_stmt->execute(['pupil_id' => $pupil_id]);
    $pupil = $pupil_stmt->fetch(PDO::FETCH_ASSOC);

    // Check if pupil actually exists.
    if (!$pupil) {
      die("Invalid pupil ID provided.");
    }

    // Grab classes that are under capacity or matching pupils class ID from the database.
    $class_sql = "SELECT classes.class_id, classes.name 
    FROM classes 
    LEFT JOIN pupils ON classes.class_id = pupils.class_id 
    GROUP BY classes.class_id, classes.name, classes.capacity 
    HAVING COUNT(pupils.pupil_id) < classes.capacity OR classes.class_id = :class_id 
    ORDER BY classes.name ASC";

    $class_stmt = $pdo->prepare($class_sql);
    $class_stmt->execute(['class_id' => $pupil['class_id']]);
    $classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
  } catch (PDOException $error) {
    die("Unknown error!");
  }

  // Check if class ID is valid.
  $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
  if (!$class_id || !in_array($class_id, array_column($classes, 'class_id'))) {
    die("Invalid class ID provided.");
  }

  // Check if full name is valid.
  $full_name = trim(strip_tags($_POST['full_name']));
  if (empty($full_name) || strlen($full_name) > 100 || strlen($full_name) < 2) {
    die("Invalid full name provided.");
  }

  // Check if date provided is a valid date.
  $birthday = $_POST['birthday'];
  $date_object = DateTime::createFromFormat('Y-m-d', $birthday);
  if (!$date_object || $date_object->format('Y-m-d') !== $birthday) {
    die("Invalid birthday provided.");
  }

  $today = new DateTime();
  if ($date_object > $today) {
    die("Invalid birthday provided.");
  }

  // Check if address is valid.
  $address = trim(strip_tags($_POST['address']));
  if (empty($address)) {
    die("Invalid address provided.");
  }
  
  $medical_info = !empty($_POST['medical_info']) ? trim(strip_tags($_POST['medical_info'])) : null;

  try {
    // Update pupil within the database.
    $pupil_sql = "UPDATE pupils SET 
    full_name = :full_name, 
    address = :address,
    birthday = :birthday, 
    medical_info = :medical_info,
    class_id = :class_id 
    WHERE pupil_id = :pupil_id";

    $stmt = $pdo->prepare($pupil_sql);
    $stmt->execute([
      'full_name' => $full_name,
      'address' => $address,
      'birthday' => $birthday,
      'medical_info' => $medical_info,
      'class_id' => $class_id,
      'pupil_id' => $pupil_id
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
