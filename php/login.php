<?php
session_start();

require 'connect.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
  header('Location: index.php');
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Company</title>
  <link rel="stylesheet" href="style.css">
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
          <li><a href="#">Tickets</a></li>
          <li><a href="livemap.php">Map</a></li>
          <li><a href="#">Timetables</a></li>
          <li><a href="#">Journeys</a></li>
        </ul>
      </nav>
      <a class="btn btn-primary-grad" href="login.php" id="login">Login</a>
    </div>
  </header>

  <main class="site-content" style="margin: 60px auto; max-width: 900px; overflow: hidden;">
    <div class="container auth-card">
      <div class="auth-half login-side">
        <h2>Login</h2>
        <form>
          <div class="form-group">
            <label>Email</label>
            <input type="email" placeholder="Enter email here...">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" placeholder="Enter password here...">
          </div>
          <button type="submit" class="btn full-width">Sign In</button>
      </div>
      <div class="auth-half register-side">
        <h2>Register</h2>
        <p>New to Trafford Bus? Create an account to plan journeys or access tickets.</p>
        <button class="btn btn-outline full-width">Create Account</button>
      </div>
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
            <li><a href="#">Tickets</a></li>
            <li><a href="livemap.php">Map</a></li>
            <li><a href="#">Timetables</a></li>
            <li><a href="#">Journeys</a></li>
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
</body>
</html>
