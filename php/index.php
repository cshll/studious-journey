<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Company</title>
  <link rel="stylesheet" href="style.css">
  <!-- AI NEEDS REFERENCES - PWA Manifest link -->
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
      <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
        <a class="btn btn-header" href="logout.php" id="logout">Logout</a>
      <?php else: ?>
        <a class="btn btn-header" href="login.php" id="login">Login</a>
      <?php endif; ?>
    </div>
  </header>

  <main class="site-content">
    <div class="container">
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
  <!-- AI NEEDS REFERENCES - Service Worker Registration -->
  <script>
    // AI NEEDS REFERENCES - PWA installation detection
    let deferredPrompt;
    
    // AI NEEDS REFERENCES - Before install prompt event
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
    });
    
    // AI NEEDS REFERENCES - App installed event
    window.addEventListener('appinstalled', () => {
      deferredPrompt = null;
    });
    
    // AI NEEDS REFERENCES - Service Worker Registration
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
        .catch((error) => {
          // AI NEEDS REFERENCES - console.error for service worker registration failure
          console.error('Service Worker registration failed:', error);
        });
    }
  </script>
</body>
</html>
