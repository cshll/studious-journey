<?php
// Handles authentication within the website.

// Start session and set responses to JSON.
session_start();
require 'connect.php';
require 'check.php';

// Define variables used within code.
$username = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  if (empty($username) || empty($password)) {
    $error_msg = 'Invalid username or password.';
  } else {
    try {
      $stmt = $pdo->prepare("SELECT username, password_hash FROM users WHERE username = :username");
      $stmt->execute(['username' => $username]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user['username'];

        header('Location: index.php');
        exit;
      } else {
        $error_msg = 'Invalid username or password.';
      }
    } catch (PDOException $error) {
      $error_msg = 'Unknown error.';
    }
  }
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>St Alphonsus Primary School</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div id="left">
      <h1>St Alphonsus Primary School</h1>
      <h2>Control Panel</h2>
    </div>
    <div id="right">
    </div>
  </header>
  <main>
    <div id="content">
      <div id="container">
        <label id="btext">Login</label><br>
        <label>Login to access the control panel.</label><br><br>
        <form id="login" action="login.php" method="POST">
          <label for="username">Username</label> <label id="rq">*</label><br>
          <input type="text" id="username" name="username"><br>
          <label for="password">Password</label> <label id="rq">*</label><br>
          <input type="password" id="password" name="password"><br><br>
          <button id="submit" name="submit">Submit</button>
          <?php if (!empty($error_msg)): ?>
            <label id="error"><?php echo htmlspecialchars($error_msg); ?></label>
          <?php endif; ?>
        </form> 
      </div>
    </div>
    <pre></pre>
  </main>
  <footer></footer>
</body>

</html>
