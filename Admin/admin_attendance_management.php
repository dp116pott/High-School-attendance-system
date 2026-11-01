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
      window.location.href = '../Login_Page/Signin_page.php';
    });
  </script>
</body>
</html>
<?php
exit();


    endif;
//ensures necessary supervisor data is accessible
if (isset($_SESSION['supervisor_name'])):
    $sup_name = $_SESSION['supervisor_name'];
//db info
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);
$currentFormClass = $_GET['pd-class-filter'] ?? 'all'; // This is the initialization for the PD filter on student view. If not set it defaults to all
$pdFilterCondition = $currentFormClass !== 'all' ? 'AND lc.Form_class = :class_filter' : '';//this acts as a condition which filters students accessible by the supervisor based on the pd class they are appart of
$sup_form = $_SESSION['form_managed'];
//implementation of the filters to access students.
switch ($sup_form) {
    case '1st Form':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class IN('1-1','1-2','1-3','1-4','1-5','1-6','1-8') $pdFilterCondition
        ";
        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class IN('1-1','1-2','1-3','1-4','1-5','1-6','1-8')
        GROUP BY teacher_id, teacher_name";
        break;
    case '2nd Form':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class IN('2-1','2-2','2-3','2-4','2-5','2-6','2-8') $pdFilterCondition
        ";
        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class IN('2-1','2-2','2-3','2-4','2-5','2-6','2-8')
        GROUP BY teacher_id, teacher_name";
        break;
    case '3rd Form':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class IN('3-1','3-2','3-3','3-4','3-5','3-6','3-8') $pdFilterCondition
        ";
        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class IN('3-1','3-2','3-3','3-4','3-5','3-6','3-8')
GROUP BY teacher_id, teacher_name";
        break;
    case '4th Form':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class IN('4-1','4-2','4-3','4-4','4-5','4-6','4-7') $pdFilterCondition
        ";
        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class IN('4-1','4-2','4-3','4-4','4-5','4-6','4-7')
        GROUP BY teacher_id, teacher_name";
        break;
    case '5th Form':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class IN('5-1','5-2','5-3','5-4','5-5','5-6','5-7') $pdFilterCondition
        ";
        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class IN('5-1','5-2','5-3','5-4','5-5','5-6','5-7')
        GROUP BY teacher_id, teacher_name";
        break;
    case '6B':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class = '6B' $pdFilterCondition
        ";
        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class = '6B'
        GROUP BY teacher_id, teacher_name";
        break;
    case '6A':
        $query_SQL = "
            SELECT DISTINCT si.Student_name, si.Student_class, si.student_id
            FROM student_info si
            JOIN Student_subject_assignments us ON si.student_id = us.student_id
            JOIN Subject_info lc ON us.class_id = lc.class_id
            WHERE si.Student_class = '6A' $pdFilterCondition
        ";

        $tquery_SQL = "SELECT ti.teacher_id, ti.teacher_name, GROUP_CONCAT(ti.class_id ORDER BY ti.class_id SEPARATOR ', ') AS classes
        FROM teacher_info ti
        JOIN class_list cl on ti.teacher_id = cl.teacher_id
        WHERE cl.form_class = '6A'
        GROUP BY teacher_id, teacher_name";
        break;
    default:
        die("Invalid form access.");
}

function buildFilterCondition($column)
{
    $filter = $_GET['filter'] ?? 'day';
    switch ($filter) {
        case 'day':
            return "AND DATE($column) = CURDATE()";
        case 'week':
            return "AND YEARWEEK($column, 1) = YEARWEEK(CURDATE(), 1)";
        case 'month':
            return "AND MONTH($column) = MONTH(CURDATE()) AND YEAR($column) = YEAR(CURDATE())";
        case 'term':
            return "AND $column >= DATE_SUB(CURDATE(), INTERVAL 3 MONTH)";
        case 'year':
            return "AND YEAR($column) = YEAR(CURDATE())";
        default:
            return '';
    }
}

$query_stmnt = $connection_instance->prepare($query_SQL);
$tquery_stmnt = $connection_instance->prepare($tquery_SQL);
if ($currentFormClass !== 'all') {
    $query_stmnt->bindParam("class_filter", $currentFormClass);
}
$query_stmnt->execute();
$tquery_stmnt->execute();
$student_list = $query_stmnt->fetchAll(PDO::FETCH_OBJ);
$teacher_list = $tquery_stmnt->fetchAll(PDO::FETCH_OBJ);
$attendance_data = [];
foreach ($student_list as $list) {//breaks object down into arrays for each iteration of data stored
    $student_id = $list->student_id;
    //obtains total student absences
    $absent_query = "SELECT COUNT(*) as total_absent FROM student_attendance WHERE Absent = 1 AND student_id = :student_id " . buildFilterCondition('date_recorded');
    $absent_stmt = $connection_instance->prepare($absent_query);
    $absent_stmt->execute(['student_id' => $student_id]);
    $absent_count = $absent_stmt->fetch(PDO::FETCH_ASSOC)['total_absent'];
    //obtains total student lates
    $late_query = "SELECT COUNT(*) as total_late FROM student_attendance WHERE Late = 1 AND student_id = :student_id " . buildFilterCondition('date_recorded');
    $late_stmt = $connection_instance->prepare($late_query);
    $late_stmt->execute(['student_id' => $student_id]);
    $late_count = $late_stmt->fetch(PDO::FETCH_ASSOC)['total_late'];

    $subject_query = "SELECT lc.Subject_name, SUM(CASE WHEN sa.Late = 1 THEN 1 ELSE 0 END) as total_lates, SUM(CASE WHEN sa.Absent = 1 THEN 1 ELSE 0 END) as total_absents
        FROM Student_subject_assignments u
        JOIN Subject_info lc ON u.class_id = lc.class_id
        LEFT JOIN student_attendance sa ON sa.class_id = lc.class_id 
            AND sa.student_id = u.student_id 
            AND sa.class_id = u.class_id " . buildFilterCondition('sa.date_recorded') . "
        WHERE u.student_id = :student_id
        GROUP BY lc.Subject_name";

    $subject_stmt = $connection_instance->prepare($subject_query);
    $subject_stmt->execute(['student_id' => $student_id]);
    $subjects = $subject_stmt->fetchAll(PDO::FETCH_ASSOC);

    //obj with various classes
    $attendance_data[] = [
        'student_id' => $list->student_id,
        'name' => $list->Student_name,
        'class' => $list->Student_class,
        'absent' => $absent_count,
        'late' => $late_count,
        'subjects' => $subjects
    ];
}
//similar explanation as above
foreach ($teacher_list as $teacher) {
    $teacher_id = $teacher->teacher_id;
    $tabsent_query = "SELECT COUNT(*) as teacher_total_absent FROM teacher_attendance WHERE Absent = 1 AND teacher_id = :teacher_id " . buildFilterCondition('date_recorded');
    $tabsent_stmt = $connection_instance->prepare($tabsent_query);
    $tabsent_stmt->execute(['teacher_id' => $teacher_id]);
    $tabsent_count = $tabsent_stmt->fetch(PDO::FETCH_ASSOC)['teacher_total_absent'];

    $tlate_query = "SELECT COUNT(*) as teacher_total_late FROM teacher_attendance WHERE T_late = 1 AND teacher_id = :teacher_id " . buildFilterCondition('date_recorded');
    $tlate_stmt = $connection_instance->prepare($tlate_query);
    $tlate_stmt->execute(['teacher_id' => $teacher_id]);
    $tlate_count = $tlate_stmt->fetch(PDO::FETCH_ASSOC)['teacher_total_late'];

    $tsubject_query = "SELECT lc.Subject_name, SUM(CASE WHEN ta.T_late = 1 THEN 1 ELSE 0 END) AS teacher_total_lates,SUM(CASE WHEN ta.Absent = 1 THEN 1 ELSE 0 END) as teacher_total_absents
        FROM class_list cl
        JOIN Subject_info lc ON cl.class_id = lc.class_id
        LEFT JOIN teacher_attendance ta ON ta.class_id = lc.class_id 
            AND ta.teacher_id = cl.teacher_id " . buildFilterCondition('ta.date_recorded') . "
        WHERE cl.teacher_id = :teacher_id
        GROUP BY lc.Subject_name";
    $tsubject_stmt = $connection_instance->prepare($tsubject_query);
    $tsubject_stmt->execute(['teacher_id' => $teacher_id]);
    $tsubjects = $tsubject_stmt->fetchAll(PDO::FETCH_ASSOC);

    $attendance_data[] = [
        'name' => $teacher->teacher_name,
        'taught-class' => $teacher->classes,
        't_late' => $tlate_count,
        'absent' => $tabsent_count,
        'subjects' => $tsubjects
    ];
}
?>

<!DOCTYPE html>
<lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Attendance Summary Table</title>
        <!-- References the CSS used to build this page's display -->
        <link href="http://localhost/campion_Beadle_Slip/CSS_stylesheets/Adminattendance_styles.css" rel="stylesheet">
        <!-- Alerts design reference -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <div>
        <div class="sidebar">
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    // Get all cards and add click event listener
                    document.querySelectorAll(".card").forEach(card => {
                        card.addEventListener("click", function () {
                            const link = this.getAttribute("data-link");
                            if (link) {
                                window.location.href = link; // Redirect on click CHECK THIS
                            }
                        });
                    });
                });

            </script>
            <h2>Dashboard</h2>
            <ul>
                <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_dashboard.php">Home</a></li>
                <li><a
                        href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_attendance_management.php">Attendance</a>
                </li>
                <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_analytics.php">Analytics</a></li>
                <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_settings.php">Settings</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="selection-bar">
                <div class="view-toggle">
                    <button class="toggle-button active">Student View</button>
                    <button class="toggle-button">Teacher View</button>
                </div>
                <?php $currentFilter = $_GET['filter'] ?? 'day'; ?>
                <div class="filter-toggle">
                    <label for="filter-range">Filter by:</label>
                    <select id="filter-range">
                        <option value="day" <?= $currentFilter === 'day' ? 'selected' : '' ?>>Daily</option>
                        <option value="week" <?= $currentFilter === 'week' ? 'selected' : '' ?>>Weekly</option>
                        <option value="month" <?= $currentFilter === 'month' ? 'selected' : '' ?>>Monthly</option>
                        <option value="term" <?= $currentFilter === 'term' ? 'selected' : '' ?>>Termly</option>
                        <option value="year" <?= $currentFilter === 'year' ? 'selected' : '' ?>>Yearly</option>
                    </select>
                </div>
                <div class="filter-toggle" id="pd-class-filter-container" style="margin-left: 20px;">
                    <label for="pd-class-filter">Form Class:</label>
                    <select id="pd-class-filter">
                        <?php
                        // Check if form is 6A or 6B
                        if ($sup_form === '6A' || $sup_form === '6B') {
                            ?>
                            <option value="all" <?= ($currentFormClass ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                            <?php
                        } else {
                            // Extract numeric prefix to use as 'initial'
                            $initial = intval($sup_form); // e.g. "3A" → 3
                            ?>
                            <option value="all" <?= ($currentFormClass ?? '') === 'all' ? 'selected' : '' ?>>All</option>
                            <?php for ($i = 1; $i <= 8; $i++):
                                if (($initial == 4) || ($initial == 5)) {
                                    if ($i !== 8) {
                                        $form_value = "$initial-$i";
                                    } else {
                                        continue;
                                    }
                                } else {
                                    if ($i !== 7) {
                                        $form_value = "$initial-$i";
                                    } else {
                                        continue;
                                    }
                                }
                                ?>
                                <option value="<?= $form_value ?>" <?= ($currentFormClass ?? '') === $form_value ? 'selected' : '' ?>>
                                    <?= $form_value ?>
                                </option>
                            <?php endfor;
                        }
                        ?>
                    </select>
                </div>

            </div>

            <h1>Attendance Summary for <?= htmlspecialchars($sup_form) ?></h1>
            <div id="student-view">
                <div class="table-scroll">
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Class</th>
                                    <th>Total Absences</th>
                                    <th>Total Lates</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance_data as $student): ?>
                                    <?php if (isset($student['late'])): // only students have 'late' so this effectively differentiates between teachers and students ?>
                                        <tr class="summary-row">
                                            <td>
                                                <a href="student_class_assignment.php?student_id=<?= urlencode($student['student_id'] ?? '') ?>"
                                                    class="student-link">
                                                    <?= htmlspecialchars($student['name']) ?>
                                                </a>
                                            </td>

                                            <td><?= htmlspecialchars($student['class']) ?></td>
                                            <td><?= $student['absent'] ?></td>
                                            <td><?= $student['late'] ?></td>
                                        </tr>
                                        <tr class="detail-row" style="display: none;">
                                            <td colspan="4">
                                                <div class="student-card">
                                                    <h3><?= htmlspecialchars($student['name']) ?></h3>
                                                    <p><strong>Class:</strong> <?= htmlspecialchars($student['class']) ?></p>
                                                    <p><strong>Total Absences:</strong> <?= $student['absent'] ?></p>
                                                    <p><strong>Total Lates:</strong> <?= $student['late'] ?></p>
                                                    <p><strong>Enrolled Subjects:</strong></p>
                                                    <ul>
                                                        <?php
                                                        $most_late_subject = null;
                                                        $most_late_count = 0;
                                                        $most_absent_subject = null;
                                                        $most_absent_count = 0;

                                                        foreach ($student['subjects'] as $subject) {
                                                            if ($subject['total_lates'] > $most_late_count) {
                                                                $most_late_subject = $subject['Subject_name'];
                                                                $most_late_count = $subject['total_lates'];
                                                            }
                                                            if ($subject['total_absents'] > $most_absent_count) {
                                                                $most_absent_subject = $subject['Subject_name'];
                                                                $most_absent_count = $subject['total_absents'];
                                                            }
                                                        }

                                                        foreach ($student['subjects'] as $subject): ?>
                                                            <li>
                                                                <?= htmlspecialchars($subject['Subject_name']) ?>:
                                                                <?= $subject['total_absents'] ?> absents,
                                                                <?= $subject['total_lates'] ?> lates
                                                            </li>
                                                        <?php endforeach; ?>
                                                    </ul>

                                                    <p><strong>Most Frequently Late Subject:</strong>
                                                        <?= $most_late_subject ?? 'N/A' ?></p>
                                                    <p><strong>Most Frequently Absent Subject:</strong>
                                                        <?= $most_absent_subject ?? 'N/A' ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>

                    </div>

                </div>
                <div class="export-button-container">
                    <button id="showStudentExport" class="export-buttons">Student Attendance Export</button>
                </div>
            </div>
            <div id="teacher-view" style="display: none;">
                    <div class="table-scroll">
                    <table>
                        <thead>
                            <tr>
                                <th>Teacher Name</th>
                                <th>Total Lates</th>
                                <th>Total Absences</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attendance_data as $teacher): ?>
                                <?php
                                if (!isset($teacher['late'])): // this filters between the teacher and student views although teachers can still be late I created a new variable to hold that ?>
                                    <tr class="summary-row">
                                        <td><?= htmlspecialchars($teacher['name']) ?></td>
                                        <?php $subjectNames = array_column($teacher['subjects'], 'Subject_name'); ?>
                                        <td><?= $teacher['t_late'] ?></td>
                                        <td><?= $teacher['absent'] ?></td>
                                    </tr>
                                    <tr class="detail-row" style="display: none;">
                                        <td colspan="3">
                                            <div class="student-card">
                                                <h3><?= htmlspecialchars($teacher['name']) ?></h3>
                                                <p><strong>Total Lates:</strong> <?= $teacher['t_late'] ?></p>
                                                <p><strong>Total Absences:</strong> <?= $teacher['absent'] ?></p>
                                                <p><strong>Subjects Taught:</strong></p>
                                                <ul>
                                                    <?php foreach ($teacher['subjects'] as $subject): ?>
                                                        <li><?= htmlspecialchars($subject['Subject_name']) ?>:
                                                            <?= $subject['teacher_total_lates'] ?> lates,
                                                            <?= $subject['teacher_total_absents'] ?> absents
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>               
                </div>
                <div class="export-button-container">
                    <button id="showTeacherExport" class="export-buttons">Teacher Attendance Export</button>
                </div>
            </div>



        </div>
    </div>
    <script src="http://localhost/campion_Beadle_Slip/JS_docs/Adminattendance.js"></script>
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
            location.replace("../Login_Page/Signin_page.php");
        });
    </script>
</body>
</html>
<?php

endif;
?>