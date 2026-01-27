<?php
session_start();

require 'connect.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM routes");
$route_count = $stmt->fetchColumn();
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
<body class="home-page">
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
    <section class="hero-section">
      <div class="hero-overlay">
        <h1>Welcome to Trafford Bus</h1>
        <p>Serving <?php echo $route_count; ?> routes in Greater Manchester.</p>
        
        <a href="#about" class="scroll-down-arrow">v</a>
      </div>
    </section>

    <section id="about" class="content-section">
      <div class="container">
        <div class="info-grid">
          <div class="info-card">
          </div>
<!--TODO:
  IMPLEMENT INFO CARDS
-->
        </div>
      </div>
    </section> 
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
      <button id="pwa-install-btn" class="btn btn-primary">Install App ↓</button>
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

  <script>
    // AI NEEDS REFERENCES - Service Worker Registration
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
        .catch((error) => {
          // AI NEEDS REFERENCES - console.error for service worker registration failure
          console.error('Service Worker registration failed:', error);
        });
    }

    let deferredPrompt;
    const pwaContainer = document.getElementById('pwaPromo');
    const installBtn = document.getElementById('pwa-install-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
      pwaContainer.style.display = 'flex';
    });

    installBtn.addEventListener('click', async () => {
      if (deferredPrompt) {
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response to install prompt: ${outcome}`);
        deferredPrompt = null;
        pwaContainer.style.display = 'none';
      }
    });

    window.addEventListener('appinstalled', () => {
      pwaContainer.style.display = 'none';
      deferredPrompt = null;
    });
  </script>
</body>
</html>
