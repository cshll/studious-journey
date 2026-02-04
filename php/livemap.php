<?php
require 'init.php';

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <div id="liveMap" style="width: 100%; height: 500px; border-radius: 2%;"></div>
    <script>
      var liveMap = L.map('liveMap', {
        zoomControl: false
      }).setView([53.4189361, -2.3592972], 13);

      L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>'
      }).addTo(liveMap);
      
      L.control.locate({
        position: 'bottomright'
      }).addTo(liveMap);

      L.control.zoom({
        position: 'bottomright'
      }).addTo(liveMap);
      //Insert Reference for Leaflet and Thunderforest API
      //leaflet-locatecontrol-gh-pages
    </script>
  </div>
  
  <!-- Container for displaying bus data -->
  <div id="busDataOutput" class="container"></div>
</main>

  <?php require 'footer.php'; ?>
