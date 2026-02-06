<?php
require 'init.php';

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <div class="wrapper map">
      <h1>Live Map</h1>
      <p>Select an icon to view the route for that service.</p>
      <div id="live-map" style="width: 100%; height: 500px; border-radius: 2%;"></div>
      <script src="http://localhost:3000/socket.io/socket.io.js"></script>
      <script src="start-map.js"></script>
    </div>
    <!-- Socket.io is used to connect to the server -->
    <!-- start-map.js is used to start the map -->
    <!--OpenStreetMap is used to display the map -->
  </div> 
</main>

<?php require 'footer.php'; ?>
