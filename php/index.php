<?php
session_start();

require 'connect.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
  header('Location: login.php');
  exit;
}

// Pull down all the notices from the database.
$stmt = $pdo->query("SELECT notices.* FROM notices");
$notices = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <a href="index.php" style="font-weight: 700;">Dashboard</a>

          <?php if ($_SESSION['usertype'] != 'admin'): ?>
            <a href="my_settings.php">My Settings</a>
          <?php endif; ?>

          <?php if ($_SESSION['usertype'] == 'admin' || $_SESSION['usertype'] == 'teacher'): ?>
            <a href="classes.php">Classes</a>
            <a href="pupils.php">Pupils</a>
            <a href="guardians.php">Guardians</a>
          <?php endif; ?>

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
        <div class="row-box">
          <img class="card-avatar" src="https://placehold.co/100" />
          <div class="column-box">
            <label class="card-title">Welcome, <?php echo $_SESSION['fullname']; ?>!</label>
            <label class="card-text">It is currently <?php echo getdate()['weekday']; ?> and the time is <?php echo date('h:iA'); ?>.</label>
          </div>
        </div>
      </section>

      <?php if ($_SESSION['usertype'] == 'guardian'): ?>
        <?php
          $my_pupils = [];

          // Pull down the guardian from the database using the session user id.
          $stmt = $pdo->prepare("SELECT guardian_id, full_name FROM guardians WHERE user_id = :user_id");
          $stmt->execute(['user_id' => $_SESSION['userid']]);
          $guardian = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($guardian) {
            // Pull down the guardians children from the database.
            $guardian_id = $guardian['guardian_id'];

            $sql = "SELECT pupils.full_name, pupils.birthday, pupils.medical_info, classes.name as class_name 
            FROM pupils 
            JOIN guardian_pupil ON pupils.pupil_id = guardian_pupil.pupil_id 
            JOIN classes ON pupils.class_id = classes.class_id 
            WHERE guardian_pupil.guardian_id = :guardian_id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute(['guardian_id' => $guardian_id]);
            $my_pupils = $stmt->fetchAll(PDO::FETCH_ASSOC);
          }
        ?>

        <section class="card-container">
          <label class="card-header">Your Children</label>
          
          <?php if (count($my_pupils) > 0): ?>  
            <?php foreach ($my_pupils as $pupil): ?>
              <button class="accordion">
                <?php echo htmlspecialchars($pupil['full_name']); ?>
                <label class="class-badge"><?php echo htmlspecialchars($pupil['class_name']); ?></label>
              </button>
            
              <div class="panel">
                <label class="card-title">Teacher: </label>
                <label class="card-text"></label><br>
                <label class="card-title">Date of Birth: </label>
                <label class="card-text"><?php echo date("d M Y", strtotime($pupil['birthday'])); ?><br>
                <label class="card-title">Medical Information: </label>
                <label class="card-text"><?php echo htmlspecialchars($pupil['medical_info'] ?: 'None recorded'); ?></label>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <label class="card-text">No pupils linked to your account.</label>
          <?php endif; ?>
        </section>
      <?php endif; ?>

      <?php if ($_SESSION['usertype'] == 'teacher'): ?>
        <?php
          // Pull down the teacher from the database using the session user id.
          $teacher_sql = "SELECT teachers.*, job_type.name, job_type.annual_salary, (job_type.annual_salary / 12) as monthly_gross 
          FROM teachers 
          LEFT JOIN job_type ON teachers.job_id = job_type.job_id 
          WHERE teachers.teacher_id = :teacher_id";

          $stmt = $pdo->prepare($teacher_sql);
          $stmt->execute(['teacher_id' => $_SESSION['userid']]);
          $teacher = $stmt->fetch(PDO::FETCH_ASSOC);

          // Format annual salary into monthly gross.
          $monthly_formatted = number_format($teacher['monthly_gross'], 2);

          // Grab the date for the last day of the current month. 
          $date = new DateTime('last day of this month');

          if ($date->format('N') > 5) {
            $date->modify('last Friday');
          }

          $pay_day = $date->format('l jS'); // Formats as Day of the week Day (Numeric).
          $pay_month = $date->format('F'); // Formats as Month.
        ?>

        <section class="card-container">
          <label class="card-header">Salary</label><br>
          <label class="card-text">Your monthly payment of <strong>£<?php echo $monthly_formatted; ?></strong> is due to be credited to your nominated bank account on <strong><?php echo $pay_day; ?>, <?php echo $pay_month; ?></strong>.<br><small>* <em>Please talk to the <strong>school officer</strong> to discuss or make changes to your salary.</em></small></label>
        </section>
      <?php endif; ?>

      <?php if ($_SESSION['usertype'] == 'admin'): ?>
        <section class="card-container">
          <label class="card-header">Create Notice</label><br>
          <form id="create-notice" action="create_notice.php" method="POST">
            <label for="date" class="card-title">Date: </label>
            <input type="date" name="date" id="date" required><br>
            <label for="title" class="card-title">Title: </label>
            <input type="text" name="title" id="title"><br>
            <label for="description" class="card-title">Description: </label><br>
            <textarea name="description" id="description" rows="4" cols="45" required></textarea><br><br>
            <button type="submit" class="btn btn-primary-grad">Submit</button>
          </form>
        </section>
      <?php endif; ?>

      <section class="card-container">
        <label class="card-header">Notice Board</label><br>
        <?php if (count($notices) > 0): ?>
          <?php foreach($notices as $notice): ?>
            <?php if ($notice['notice_date'] >= date('Y-m-d') ): ?>
              <button class="accordion">
                <?php echo date("d M Y", strtotime($notice['notice_date'])); ?>
                <?php if (!empty($notice['title'])): ?>
                  <label class="class-badge"><?php echo htmlspecialchars($notice['title']); ?></label>
                <?php endif; ?> 
              </button>
              <div class="panel">
                <label class="card-text"><?php echo htmlspecialchars($notice['description']); ?></label><br>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        <?php else: ?>
          <label class="card-text">No upcoming notices.</label>
        <?php endif; ?>
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
