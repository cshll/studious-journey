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

  $schedule_sql = "SELECT trips.trip_headsign, MIN(stop_times.arrival_time) as start_time 
    FROM trips 
    JOIN stop_times ON trips.trip_id = stop_times.trip_id 
    WHERE trips.route_id = ? 
    GROUP BY trips.trip_id 
    ORDER BY start_time ASC";
  $stmt_schedule = $pdo->prepare($schedule_sql);
  $stmt_schedule->execute([$route_id]);
  $daily_schedule = $stmt_schedule->fetchAll();

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
<?php require 'head.php'; ?>
<body class="home-page">
  <?php require 'header.php'; ?>

  <main class="site-content">
    <div class="container">
      <?php if (!$route_id): ?>
        <div class="seach-bar-wrapper" style="width: 100%;">
          <form action="timetable.php" method="GET" class="search-form">
            <input type="text" name="search" placeholder="Search routes here..." value="<?= htmlspecialchars($search) ?>">
            <?php if ($route_id): ?>
              <input type="hidden" name="route_id" value="<?= htmlspecialchars($route_id) ?>">
            <?php endif; ?>
            <button type="submit" class="btn">Search</button>
          </form>
        </div>

        <hr class="divider">

        <div class="trip-list-panel" style="width: 100%; max-width: 100%;">
          <?php if (count($all_routes) > 0): ?>
            <div class="routes-grid">
              <?php foreach ($all_routes as $route): ?>
                <a href="timetable.php?route_id=<?= $route['route_id'] ?>" class="route-btn-card">
                  <span class="route-number-large"><?= htmlspecialchars($route['route_number']) ?></span>
                  <span class="route-name"><?= htmlspecialchars($route['route_name']) ?></span>
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
          <a href="timetable.php" style="display: inline-block; margin-bottom: 15px; font-weight: bold; color: #1565c0;">← Back to All Routes</a>
          <h2>Route <?= htmlspecialchars($selected_route['route_number']) ?></h2>
        </span>

        <div class="next-bus-hero">
          <?php if ($next_trip): ?>
            <div class="hero-label">Next Bus Departing At</div>
            <div class="hero-time"><?= date('H:i', strtotime($next_trip['start_time'])) ?></div>
            <div class="hero-dest">To <?= htmlspecialchars($next_trip['trip_headsign']) ?></div>
          <?php else: ?>
            <div class="hero-time">End of Service</div>
            <p>No more buses scheduled for today.</p>
          <?php endif; ?>
        </div>

        <?php if ($next_trip): ?>
          <div class="trip-detail-panel" style="display: block;">
            <h3>Current Schedule</h3>
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
                    <td class="stop-name"><?= htmlspecialchars($stop['stop_name']) ?></td>
                    <td class="time-slot"><?= date('H:i', strtotime($stop['arrival_time'])) ?></td>
                    <td>
                      <?php if ($stop['latitude']): ?>
                        <a href="https://www.google.com/maps?q=<?= $stop['latitude'] ?>,<?= $stop['longitude'] ?>"
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

        <div class="daily-schedule-container">
          <h3>Daily Departures</h3>
          <div class="table-responsive">
            <table class="schedule-compact">
              <thead>
                <tr>
                  <th>Depart</th>
                  <th>To</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($daily_schedule as $trip):
                  $trip_time = $trip['start_time'];
                    
                  if ($trip_time < $current_time) {
                    $status = "Departed";
                    $row_class = "row-past";
                  } elseif ($next_trip && $trip_time == $next_trip['start_time']) {
                    $status = "Next Bus";
                    $row_class = "row-next";
                  } else {
                    $status = "On Time";
                    $row_class = "";
                  }
                ?>
                  <tr class="<?= $row_class ?>">
                    <td class="time-cell"><?= date('H:i', strtotime($trip_time)) ?></td>
                    <td><?= htmlspecialchars($trip['trip_headsign']) ?></td>
                    <td><span class="status-badge <?= strtolower(str_replace(' ', '-', $status)) ?>"><?= $status ?></span></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <?php require 'footer.php'; ?>
  
  <?php require 'pwa-promo.php'; ?>
</body>
</html>
