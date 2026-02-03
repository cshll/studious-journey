<?php
session_start();

require 'connect.php';

$stops_sql = "SELECT stop_id, stop_name FROM stops ORDER BY stop_name ASC";
$stmt_stops = $pdo->query($stops_sql);
$all_stops = $stmt_stops->fetchAll(PDO::FETCH_ASSOC);

$results = [];
$search_performed = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['origin_id']) && isset($_GET['dest_id'])) {
  $search_performed = true;
  $origin_id = $_GET['origin_id'];
  $dest_id = $_GET['dest_id'];
  $time_input = $_GET['time'] ?? date('H:i');

  if ($origin_id == $dest_id) {
    $error = "Please select different start and end locations.";
  } else {
    $sql = "SELECT 
        routes.route_number,
        routes.route_name,
        trips.trip_id,
        st_start.arrival_time AS depart_time,
        st_end.arrival_time AS arrive_time 
      FROM trips 
      JOIN routes ON trips.route_id = routes.route_id 
      JOIN stop_times st_start ON trips.trip_id = st_start.trip_id 
      JOIN stop_times st_end ON trips.trip_id = st_end.trip_id 
      WHERE st_start.stop_id = :origin 
        AND st_end.stop_id = :dest 
        AND st_start.stop_sequence < st_end.stop_sequence 
        AND st_start.arrival_time >= :time_input 
      ORDER BY st_start.arrival_time ASC
      LIMIT 5";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
      'origin' => $origin_id,
      'dest' => $dest_id,
      'time_input' => $time_input
    ]);

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Journeys - Bus Company</title>
  <link rel="stylesheet" href="style.css">
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
      <form action="journeys.php" method="GET" class="planner-form-inline">
        <div class="form-row">
          <div class="input-group">
            <label for="origin">From</label>
            <select name="origin_id" id="origin" required>
              <option value="" disabled selected>Start Location</option>
              <?php foreach ($all_stops as $stop): ?>
                <option value="<?= $stop['stop_id'] ?>"
                  <?= (isset($_GET['origin_id']) && $_GET['origin_id'] == $stop['stop_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($stop['stop_name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="input-group">
            <label for="dest">To</label>
            <select name="dest_id" id="dest" required>
              <option value="" disabled selected>End Location</option>
              <?php foreach ($all_stops as $stop): ?>
                <option value="<?= $stop['stop_id'] ?>"
                  <?= (isset($_GET['dest_id']) && $_GET['dest_id'] == $stop['stop_id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($stop['stop_name']) ?>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="input-group time-group">
            <label for="time">Leaving at</label>
            <input type="time" name="time" id="time" value="<?= $_GET['time'] ?? date('H:i') ?>" required>
          </div>

          <button type="submit" class="btn btn-primary search-btn">Search</button>
        </div>
      </form>

      <hr class="divider">

      <?php if ($error): ?>
        <p class="error-msg"><?= $error ?></p>
      <?php elseif ($search_performed): ?>
        <div class="journey-list">
          <?php if (count($results) > 0): ?>
            <h3>Available Buses</h3>
            <?php foreach ($results as $row): ?>
              <div class="journey-item">
                <div class="bus-info">
                  <span class="route-number"><?= htmlspecialchars($row['route_number']) ?></span>
                  <div class="route-times">
                    <strong><?= substr($row['depart_time'], 0, 5) ?></strong>
                    <span>to</span>
                    <strong><?= substr($row['arrive_time'], 0, 5) ?></strong>
                  </div>
                </div>
                <div class="bus-action">
                  <span class="route-name"><?= htmlspecialchars($row['route_name']) ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      <?php endif; ?>
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
  
  <div class="pwa-promo-container" id="pwaPromo">
    <div class="pwa-text-box">
      <h3>Mobile Users Benefit</h3>
      <p>Install the app for a better experience.</p>

      <div class="pwa-btn-group">
        <button id="pwa-dismiss-btn" class="btn btn-outline-small">No Thanks</button>
        <button id="pwa-install-btn" class="btn btn-primary-small">Install App ↓</button>
      </div>
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

  <script src="pwa.js"></script>
</body>
</html>
