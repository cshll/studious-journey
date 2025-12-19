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

// Pull down the correct guardian from the database.
$guardian_sql = "SELECT * FROM guardians WHERE 1=1";
$guardian_params = [];

// Concatenate search terms to SQL statement.
if (!empty($search_term)) {
  $guardian_sql .= " AND full_name LIKE :search";
  $guardian_params['search'] = "%$search_term%";
}

$guardian_sql .= " ORDER BY full_name ASC";

$stmt = $pdo->prepare($guardian_sql);
$stmt->execute($guardian_params);
$guardians = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <a href="guardians.php" style="font-weight: 700;">Guardians</a>

          <?php if ($_SESSION['usertype'] != 'admin'): ?>
            <a href="my_settings.php">My Settings</a>
          <?php endif; ?>

          <a href="index.php">Dashboard</a>

          <a href="classes.php">Classes</a>
          <a href="pupils.php">Pupils</a>

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
        <form id="pupil_search" action="guardians.php" method="GET">
          <input class="input-text" type="text" id="search" name="search" placeholder="Query by guardian name..." value="<?php echo htmlspecialchars($search_term); ?>"><br><br>
          <button class="btn btn-primary-grad" id="submit" name="submit">Search</button>
        </form>
      </section>

      <div class="card-container">
        <label class="card-header">Guardians</label>
        <?php if (count($guardians) > 0): ?>
          <?php foreach($guardians as $guardian): ?>
            <a class="accordion" style="text-decoration: none; color: unset;"
              href="view_guardian.php?id=<?php echo $guardian['guardian_id']; ?>"
            >
              <?php echo htmlspecialchars($guardian['full_name']); ?>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <label class="card-text">No guardians found.</label>
        <?php endif; ?>
        <br><a href="create_guardian.php" class="btn btn-primary-grad">Create Guardian</a>
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
