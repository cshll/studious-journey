<?php
session_start();

require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

if ($_SESSION['usertype'] != 'admin') {
  die("403: You are not authorized to access this resource.");
}

// Pull down classes where the capacity is not full. 
try {
  $class_sql = "SELECT classes.class_id, classes.name 
  FROM classes 
  LEFT JOIN pupils ON classes.class_id = pupils.class_id 
  GROUP BY classes.class_id, classes.name, classes.capacity 
  HAVING COUNT(pupils.pupil_id) < classes.capacity 
  ORDER BY classes.name ASC";

  $class_stmt = $pdo->query($class_sql);
  $classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $error) {
  die("Unknown error!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['full_name'], $_POST['birthday'], $_POST['address'], $_POST['class_id'])) {
  // Check if class ID is valid or within the classes array.
  $class_id = filter_input(INPUT_POST, 'class_id', FILTER_VALIDATE_INT);
  if (!$class_id || !in_array($class_id, array_column($classes, 'class_id'))) {
    die("Invalid class ID provided.");
  }

  // Check if full name is valid.
  $full_name = trim(strip_tags($_POST['full_name']));
  if (empty($full_name) || strlen($full_name) > 100 || strlen($full_name) < 2) {
    die("Invalid full name provided.");
  }

  // Check if the given date is a valid date.
  $birthday = $_POST['birthday'];
  $date_object = DateTime::createFromFormat('Y-m-d', $birthday);
  if (!$date_object || $date_object->format('Y-m-d') !== $birthday) {
    die("Invalid birthday provided.");
  }

  $today = new DateTime();
  if ($date_object > $today) {
    die("Invalid birthday provided.");
  }

  // Check if address is valid.
  $address = trim(strip_tags($_POST['address']));
  if (empty($address)) {
    die("Invalid address provided.");
  }
  
  $medical_info = !empty($_POST['medical_info']) ? trim(strip_tags($_POST['medical_info'])) : null;

  // Insert the pupil data into the database.
  try {
    $pupil_sql = "INSERT INTO pupils (full_name, address, birthday, medical_info, class_id) 
    VALUES (:full_name, :address, :birthday, :medical_info, :class_id)";

    $stmt = $pdo->prepare($pupil_sql);
    $stmt->execute([
      'full_name' => $full_name,
      'address' => $address,
      'birthday' => $birthday,
      'medical_info' => $medical_info,
      'class_id' => $class_id
    ]);

    echo '<script>window.history.back();</script>';
    exit;
  } catch (PDOException $error) {
    die("Unknown error!");
  }
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
          <a href="#" style="font-weight: 700;">Creating Pupil</a>

          <a href="index.php">Dashboard</a>

          <a href="classes.php">Classes</a>
          <a href="pupils.php">Pupils</a>
          <a href="guardians.php">Guardians</a>

          <a href="teachers.php">Teachers</a>
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

      <form action="create_pupil.php" method="POST">
        <section class="card-container">
          <label class="card-header">Pupil Information</label><br>
          <label class="card-title" for="full_name">Full Name: </label>
          <input type="text" name="full_name" 
            required 
            pattern="[a-zA-Z\s\-\']+"
          ><br>

          <label class="card-title" for="birthday">Date of Birth: </label>
          <input type="date" name="birthday"
            required
          ><br>

          <label class="card-title" for="address">Address: </label>
          <input type="text" name="address" 
            required 
            pattern="^[a-zA-Z0-9\s,.'\-\/&]+$"
          ><br>

          <label class="card-title" for="medical_info">Medical Information: </label><br>
          <textarea name="medical_info" rows="4" cols="46"></textarea>
        </section>

        <section class="card-container">
          <label for="class_id" class="card-header">Class</label><br>
          <select name="class_id" id="class_id" required>
            <option value="">Select Class</option>
            <?php foreach ($classes as $class): ?>
              <option value="<?php echo htmlspecialchars($class['class_id']); ?>">
                <?php echo htmlspecialchars($class['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </section>

        <section class="card-container">
          <label class="card-header">Guardians</label><br>
        </section> 

        <section class="card-container">
          <button class="btn btn-primary-grad" name="submit" onclick="return confirm('Are you sure you want to save changes?')">Save All Changes</button>
        </section>
      </form>

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
