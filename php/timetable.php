<?php
session_start();

require 'connect.php';

$search = $_GET['search'] ?? '';
$search_params = [];

if ($search) {
  $routes_sql = "SELECT * FROM routes WHERE route_number LIKE ? OR route_name LIKE ? ORDER BY route_number ASC";
  $search_params = ["%$search%", "%$search%"];
} else {
  $routes_sql = "SELECT * FROM routes ORDER BY route_number ASC";
}

$stmt_routes = $pdo->prepare($routes_sql);
$stmt_routes->execute($search_params);
$all_routes = $stmt_routes->fetchAll();

$selected_route = null;
$next_trip = null;
$trip_stops = [];
$route_id = $_GET['route_id'] ?? null;

if ($route_id) {
  $r_sql = "SELECT * FROM routes WHERE route_id = ?";
  $stmt_r = $pdo->prepare($r_sql);
  $stmt_r->execute([$route_id]);
  $selected_route = $stmt_r->fetch();

  $current_time = date('H:i:s');

  $next_sql = "SELECT trips.*, MIN(stop_times.arrival_time) as start_time 
    FROM trips 
    JOIN stop_times ON trips.trip_id = stop_times.trip_id 
    WHERE trips.route_id = ? 
    GROUP BY trips.trip_id 
    HAVING start_time >= ? 
    ORDER BY start_time ASC 
    LIMIT 1";
  $stmt_next = $pdo->prepare($next_sql);
  $stmt_next->execute([$route_id, $current_time]);
  $next_trip = $stmt_next->fetch();

  if ($next_trip) {
    $stops_sql = "SELECT stops.stop_name, stops.latitude, stops.longitude, stop_times.arrival_time 
      FROM stop_times 
      JOIN stops ON stop_times.stop_id = stops.stop_id 
      WHERE stop_times.trip_id = ? 
      ORDER BY stop_times.stop_sequence ASC";
    $stmt_stops = $pdo->prepare($stops_sql);
    $stmt_stops->execute([$next_trip['trip_id']]);
    $trip_stops = $stmt_stops->fetchAll();
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Company</title>
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
    <div class="container" style="display: flex; flex-wrap: wrap; gap: 25px;">
      <?php if (!$route_id): ?>
        <div class="seach-bar-wrapper" style="width: 100%;">
          <form action="timetable.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search routes here..." value="<?php echo htmlspecialchars($search); ?>">
            <?php if ($route_id): ?>
              <input type="hidden" name="route_id" value="<?php echo htmlspecialchars($route_id); ?>">
            <?php endif; ?>
            <button type="submit" class="btn">Search</button>
          </form>
        </div>

        <div class="trip-list-panel" style="width: 100%; max-width: 100%;">
          <?php if (count($all_routes) > 0): ?>
            <div class="routes-grid">
              <?php foreach ($all_routes as $route): ?>
                <a href="timetable.php?route_id=<?php echo $route['route_id']; ?>" class="route-btn-card">
                  <span class="route-number-large"><?php echo htmlspecialchars($route['route_number']); ?></span>
                  <span class="route-name"><?php echo htmlspecialchars($route['route_name']); ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p>No routes found.</p>
          <?php endif; ?>
        </div>
      <?php endif; ?>

      <?php if ($route_id && $selected_route): ?>
        <span>
          <a href="timetable.php" style="display: inline-block; margin-bottom: 15px; font-weight: bold;">← Back to All Routes</a>
          <h2>Route <?php echo htmlspecialchars($selected_route['route_number']); ?></h2>
        </span>

        <div class="next-bus-hero">
          <?php if ($next_trip): ?>
            <div class="hero-label">Next Bus Departing At</div>
            <div class="hero-time"><?php echo date('H:i', strtotime($next_trip['start_time'])); ?></div>
            <div class="hero-dest">To <?php echo htmlspecialchars($next_trip['trip_headsign']); ?></div>
          <?php else: ?>
            <div class="hero-time">End of Service</div>
            <p>No more buses scheduled for today.</p>
          <?php endif; ?>
        </div>

        <?php if ($next_trip): ?>
          <div class="trip-detail-panel" style="display: block;">
            <div class="detail-header">
                <h3>Full Schedule (Current Trip)</h3>
            </div>
        
            <table class="bus-table">
              <thead>
                <tr>
                  <th>Stop Name</th>
                  <th>Arrival</th>
                  <th>Map</th>
                </tr>
              </thead>

              <tbody>
                <?php foreach ($trip_stops as $stop): ?>
                  <tr>
                    <td class="stop-name"><?php echo htmlspecialchars($stop['stop_name']); ?></td>
                    <td class="time-slot"><?php echo date('H:i', strtotime($stop['arrival_time'])); ?></td>
                    <td>
                      <?php if ($stop['latitude']): ?>
                        <a href="https://www.google.com/maps?q=<?php echo $stop['latitude']; ?>,<?php echo $stop['longitude']; ?>"
                          target="_blank" class="map-link">📍 View</a>
                        <?php else: ?>
                          -
                        <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php endif; ?>
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
