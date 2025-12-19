<?php
session_start();

require 'connect.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header('Location: index.php');
  exit;
}

$username = '';
$error_msg = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = trim($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';

  // Pull down the user from the database that matches the username.
  try {
    $sql_users = "SELECT users.user_id, users.username, users.password_hash, types.type_name 
    FROM users 
    LEFT JOIN types ON users.type_id = types.type_id 
    WHERE users.username = :username";

    $stmt = $pdo->prepare($sql_users);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if hash matches the password.
    if ($user && password_verify($password, $user['password_hash'])) {
      // Set session variables for reuse.
      $_SESSION['loggedin'] = true;
      $_SESSION['userid'] = $user['user_id'];
      $_SESSION['usertype'] = $user['type_name'];
      $_SESSION['username'] = $user['username'];

      $real_name = $user['username'];

      // Grab users full name from the database.
      try {
        $stmt = $pdo->prepare("SELECT full_name FROM " . $user['type_name'] . "s WHERE user_id = :user_id");  
        $stmt->execute(['user_id' => $user['user_id']]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($profile) $real_name = $profile['full_name'];
      } catch (PDOException $error) {}

      $_SESSION['fullname'] = $real_name;

      header('Location: index.php');
      exit;
    } else {
      $error_msg = 'Invalid username or password.';
    }
  } catch (PDOException $error) {
    $error_msg = "Unknown error!";
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St Alphonsus Primary School - Control Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>

  <body>
    <div class="app-container">

      <header class="app-header">
      </header>

      <section class="card-container">
        <label class="card-header">Login</label><br><br>
        <form id="login" action="login.php" method="POST">
          <label for="username" class="card-text">Username</label> <label class="rq-text">*</label><br>
          <input type="text" id="username" name="username" class="input-text"><br>
          <label for="password" class="card-text">Password</label> <label class="rq-text">*</label><br>
          <input type="password" id="password" name="password" class="input-text"><br><br>
          <button class="btn btn-primary-grad" id="submit" name="submit">Submit</button><br>
          <?php if (!empty($error_msg)): ?>
            <label class="error-text"><?php echo htmlspecialchars($error_msg); ?></label>
          <?php endif; ?>
        </form>
      </section>

      <section class="card-container">
        <label class="card-header">Newly Enrolled / Forgot Password</label>
        <label class="card-text">Please talk to the <strong>IT Team</strong> to change or make queries about your user account.<br><small>* <em>Please save your password somewhere safe as the IT Team can only take requests during <strong>working hours</strong>.</em></small></label>
      </section>

      <footer class="app-footer">
        <p class="footer-copy">St Alphonsus Primary School<br>Control Panel</p>
      </footer>

    </div>

  </body>
</html>
