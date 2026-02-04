<header class="site-header">
  <div class="container header-flex">
    <div class="logo">
      <?php require 'icon-logo.php'; ?>
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
