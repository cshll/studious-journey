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

// Grab teacher from the database.
$stmt = $pdo->prepare("SELECT teachers.* FROM teachers WHERE teacher_id = :teacher_id");
$stmt->execute(['teacher_id' => $_GET['id']]);
$teacher = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if teacher is found.
if (!$teacher) {
  die("Teacher not found!");
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
          <a href="#" style="font-weight: 700;">Viewing Teacher</a>

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

      <form action="update_teacher.php" method="POST">
        <section class="card-container">
          <label class="card-header">Teacher Information</label><br>
          <label class="card-title" for="full_name">Full Name: </label>
          <input type="text" name="full_name" 
            value="<?php echo $teacher['full_name']; ?>" 
            required 
            pattern="[a-zA-Z\s\-\']+"
          ><br>

          <label class="card-title" for="address">Address: </label>
          <input type="text" name="address" 
            value="<?php echo $teacher['address']; ?>"
            required 
            pattern="^[a-zA-Z0-9\s,.'\-\/&]+$"
          ><br>
        </section>

        <section class="card-container">
          <label class="card-header">Contact Information</label><br>

          <label class="card-title" for="email">Email: </label>
          <input type="email" name="email" 
            value="<?php echo $teacher['email']; ?>"
            required
            autocomplete="email"
          ><br>

          <label class="card-title" for="phone_number">Phone Number: </label>
          <input type="tel" name="phone_number" 
            value="<?php echo $teacher['phone_number']; ?>"
            required
            autocomplete="tel" 
            pattern="^0[0-9\s]*$"
          ><br>
        </section>

        <section class="card-container">
          <button class="btn btn-primary-grad" name="submit" onclick="return confirm('Are you sure you want to save changes?')">Save All Changes</button>
        </section>

        <input type="hidden" name="id" value="<?php echo htmlspecialchars($teacher['teacher_id']); ?>">
      </form>

      <section class="card-container">
        <label class="card-header">Admin Controls</label><br><br>
        <form action="delete_teacher.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this teacher? This cannot be undone.');" style="display: inline;">
          <input type="hidden" name="id" value="<?php echo $teacher['teacher_id']; ?>">
          <button class="btn btn-primary-grad" type="submit">Delete Teacher</button>
        </form>
      </section>

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
