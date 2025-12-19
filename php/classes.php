<?php
session_start();

require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['usertype'] != 'admin' && $_SESSION['usertype'] != 'teacher') {
  die("403: You are not authorized to access this resource.");
}

$search_term = $_GET['search'] ?? '';

// Pull down classes from database.
$class_sql = "SELECT classes.*, teachers.full_name as teacher_name, teachers.teacher_id as teacher_id 
FROM classes 
LEFT JOIN teachers ON classes.class_id = teachers.class_id 
WHERE 1=1 
ORDER BY classes.class_id ASC";

$stmt = $pdo->query($class_sql);
$classes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Pull down pupils from database.
$pupil_sql = "SELECT * FROM pupils WHERE 1=1";
$pupil_params = [];

// Add search terms to the SQL statement.
if (!empty($search_term)) {
  $pupil_sql .= " AND full_name LIKE :search";
  $pupil_params['search'] = "%$search_term%";
}

$pupil_sql .= " ORDER BY full_name ASC";

$stmt = $pdo->prepare($pupil_sql);
$stmt->execute($pupil_params);
$pupils = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to grab pupils for a specific class, returns pupils within a class.
function getPupilsForClass($class_id, $pupils) {
  $results = [];
  foreach ($pupils as $pupil) {
    if ($pupil['class_id'] == $class_id) {
      $results[] = $pupil;
    }
  }
  return $results;
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>St Alphonsus Primary School - Control Panel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
  </head>

  <body>
    <div class="app-container">

      <div id="nav-bar" class="overlay">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>

        <div class="overlay-content">
          <a href="classes.php" style="font-weight: 700;">Classes</a>

          <?php if ($_SESSION['usertype'] != 'admin'): ?>
            <a href="my_settings.php">My Settings</a>
          <?php endif; ?>

          <a href="index.php">Dashboard</a>

          <a href="pupils.php">Pupils</a>
          <a href="guardians.php">Guardians</a>

          <?php if ($_SESSION['usertype'] == 'admin'): ?>
            <a href="teachers.php">Teachers</a>
          <?php endif; ?> 
        </div>
      </div>

      <header class="app-header">
        <button class="nobtn hamburger" id="hamburger-menu" onclick="openNav()">
          <svg width="35" height="40" viewBox="0 0 24 24" fill="none" style="stroke: var(--text-slate);" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="3" y1="12" x2="21" y2="12"></line>
            <line x1="3" y1="6" x2="21" y2="6"></line>
            <line x1="3" y1="18" x2="21" y2="18"></line>
          </svg>
        </button>

        <section>
            <?php echo $_SESSION['username']; ?>&nbsp;&nbsp;
            <a class="btn btn-primary-grad" href="logout.php" id="logout">Logout</a>
        </section>
      </header>

      <section class="card-container">
        <label class="card-header">Search</label><br>
        <form id="pupil_search" action="classes.php" method="GET">
          <input class="input-text" type="text" id="search" name="search" placeholder="Query by pupil name..." value="<?php echo htmlspecialchars($search_term); ?>"><br><br>
          <button class="btn btn-primary-grad" id="submit" name="submit">Search</button>
        </form>
      </section>

      <div class="card-container">
        <label class="card-header">Classes</label>
        <?php if (count($classes) > 0): ?>
          <?php foreach($classes as $class): ?>

            <?php 
              $class_pupils = getPupilsForClass($class['class_id'], $pupils);
              $count = count($class_pupils);
            ?>
            
            <?php if ((!empty($search_term) && $count > 0) || empty($search_term)): ?>
              <button class="accordion">
                <?php echo htmlspecialchars($class['name']); ?>
                <label class="class-badge"><?php echo $count; ?>/<?php echo $class['capacity']; ?></label>
              </button>

              <div class="panel">
                <?php if (!empty($class['teacher_id'])): ?>
                  <a style="text-decoration: none; color: unset" class="accordion" 
                    href="view_teacher.php?id=<?php echo $class['teacher_id']; ?>"
                  >
                    <?php echo htmlspecialchars($class['teacher_name']); ?>
                    <label class="class-badge">Teacher</label>
                  </a>
                <?php else: ?>
                  <label class="card-text">No teacher assigned.</label>
                <?php endif; ?>

                <?php foreach ($class_pupils as $pupil): ?>
                  <a style="text-decoration: none; color: unset;" class="accordion" 
                    href="view_pupil.php?id=<?php echo $pupil['pupil_id']; ?>"
                  >
                    <?php echo htmlspecialchars($pupil['full_name']); ?>
                    <label class="class-badge">Pupil</label>
                  </a>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <label class="card-text">No classes have been found.</label>
        <?php endif; ?>
      </div>

      <footer class="app-footer">
        <p class="footer-copy">St Alphonsus Primary School<br>Control Panel</p>
      </footer>

    </div>

    <script>
      function openNav() {
        document.getElementById("nav-bar").style.width = "100%";
      }
      
      function closeNav() {
        document.getElementById("nav-bar").style.width = "0%";
      }

      var accordion = document.getElementsByClassName("accordion");
      var i;

      for (i = 0; i < accordion.length; i++) {
        accordion[i].addEventListener("click", function() {
          this.classList.toggle("active");
          
          var panel = this.nextElementSibling;
          if (panel.style.display === "block") {
            panel.style.display = "none";
          } else {
            panel.style.display = "block";
          }
        });
      }
    </script>

  </body>
</html> 
