<?php
require 'init.php';

if ($is_logged_in) {
  header('Location: index.php');
  exit;
}

$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  try {
    $sql_users = "SELECT users.user_id, users.email, users.password_hash, users.full_name 
    FROM users 
    WHERE users.email = :email";

    $stmt = $pdo->prepare($sql_users);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
      $_SESSION['loggedin'] = true;
      $_SESSION['userid'] = $user['user_id'];
      $_SESSION['email'] = $user['email'];
      $_SESSION['full_name'] = $user['full_name'];

      header('Location: index.php');
      exit;
    } else {
      $error_msg = 'Invalid email or password.';
    }
  } catch (PDOException $error) {
    $error_msg = $error;
  }
}

require 'header.php';
?>

<main class="site-content" style="margin: 60px auto; max-width: 900px; overflow: hidden;">
  <div class="container auth-card">
    <div class="auth-half login-side">
      <h2>Login</h2>
      <form id="login" action="login.php" method="POST">
        <div class="form-group">
          <label>Email</label>
          <input name="email" type="email" placeholder="Enter email here...">
        </div>
        <div class="form-group">
          <label>Password</label>
          <input name="password" type="password" placeholder="Enter password here...">
        </div>
        <button type="submit" class="btn full-width">Sign In</button>
        <?php if (!empty($error_msg)): ?>
          <p style="margin-top: 5px; color: #ff6b6b !important;"><?= htmlspecialchars($error_msg) ?></p>
        <?php endif; ?>
      </form>
    </div>

    <div class="auth-half register-side">
      <h2>Register</h2>
      <p>New to Trafford Bus? Create an account to plan journeys or access tickets.</p>
      <a class="btn btn-outline full-width" href="signup.php" id="signup">Create Account</a>
    </div>
  </div>
</main>

<?php require 'footer.php'; ?>
