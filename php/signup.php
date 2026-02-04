<?php
session_start();

require 'connect.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header("Location: index.php");
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $full_name = trim($_POST['full_name']);
  $dob = $_POST['dob'];
  $address = trim($_POST['address']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  if (empty($full_name) || empty($dob) || empty($address) || empty($email) || empty($password)) {
    $error = "All fields are required!";
  } elseif ($password !== $confirm_password) {
    $error = "Password do not match!";
  } elseif (strlen($password) < 6) {
    $error = "Password must be at least 6 characters long!";
  } else {
    $sql = "SELECT user_id FROM users WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);

    if ($stmt->rowCount() > 0) {
      $error = "This email is already registered!";
    } else {
      $password_hash = password_hash($password, PASSWORD_DEFAULT);

      $sql = "INSERT INTO users (full_name, date_of_birth, address, email, password_hash) 
        VALUES (:name, :dob, :address, :email, :password_hash)";
      
      try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'name' => $full_name,
          'dob' => $dob,
          'address' => $address,
          'email' => $email,
          'password_hash' => $password_hash
        ]);

        $new_user_id = $pdo->lastInsertId();

        $_SESSION['loggedin'] = true;
        $_SESSION['userid'] = $new_user_id;
        $_SESSION['email'] = $email;
        $_SESSION['full_name'] = $full_name;

        header("Location: index.php");
        exit;
      } catch (PDOException $_) {
        $error = "Something went wrong, please contact us if this persists.";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Bus Company</title>
  <link rel="stylesheet" href="style.css">
  <link rel="manifest" href="manifest.json">
</head>
<body>
  <header class="site-header">
    <div class="container header-flex">
      <div class="logo">
        <svg width="50" height="50" viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
          <g fill="var(--text-dark)" fill-rule="evenodd">
            <path d="M10 20h40c2.2 0 4 1.8 4 4v16c0 2.2-1.8 4-4 4h-4v4h-6v-4H20v4h-6v-4h-4c-2.2 0-4-1.8-4-4V24c0-2.2 1.8-4 4-4zm4 6h10v8H14v-8zm18 0h14v8H32v-8z"/>
          </g>
        </svg>
        <a href="index.php">Trafford Bus</a>
      </div>
      <nav class="main-nav">
        <ul>
          <li><a href="tickets.php">Tickets</a></li>
          <li><a href="livemap.php">Map</a></li>
          <li><a href="timetable.php">Timetables</a></li>
          <li><a href="journeys.php">Journeys</a></li>
        </ul>
      </nav>
      <a class="btn btn-header" href="login.php" id="login">Login</a>
    </div>
  </header>

  <main class="site-content" style="padding: 20px; margin: 60px auto; max-width: 900px; overflow: hidden;">
    <div class="container auth-card register-page">
      <h1>Join Trafford Bus</h1>
      <p>Create an account to buy tickets and track your bus.</p>
  
      <?php if (!empty($error)): ?>
        <p style="margin-top: 5px; color: #ff6b6b !important;"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form action="signup.php" method="POST" class="auth-form">
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="dob">Date of Birth</label>
          <input type="date" id="dob" name="dob" required value="<?= htmlspecialchars($_POST['dob'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="address">Address</label>
          <input type="text" id="address" name="address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <input type="password" id="confirm_password" name="confirm_password" required>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn btn-primary btn-block">Create Account</button>
        </div>
      </form>
    </div>
  </main>

  <footer class="site-footer">
    <div class="container">
      <div class="footer-grid">
        <div class="footer-col">
          <h3>About Us</h3>
          <p>Trafford Bus operates a local bus service within the Trafford area.</p>
        </div>

        <div class="footer-col">
          <h4>Quick Links</h4>
          <ul>
            <li><a href="tickets.php">Tickets</a></li>
            <li><a href="livemap.php">Map</a></li>
            <li><a href="timetable.php">Timetables</a></li>
            <li><a href="journeys.php">Journeys</a></li>
          </ul>
        </div>
      
        <div class="footer-col">
          <h4>Contact Us</h4>
          <p>📧 support@traffordbus.local</p>
          <p>📱 0161</p>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 Trafford Bus. All rights reserved.</p>
      </div>
    </div>
  </footer>
  
  <div class="pwa-promo-container" id="pwaPromo">
    <div class="pwa-text-box">
      <h3>Mobile Users Benefit</h3>
      <p>Install the app for a better experience.</p>
      
      <div class="pwa-btn-group">
        <button id="pwa-dismiss-btn" class="btn btn-outline-small">No Thanks</button>
        <button id="pwa-install-btn" class="btn btn-primary-small">Install App ↓</button>
      </div>
    </div> 

    <div class="phone-mockup">
      <div class="phone-screen">
        <div class="screen-content">
          <span style="font-size: 2rem;">🚌</span>
          <h4>Trafford Bus</h4>
        </div>
      </div>
      <div class="phone-notch"></div>
    </div>
  </div> 

  <script src="pwa.js"></script>
</body>
</html>
