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

// Select the pupil frpm the database.
$pupil_stmt = $pdo->prepare("SELECT pupils.* FROM pupils WHERE pupil_id = :pupil_id");
$pupil_stmt->execute(['pupil_id' => $_GET['id']]);
$pupil = $pupil_stmt->fetch(PDO::FETCH_ASSOC);

try {
  // Grab classes that are under capacity or matching the pupils class ID from the database.
  $class_sql = "SELECT classes.class_id, classes.name 
  FROM classes 
  LEFT JOIN pupils ON classes.class_id = pupils.class_id 
  GROUP BY classes.class_id, classes.name, classes.capacity 
  HAVING COUNT(pupils.pupil_id) < classes.capacity OR classes.class_id = :class_id 
  ORDER BY classes.name ASC";

  $class_stmt = $pdo->prepare($class_sql);
  $class_stmt->execute(['class_id' => $pupil['class_id']]);
  $classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
  die("Unknown error!");
}

// Check if pupil is found.
if (!$pupil) {
  die("Pupil not found!");
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
          <a href="#" style="font-weight: 700;">Viewing Pupil</a>

          <?php if ($_SESSION['usertype'] != 'admin'): ?>
            <a href="my_settings.php">My Settings</a>
          <?php endif; ?>

          <a href="index.php">Dashboard</a>

          <a href="classes.php">Classes</a>
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

      <form action="update_pupil.php" method="POST">
        <section class="card-container">
          <label class="card-header">Pupil Information</label><br>
          <label class="card-title" for="full_name">Full Name: </label>
          <input type="text" name="full_name" 
            value="<?php echo $pupil['full_name']; ?>" 
            <?php if ($_SESSION['usertype'] != 'admin'): ?>
              readonly style="cursor: not-allowed;"
            <?php endif; ?> 
            required 
            pattern="[a-zA-Z\s\-\']+"
          ><br>

          <label class="card-title" for="birthday">Date of Birth: </label>
          <input type="date" name="birthday"
            value="<?php echo date('Y-m-d', strtotime($pupil['birthday'])); ?>"
            <?php if ($_SESSION['usertype'] != 'admin'): ?>
              readonly style="cursor: not-allowed;"
            <?php endif; ?> 
            required
          ><br>

          <label class="card-title" for="address">Address: </label>
          <input type="text" name="address" 
            value="<?php echo $pupil['address']; ?>"
            <?php if ($_SESSION['usertype'] != 'admin'): ?>
              readonly style="cursor: not-allowed;"
            <?php endif; ?> 
            required 
            pattern="^[a-zA-Z0-9\s,.'\-\/&]+$"
          ><br>

          <label class="card-title" for="medical_info">Medical Information: </label><br>
          <textarea name="medical_info" rows="4" cols="46"
            <?php if ($_SESSION['usertype'] != 'admin'): ?>
              readonly style="cursor: not-allowed;"
            <?php endif; ?>
          ><?php echo htmlspecialchars($pupil['medical_info'] ?: 'None recorded'); ?></textarea>
        </section>

        <section class="card-container">
          <label for="class_id" class="card-header">Class</label><br>
          <select name="class_id" id="class_id" required
            <?php if ($_SESSION['usertype'] != 'admin'): ?>
              readonly style="cursor: not-allowed;"
            <?php endif; ?>
          >
            <option value="">Select Class</option>
            <?php foreach ($classes as $class): ?>
              <option value="<?php echo htmlspecialchars($class['class_id']); ?>"
                <?php
                  if ($class['class_id'] == $pupil['class_id']) {
                    echo 'selected';
                  }
                ?>
              >
                <?php echo htmlspecialchars($class['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </section>

        <section class="card-container">
          <label class="card-header">Guardians</label><br>
        </section> 
        
        <?php if ($_SESSION['usertype'] == 'admin'): ?>
          <section class="card-container">
            <button class="btn btn-primary-grad" name="submit" onclick="return confirm('Are you sure you want to save changes?')">Save All Changes</button>
          </section>
        <?php endif; ?>

        <input type="hidden" name="id" value="<?php echo htmlspecialchars($pupil['pupil_id']); ?>">
      </form>

      <?php if ($_SESSION['usertype'] == 'admin'): ?>
        <section class="card-container">
          <label class="card-header">Admin Controls</label><br><br>
          <form action="delete_pupil.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this pupil? This cannot be undone.');" style="display: inline;">
            <input type="hidden" name="id" value="<?php echo $pupil['pupil_id']; ?>">
            <button class="btn btn-primary-grad" type="submit">Delete Pupil</button>
          </form>
        </section>
      <?php endif; ?>

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
