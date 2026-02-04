<?php
session_start();

require 'connect.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM routes");
$route_count = $stmt->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">
<?php require 'head.php'; ?>
<body class="home-page">
  <?php require 'header.php'; ?>

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

  <?php require 'footer.php'; ?>

  <?php require 'pwa-promo.php'; ?>
</body>
</html>
