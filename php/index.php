<?php
require 'init.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM routes");
$route_count = $stmt->fetchColumn();

$body_class = 'home-page';
require 'header.php';
?>

<main class="site-content">
  <section class="hero-section">
    <div class="hero-overlay">
      <h1>Welcome to Trafford Bus</h1>
      <p>Serving <?= $route_count ?> routes in Greater Manchester.</p> 
      <a href="#about" class="scroll-down-arrow">⌄</a>
    </div>
  </section>

  <section id="about" class="content-section">
    <div class="container">
      <?php require 'load-about.php' ?>
    </div>
  </section> 
</main>

<?php require 'footer.php'; ?>
