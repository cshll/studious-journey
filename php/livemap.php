<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Company</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
     integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
     crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
     integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
     crossorigin=""></script>
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.86.0/dist/L.Control.Locate.min.css" />
     <script src="https://cdn.jsdelivr.net/npm/leaflet.locatecontrol@0.86.0/dist/L.Control.Locate.min.js" charset="utf-8"></script>
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

  <main class="site-content">
    <div class="container">
      <h1>Map of Trafford</h1>
    <div id="liveMap" style="width: 100%; height: 500px;"></div>
    <script>
        var liveMap = L.map('liveMap').setView([53.4189361, -2.3592972], 13);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
    }).addTo(liveMap);
    L.control.locate().addTo(liveMap);
    //Insert Reference for Leaflet and Thunderforest API
    //leaflet-locatecontrol-gh-pages
    </script>
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
      <div class="footer-bottom"</div>
        <p>&copy; 2026 Trafford Bus. All rights reserved.</p>
      </div>
    </div>
  </footer>
</body>
</html>
