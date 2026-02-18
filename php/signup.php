<?php
require 'init.php';

if ($is_logged_in) {
  header("Location: index.php");
  exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $full_name = trim(strip_tags($_POST['full_name']));
  $dob = $_POST['dob'];
  $address = trim(strip_tags($_POST['address']));
  $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
  $password = $_POST['password'];
  $confirm_password = $_POST['confirm_password'];

  $date_object = DateTime::createFromFormat('Y-m-d', $dob);

  // Ensure all information is correct, valid, and secure.
  if (empty($full_name) || empty($dob) || empty($address) || empty($email) || empty($password)) {
    $error = "All fields are required!";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
    $error = "Invalid email provided!";
  } elseif (strlen($full_name) > 100 || strlen($full_name) < 2) {
    $error = "Invalid full name provided!";
  } elseif (!$date_object || $date_object->format('Y-m-d') !== $dob) {
    $error = "Invalid date of birth provided!";
  } elseif ($password !== $confirm_password) {
    $error = "Password do not match!";
  } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    $error = "Password must be at least 8 characters long, contain a number, and a capital letter!";
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

      // Try to create user on database.
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

require 'header.php';
?>

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
