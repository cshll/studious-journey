<?php
require 'init.php';

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
        routes.route_id, 
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

require 'header.php';
?>

<main class="site-content">
  <div class="container">
    <div class="journeys-wrapper">
      <h1>Plan a Journey</h1>
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
    </div>

    <?php if ($error || ($search_performed && count($results) > 0)): ?>
      <hr class="divider">
    <?php endif; ?>

    <?php if (!empty($error)): ?>
      <p style="margin-top: 5px; color: #ff6b6b !important;"><?= htmlspecialchars($error) ?></p>
    <?php elseif ($search_performed): ?>
      <div class="journey-list">
        <?php if (count($results) > 0): ?>
          <h3>Available Buses</h3>
          <?php foreach ($results as $row): ?>
          <a href="timetable.php?route_id=<?= $row['route_id'] ?>">
              <div class="journey-item">
                <span class="route-number"><?= htmlspecialchars($row['route_number']) ?></span>
                <div class="bus-info-action">
                  <div class="bus-info">
                    <div class="route-times">
                      <strong><?= substr($row['depart_time'], 0, 5) ?></strong>
                      <span>to</span>
                      <strong><?= substr($row['arrive_time'], 0, 5) ?></strong>
                    </div>
                  </div>

                  <div class="bus-action">
                    <span class="route-name">To <?= htmlspecialchars($row['route_name']) ?></span>
                  </div>
                </div>
              </div>
            </a>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php require 'footer.php'; ?>
