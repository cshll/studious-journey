<?php
session_start();

require 'connect.php';

$search = $_GET['search'] ?? '';
$search_sql = "SELECT trips.trip_id, routes.route_number, trips.trip_headsign 
FROM trips 
JOIN routes ON trips.route_id = routes.route_id 
WHERE routes.route_number LIKE ? OR trips.trip_headsign LIKE ? 
ORDER BY routes.route_number ASC";

$stmt_list = $pdo->prepare($search_sql);
$stmt_list->execute(["%$search%", "%$search%"]);
$all_trips = $stmt_list->fetchAll();

$selected_trip = null;
$trip_stops = [];

if (isset($_GET['trip_id'])) {
  $trip_id = $_GET['trip_id'];

  $info_sql = "SELECT trips.*, routes.route_number, routes.route_name 
  FROM trips 
  JOIN routes ON trips.route_id = routes.route_id 
  WHERE trips.trip_id = ?";

  $stmt_info = $pdo->prepare($info_sql);
  $stmt_info->execute([$trip_id]);
  $selected_trip = $stmt_info->fetch();

  $stops_sql = "SELECT stops.stop_name, stops.latitude, stops.longitude, stop_times.arrival_time 
  FROM stop_times 
  JOIN stops ON stop_times.stop_id = stops.stop_id 
  WHERE stop_times.trip_id = ? 
  ORDER BY stop_times.stop_sequence ASC";

  $stmt_stops = $pdo->prepare($stops_sql);
  $stmt_stops->execute([$trip_id]);
  $trip_stops = $stmt_stops->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Bus Company</title>
  <link rel="stylesheet" href="style.css">
  <!-- AI NEEDS REFERENCES - PWA Manifest link -->
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
      <h1>Timetable</h1>
      <div class="seach-bar-wrapper" style="width: 100%;">
        <form action="timetable.php" method="GET" class="search-form">
          <input type="text" name="search" placeholder="Search routes here..." value="<?php echo htmlspecialchars($search); ?>">
          <?php if (isset($_GET['trip_id'])): ?>
            <input type="hidden" name="trip_id" value="<?php echo $_GET['trip_id']; ?>">
          <?php endif; ?>
          <button type="submit" class="btn">Search</button>
        </form>
      </div>

      <div class="dashboard-grid">
        <div class="trip-list-panel">
          <h3>Available Trips</h3>
          <div class="trip-scroller">
            <?php if (count($all_trips) > 0): ?>
              <?php foreach ($all_trips as $trip): ?>
                <?php $is_active = (isset($_GET['trip_id']) && $_GET['trip_id'] == $trip['trip_id']) ? 'active-trip' : ''?>
                <a href="timetable.php?trip_id=<?php echo $trip['trip_id']; ?>&search=<?php echo htmlspecialchars($search); ?>"
                  class="trip-card-item <?php echo $is_active; ?>">
                  <span class="badge-route"><?php echo htmlspecialchars($trip['route_number']); ?></span>
                  <span class="trip-dest"><?php echo htmlspecialchars($trip['trip_headsign']); ?></span>
                  <span class="arrow">→</span>
                </a>
              <?php endforeach; ?>
            <?php else: ?>
              <p class="no-results">No trips found.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <div class="trip-detail-panel">
        <?php if ($selected_trip): ?>
          <div class="detail-header">
            <h2>
              <span class="big-badge"><?php echo htmlspecialchars($selected_trip['route_number']); ?></span>
              Route to <?php echo htmlspecialchars($selected_trip['trip_headsign']); ?>
            </h2>
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
                  <td class="time-slot"><?php echo htmlspecialchars($stop['arrival_time']); ?></td>
                  <td>
                    <?php if ($stop['latitude']): ?>
                      <a href="https://www.google.com/maps?q=<?php echo $stop['latitude']; ?>,<?php echo $stop['longitude']; ?>"
                        target="_blank" class="map-link">📍 View</a>
                    <?php else: ?>
                      <span class="text-muted">-</span>
                    <?php endif; ?>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="empty-state">
            <div style="font-size: 3rem;">🚌</div>
            <h3>Select a trip to view the schedule</h3>
            <p>Choose a route from the list.</p>
          </div>
        <?php endif; ?>
      </div>
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
  <!-- AI NEEDS REFERENCES - Service Worker Registration -->
  <script>
    // AI NEEDS REFERENCES - PWA installation detection
    let deferredPrompt;
    
    // AI NEEDS REFERENCES - Before install prompt event
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      deferredPrompt = e;
    });
    
    // AI NEEDS REFERENCES - App installed event
    window.addEventListener('appinstalled', () => {
      deferredPrompt = null;
    });
    
    // AI NEEDS REFERENCES - Service Worker Registration
    if ('serviceWorker' in navigator) {
      navigator.serviceWorker.register('/sw.js')
        .catch((error) => {
          // AI NEEDS REFERENCES - console.error for service worker registration failure
          console.error('Service Worker registration failed:', error);
        });
    }
  </script>
</body>
</html>
