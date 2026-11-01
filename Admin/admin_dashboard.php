<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
if (isset($_SESSION['admin_id'], $_SESSION['admin_id_expiry'])):
  if (time() > $_SESSION['admin_id_expiry']):
    session_unset();
    session_destroy();
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8">
      <title>Session Expired</title>
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>
      <script>
        Swal.fire({
          icon: 'error',
          title: 'Session Expired',
          text: 'Please log in again.',
          confirmButtonText: 'Login'
        }).then(() => {
          location.replace("../Login_Page/Signin_page.php");
        });
      </script>
    </body>

    </html>
    <?php
    exit();
  endif;
  //ensures that necessary supervisor data is available
  if (isset($_SESSION['supervisor_name'])):
    $sup_name = $_SESSION['supervisor_name'];
    //db info
    include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
    include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
    $connection_instance = connect($config);
    $sup_form = $_SESSION['form_managed'];
    //This switch differentiates between each of the grade supervisors and allows each supervisor access to their specific authorized cohort
    switch ($sup_form) {
      case '1st Form':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class IN("1-1","1-2","1-3","1-4","1-5","1-6","1-8")';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'lower class';
        break;
      case '2nd Form':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class IN("2-1","2-2","2-3","2-4","2-5","2-6","2-8")';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'lower class';
        break;
      case '3rd Form':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class IN("3-1","3-2","3-3","3-4","3-5","3-6","3-8")';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'lower class';
        break;
      case '4th Form':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class IN("4-1","4-2","4-3","4-4","4-5","4-6","4-7")';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'upper class';
        break;
      case '5th Form':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class IN("5-1","5-2","5-3","5-4","5-5","5-6","5-7")';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'upper class';
        break;
      case '6B':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class = "6B"';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'upper class';
        break;
      case '6A':
        $query_SQL = 'SELECT Student_name, Student_class, student_id FROM Student_info WHERE Student_class = "6A"';
        $query_stmnt = $connection_instance->prepare($query_SQL);
        $query_stmnt->execute();
        $student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
        $classification = 'upper class';
        break;
    }
    $count = 0;
    $temp = 0;
    foreach ($student_list as $list) {
      $student_id = $list->student_id;
      // This query assertains the total amount of absences a student has recorded
      $absent_query = 'SELECT COUNT(*) as total_absent FROM student_attendance WHERE Absent = 1 AND student_id = :student_id';
      $absent_stmt = $connection_instance->prepare($absent_query);
      $absent_stmt->execute(['student_id' => $student_id]);
      $absent_result = $absent_stmt->fetch(PDO::FETCH_ASSOC);
      $absent_count = $absent_result['total_absent'];

      // This query assertains the total amount of lates a student has recorded
      $late_query = 'SELECT COUNT(*) as total_late FROM student_attendance WHERE Late = 1 AND student_id = :student_id';
      $late_stmt = $connection_instance->prepare($late_query);
      $late_stmt->execute(['student_id' => $student_id]);
      $late_result = $late_stmt->fetch(PDO::FETCH_ASSOC);
      $late_count = $late_result['total_late'];

      // Calculate punctuality score (1 absent = 2 lates)
      $score = $absent_count * 2 + $late_count;

      // Store in array
      $punctuality_scores[] = [
        'id' => $student_id,
        'absent' => $absent_count,
        'late' => $late_count,
        'score' => $score
      ];
    }
    $count = count($punctuality_scores);
    //bubble sort in descending order
    for ($z = 0; $z < $count - 1; $z++) {
      for ($i = 0; $i < $count - $z - 1; $i++) {
        if ($punctuality_scores[$i]['score'] < $punctuality_scores[$i + 1]['score']) {
          // Swap entire entries
          $temp = $punctuality_scores[$i];
          $punctuality_scores[$i] = $punctuality_scores[$i + 1];
          $punctuality_scores[$i + 1] = $temp;
        }
      }
    }
    // Get top 5 worst punctual students
    $top_5_worst = array_slice($punctuality_scores, 0, 5);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>Admin Dashboard</title>
      <!-- References the CSS used to build this page's display -->
      <link href="http://localhost/campion_Beadle_Slip/CSS_stylesheets/Admindash_styles.css" rel="stylesheet">
      <!-- Alerts design reference -->
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>

      <nav class="sidebar">
        <h2>Dashboard</h2>
        <ul>
          <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_dashboard.php" class="active">Home</a></li>
          <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_attendance_management.php">Attendance</a>
          </li>
          <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_analytics.php">Analytics</a></li>
          <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_settings.php">Settings</a></li>
        </ul>
      </nav>

      <main class="main-content">
        <h1>Welcome to Your Admin Dashboard, <?= htmlspecialchars($sup_name) ?></h1>
        <section class="dashboard">
          <div class="dashcard"
            data-link="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_attendance_management.php">
            <h3>Attendance</h3>
            <p>View detailed reports on student records</p>
          </div>
          <div class="dashcard" data-link="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_settings.php">
            <h3>Settings</h3>
            <p>Customize student data</p>
          </div>
        </section>
      </main>

      <aside class="bottom-right-widget" aria-label="Most Frequent Student Lack of Punctuality">
        <h2>Attendance Concerns</h2>
        <div class="slider-container">
          <div id="student-card-container">
            <?php
            // This is the container which showcases the worst punctuality for students
            $index = 0;
            foreach (array_slice($top_5_worst, 0, 5) as $entry) {
              $student_info = array_filter($student_list, fn($s) => $s->student_id === $entry['id']);
              $student_info = array_values($student_info)[0];
              echo "<div class='card' id='card-$index'>
                  <h2>" . htmlspecialchars($student_info->Student_name) . "</h2>
                  <p><span class='highlight'>Class:</span> " . htmlspecialchars($student_info->Student_class) . "</p>
                  <p><span class='highlight'>Total Absences:</span> {$entry['absent']}</p>
                  <p><span class='highlight'>Total Lates:</span> {$entry['late']}</p>
                  <p><span class='highlight'>Attendance Score:</span> {$entry['score']}</p>
                </div>";
              $index++;
            }
            ?>
          </div>
          <button class="arrow left" aria-label="Previous">&#10094;</button>
          <button class="arrow right" aria-label="Next">&#10095;</button>
        </div>
      </aside>
      <script src="http://localhost/campion_Beadle_Slip/JS_docs/Admindash.js"></script>
    </body>

    </html>
    <?php
  endif;
else:
  ?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <title>Session Expired</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  </head>

  <body>
    <script>
      Swal.fire({
        icon: 'error',
        title: 'Session Expired',
        text: 'You are not logged in. Please sign in.',
        confirmButtonText: 'Go to Login'
      }).then(() => {
        window.location.href = '../Login_Page/Signin_page.php';
      });
    </script>
  </body>

  </html>
  <?php
endif;
?>