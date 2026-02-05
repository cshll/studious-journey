<?php
require 'init.php';

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <div id="live-map" style="width: 100%; height: 500px; border-radius: 2%;"></div>
    <script src="http://localhost:3000/socket.io/socket.io.js"></script>
    <script src="start-map.js"></script> 
  </div> 
</main>

<?php require 'footer.php'; ?>
