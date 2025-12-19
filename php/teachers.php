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

// Grab teachers from the database.
$teacher_sql = "SELECT teachers.*, classes.name as class_name 
FROM teachers 
JOIN classes ON teachers.class_id = classes.class_id 
WHERE 1=1";
$teacher_params = [];

// Concatenate the search terms within the SQL statement.
if (!empty($search_term)) {
  $teacher_sql .= " AND full_name LIKE :search";
  $teacher_params['search'] = "%$search_term%";
}

$teacher_sql .= " ORDER BY full_name ASC";

$stmt = $pdo->prepare($teacher_sql);
$stmt->execute($teacher_params);
$teachers = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <a href="teachers.php" style="font-weight: 700;">Teachers</a>

          <a href="index.php">Dashboard</a>

          <a href="classes.php">Classes</a>
          <a href="pupils.php">Pupils</a>
          <a href="guardians.php">Guardians</a>
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
        <form id="pupil_search" action="teachers.php" method="GET">
          <input class="input-text" type="text" id="search" name="search" placeholder="Query by teacher name..." value="<?php echo htmlspecialchars($search_term); ?>"><br><br>
          <button class="btn btn-primary-grad" id="submit" name="submit">Search</button>
        </form>
      </section>

      <div class="card-container">
        <label class="card-header">Teachers</label>
        <?php if (count($teachers) > 0): ?>
          <?php foreach($teachers as $teacher): ?>
            <a class="accordion" style="text-decoration: none; color: unset;"
              href="view_teacher.php?id=<?php echo $teacher['teacher_id']; ?>"
            >
              <?php echo htmlspecialchars($teacher['full_name']); ?>
              <label class="class-badge"><?php echo $teacher['class_name']; ?></label>
            </a>
          <?php endforeach; ?>
        <?php else: ?>
          <label class="card-text">No teachers found.</label>
        <?php endif; ?>
        <br><a href="create_teacher.php" class="btn btn-primary-grad">Create Teacher</a>
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
    </script>

  </body>
</html>
