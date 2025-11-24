<?php
// Handles authentication within the website.

// Start session and set responses to JSON.
session_start();
header('Content-Type: application/json');

// Define variables used within code.
$username = '';
$password = '';

try {
  require 'connect.php';

  // If the incoming request is a POST, accept it.
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // If either username or password is empty, tell the user to fix the issues.
    if (empty($username) || empty($password)) {
      echo json_encode(['success' => false, 'message' => 'Please fill out all fields']);
      exit;
    }

    // Look in the database to find the corresponding username.
    $stmt = $pdo->prepare("SELECT username, password_hash FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Hash the password and check it against the hash within the database.
    if ($user && password_verify($password, $user['password_hash'])) {
      // Set session variables to use later, tells the website we are now logged in.
      $_SESSION['loggedin'] = TRUE;
      $_SESSION['username'] = $user['username'];

      // Tell the user their login was a success.
      echo json_encode(['success' => true, 'username' => $user['username']]);
      exit;
    } else {
      // Tell the user their login was not a success.
      echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
      exit;
    }
  } else {
    // Tell the user that they did not use the POST method and throw a '405: Method not allowed'.
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Endpoint only allows POST']);
    exit;
  }
} catch(PDOException $_) {
  // Tell the user that the database connection could not be made; vague for security.
  echo json_encode(['success' => false, 'message' => 'Could not connect to database']);
  exit;
}
?>
