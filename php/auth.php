<?php
$db_host = 'db';
$db_user = 'root';
$db_pass = 'school';
$db_name = 'school_db';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
} else {
  die("Request method was invalid");
}

try {
  $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE username = :username");
  $stmt->execute(['username' => $username]);
  $user = $stmt->fetch();

  if ($user) {
    $stored_hash = $user['password_hash'];
    if (password_verify($password, $stored_hash)) {
      // success
    } else {
      // fail
    }
  } else {
    // fail
  }
} catch(PDOException $error) {
  die("Error with database\n" . $error->getMessage());
}
?>
