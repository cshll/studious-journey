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
<?php require 'head.php'; ?>
<body class="home-page">
  <?php require 'header.php'; ?>

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

  <?php require 'footer.php'; ?>
  
  <?php require 'pwa-promo.php'; ?>
</body>
</html>
