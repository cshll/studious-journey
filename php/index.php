<?php
session_start();

require 'check.php';
require 'connect.php';

$class_stmt = $pdo->query("SELECT * FROM classes ORDER BY name ASC");
$all_classes = $class_stmt->fetchAll(PDO::FETCH_ASSOC);

$search_term = $_GET['search'] ?? '';
$filter_class = $_GET['filter'] ?? '';

$sql_pupil = "SELECT pupils.*, classes.name as class_name
FROM pupils
LEFT JOIN classes ON pupils.class_id = classes.class_id
WHERE 1=1";

$sql_teacher = "SELECT teachers.*, classes.name as class_name
FROM teachers
LEFT JOIN classes ON teachers.class_id = classes.class_id
WHERE 1=1";

$params = [];

if (!empty($filter_class) && $filter_class != 'all') {
  $sql_pupil .= " AND pupils.class_id = :class_id";
  $sql_teacher .= " AND teachers.class_id = :class_id";
  
  $params['class_id'] = $filter_class;
}

$pupil_params = $params;

if (!empty($search_term)) {
  $sql_pupil .= " AND (pupils.full_name LIKE :search)";
  
  $pupil_params['search'] = "%$search_term%";
}

$pupil_stmt = $pdo->prepare($sql_pupil);
$pupil_stmt->execute($pupil_params);
$pupils = $pupil_stmt->fetchAll(PDO::FETCH_ASSOC);

$teacher_stmt = $pdo->prepare($sql_teacher);
$teacher_stmt->execute($params);
$teachers = $teacher_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>
  <title>St Alphonsus Primary School</title>
  <link rel="stylesheet" href="style.css">
</head>

<body>
  <header>
    <div id="left">
      <h1>St Alphonsus Primary School</h1>
      <h2>Control Panel</h2>
    </div>
    <div id="right">
      <h3><?php echo $_SESSION['username']; ?>&nbsp;&nbsp;</h3>
      <a href="logout.php" id="logout">Logout</a>
    </div>
  </header>
  <main>
    <div id="content">
      <div id="container">
        <form action="index.php" method="GET">
          <label id="btext">Search</label><br>
          <label>Search for specific pupils and show their corresponding teacher.</label><br><br>
          <label id="htext">Filter by Class</label><br>
          <select name="filter" id="filter">
            <option value="all">All Classes</option>
            <?php foreach ($all_classes as $class): ?>
              <option value="<?php echo $class['class_id']; ?>" <?php if ($filter_class == $class['class_id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($class['name']); ?>
              </option>
            <?php endforeach; ?>
          </select><br><br>
          <label id="htext">Query by Name</label><br>
          <input type="text" id="search" name="search" placeholder="Enter pupil name..." value="<?php echo htmlspecialchars($search_term); ?>"><br><br>
          <button type="submit">Search</button>
        </form>
      </div>
      <pre></pre>
      <div id="container">
        <label id="btext">Teachers</label><br>
        <label>You are viewing <?php echo count($teachers); ?> entries.</label><br><br>
        <table id="rows">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Address</th>
              <th>Email</th>
              <th>Phone Number</th>
              <th>Class</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>
            <?php if (count($teachers) > 0): ?>
              <?php foreach ($teachers as $teacher): ?>
                <tr>
                  <td><?php echo htmlspecialchars($teacher['teacher_id']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['address']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['email']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['phone_number']); ?></td>
                  <td><?php echo htmlspecialchars($teacher['class_name']); ?></td>
                  
                  <td>
                    <div class="actions">
                      <a href="view_teacher.php?id=<?php echo $teacher['teacher_id']; ?>">View</a>
                      <a href="edit_teacher.php?id=<?php echo $teacher['teacher_id']; ?>">Edit</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="9" style="text-align: center;">No Teachers found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
      <pre></pre>
      <div id="container">
        <label id="btext">Pupils</label><br>
        <label>You are viewing <?php echo count($pupils); ?> entries.</label><br><br>
        <table id="rows">
          <thead>
            <tr>
              <th>ID</th>
              <th>Full Name</th>
              <th>Address</th>
              <th>Birthday</th>
              <th>Medical Info</th>
              <th>Class</th>
              <th>Options</th> 
            </tr>
          </thead>
          <tbody>
            <?php if (count($pupils) > 0): ?>
              <?php foreach ($pupils as $pupil): ?>
                <tr>
                  <td><?php echo htmlspecialchars($pupil['pupil_id']); ?></td>
                  <td><?php echo htmlspecialchars($pupil['full_name']); ?></td>
                  <td><?php echo htmlspecialchars($pupil['address']); ?></td>
                  <td><?php echo htmlspecialchars($pupil['birthday']); ?></td>
                  <td><?php echo htmlspecialchars($pupil['medical_info'] ?? ''); ?></td>
                  <td><?php echo htmlspecialchars($pupil['class_name']); ?></td>

                  <td>
                    <div class="actions">
                      <a href="view_pupil.php?id=<?php echo $pupil['pupil_id']; ?>">View</a>
                      <a href="edit_pupil.php?id=<?php echo $pupil['pupil_id']; ?>">Edit</a>
                      <a href="delete_pupil.php?id=<?php echo $pupil['pupil_id']; ?>" onclick="return confirm(`Are you sure you want to delete ID: <?php echo $pupil['pupil_id']; ?> NAME: <?php echo $pupil['full_name']; ?> forever?`)">Delete</a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="7" style="text-align: center;">No pupils found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
    <pre></pre>
  </main>
  <footer></footer>
</body>

</html>
