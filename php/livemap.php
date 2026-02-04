<?php
require 'init.php';

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <div id="liveMap" style="width: 100%; height: 500px; border-radius: 2%;"></div>
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
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

      const busMarkers = {};
      const busPaths = {};
      const socket = io('http://localhost:3000');

      socket.on('initRoutes', (allRoutes) => {
        Object.keys(allRoutes).forEach(routeId => {
          const routeData = allRoutes[routeId];
          const latLngs = routeData.path.map(coord => [coord[1], coord[0]]);

          L.polyline(latLngs, {
            color: routeData.color,
            weight: 4,
            opacity: 0.7
          }).addTo(liveMap);
        });
      });

      socket.on('busesUpdate', (buses) => {
        buses.forEach(bus => {
          if (busMarkers[bus.id]) {
            busMarkers[bus.id].setLatLng([bus.lat, bus.lng]);
          } else {
            const marker = L.marker([bus.lat, bus.lng]).addTo(liveMap);
            marker.bindPopup(`<b>${bus.id}</b><br>Route: ${bus.route}`);
            busMarkers[bus.id] = marker;
          }
        });
      });
      //Insert Reference for Leaflet and Thunderforest API
      //leaflet-locatecontrol-gh-pages
    </script>
  </div>
  
  <!-- Container for displaying bus data -->
  <div id="busDataOutput" class="container"></div>
</main>

  <?php require 'footer.php'; ?>
