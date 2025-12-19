<?php
session_start();

require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['usertype'] == 'admin') {
  die("403: You can access this but you don't need to.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $user_id = $_SESSION['userid'];
  $current_password = $_POST['current_password'] ?? '';
  $new_password = $_POST['new_password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ? '';

  // Check if the password matches requirements.
  if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    $error_msg = "All fields must be entered.";
  } elseif ($new_password !== $confirm_password) {
    $error_msg = "Password does not match.";
  } elseif (strlen($new_password) < 8 ||
    !preg_match('/[A-Z]/', $new_password) ||
    !preg_match('/[0-9]/', $new_password)
  ) {
    $error_msg = "Password must be at least 8 characters, contain a number, and a capital letter.";
  } 

  // If no errors were found, continue.
  if (empty($error_msg)) {
    try {
      // Grab the password hash for the current logged in user.
      $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = :user_id");
      $stmt->execute(['user_id' => $user_id]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      // Check if the current password is correct.
      if (!password_verify($current_password, $user['password_hash'])) {
        $error_msg = 'Current password is not correct.';
      } else {
        // Hash the password.
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Update the password on the database.
        $user_sql = "UPDATE users SET 
        password_hash = :password_hash
        WHERE user_id = :user_id";

        $stmt = $pdo->prepare($user_sql);
        $stmt->execute([
          'password_hash' => $hashed_password,
          'user_id' => $user_id
        ]);
      }
    } catch (PDOException $error) {
      $error_msg = "Unknown error!";
    }
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

      <div id="nav-bar" class="overlay">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

        <div class="overlay-content">
          <a href="my_settings.php" style="font-weight: 700;">My Settings</a>

          <a href="index.php">Dashboard</a>

          <a href="classes.php">Classes</a>
          <a href="pupils.php">Pupils</a>
          <a href="guardians.php">Guardians</a>

          <?php if ($_SESSION['usertype'] == 'admin'): ?>
            <a href="teachers.php">Teachers</a>
          <?php endif; ?>
        </div>
      </div>

      <header class="app-header">
        <button class="nobtn hamburger" id="hamburger-menu" onclick="openNav()">
          <svg width="35" height="40" viewBox="0 0 24 24" fill="none" style="stroke: var(--text-slate);" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <section>
            <?php echo $_SESSION['username']; ?>&nbsp;&nbsp;
            <a class="btn btn-primary-grad" href="logout.php" id="logout">Logout</a>
        </section>
      </header>

      <section class="card-container">
        <label class="card-header">Update Password</label>
        <form action="my_settings.php" method="POST">
          <label class="card-title" for="current_password">Current Password</label> <label class="rq-text">*</label><br>
          <input type="password" name="current_password" required><br>
          <label class="card-title" for="new_password">New Password</label> <label class="rq-text">*</label><br>
          <input type="password" name="new_password" required><br>
          <label class="card-title" for="confirm_password">Confirm New Password</label> <label class="rq-text">*</label><br>
          <input type="password" name="confirm_password" required><br><br>
          <button class="btn btn-primary-grad" name="submit">Save Changes</button><br>
          <?php if (!empty($error_msg)): ?>
            <label class="error-text"><?php echo htmlspecialchars($error_msg); ?></label>
          <?php endif; ?>
        </form>
      </section>

      <footer class="app-footer">
        <p class="footer-copy">St Alphonsus Primary School<br>Control Panel</p>
      </footer>

    </div>

    <script>
      function openNav() {
        document.getElementById("nav-bar").style.width = "100%";
      }
      
      function closeNav() {
        document.getElementById("nav-bar").style.width = "0%";
      }
    </script>

  </body>
</html>
