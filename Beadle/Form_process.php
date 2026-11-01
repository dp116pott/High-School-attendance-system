<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
header('Content-Type: application/json');

// database information
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
$connection_instance = connect($config);

$id_perm = $_SESSION['beadle_id'] ?? null;

// Read and decode JSON data from the payload
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (
    !isset(
    $data["Form_class"],
    $data["Teacher_name"],
    $data["Subject_name"],
    $data["Date_submit"],
    $data["s_late"],
    $data['s_absent'],
    $data["teacher_status"],
    $data['class_start'],
    $data['time']
) || !$id_perm
) {
    echo json_encode(["status" => "error", "message" => "Missing submission information."]);
    exit;
}

$stud_class = $data['Form_class'];
$stud_teach = $data['Teacher_name'];
$stud_subj = $data['Subject_name'];
$stud_date = $data['Date_submit'];
$stud_late = json_decode($data['s_late'], true);
$stud_abs = json_decode($data['s_absent'], true);
$teach_time_arrive = $data['time'];
$t_stat = $data['teacher_status'];
$start = $data['class_start'];

$startTime = new DateTime($start);
$startTime->add(new DateInterval('PT15M'));
$time_comp = $startTime->format('H:i');

// Access check
$access_query = "
    SELECT si.class_id
    FROM class_list cl
    JOIN Subject_info si ON si.class_id = cl.class_id
    JOIN teacher_info ti ON ti.teacher_id = cl.teacher_id
    WHERE cl.beadle_id = :beadle_id
      AND si.Subject_name = :subject_name
      AND si.Form_class = :form_class
    LIMIT 1
";
$access_stmt = $connection_instance->prepare($access_query);
$access_stmt->bindParam(':beadle_id', $id_perm);
$access_stmt->bindParam(':form_class', $stud_class);
$access_stmt->bindParam(':subject_name', $stud_subj);
$access_stmt->execute();

$result = $access_stmt->fetch(PDO::FETCH_ASSOC);
if (!$result || !isset($result['class_id'])) {
    echo json_encode(["status" => "error", "message" => "Invalid subject/form class or no permission to submit."]);
    exit;
}

$target_class_id = $result['class_id'];

// Fetch student and teacher data
$student_list = $connection_instance->query('SELECT * FROM Student_info')->fetchAll(PDO::FETCH_ASSOC);
$teacher_list = $connection_instance->query('SELECT * FROM teacher_info')->fetchAll(PDO::FETCH_ASSOC);

// Process student attendance
foreach ($student_list as $student) {
    $temp_id = $student['Student_id'];

    if (in_array($student['Student_name'], $stud_late)) {
        $query_stmnt = 'INSERT INTO `student_attendance` (`student_id`, `class_id`, `date_recorded`, `Late`, `Absent`, `start_period`) 
                        VALUES (:stud_id, :s_class, :stud_date, 1, 0, :start_time)';
    } elseif (in_array($student['Student_name'], $stud_abs)) {
        $query_stmnt = 'INSERT INTO `student_attendance` (`student_id`, `class_id`, `date_recorded`, `Late`, `Absent`, `start_period`) 
                        VALUES (:stud_id, :s_class, :stud_date, 0, 1, :start_time)';
    } else {
        continue;
    }

    $insert_stmnt = $connection_instance->prepare($query_stmnt);
    $insert_stmnt->bindParam(':stud_id', $temp_id);
    $insert_stmnt->bindParam(':s_class', $target_class_id);
    $insert_stmnt->bindParam(':stud_date', $stud_date);
    $insert_stmnt->bindParam(':start_time', $start);
    $insert_stmnt->execute();
}

// Process teacher attendance
$teacher_found = false;
foreach ($teacher_list as $teacher) {
    if ($stud_teach == $teacher['teacher_name']) {
        $teacher_found = true;
        $teach_id = $teacher['teacher_id'];

        if ($teach_time_arrive > $time_comp) {
            $query_present = 'INSERT INTO `teacher_attendance`(`teacher_id`, `class_id`, `date_recorded`, `Present`, `T_late`, `Absent`, `time_arrived`, `start_period`) 
                              VALUES (:teach_id, :t_class, :t_date, "1", "1", "", :arr_time, :start_time)';
        } else {
            $query_present = 'INSERT INTO `teacher_attendance`(`teacher_id`, `class_id`, `date_recorded`, `Present`, `T_late`, `Absent`, `time_arrived`, `start_period`) 
                              VALUES (:teach_id, :t_class, :t_date, "1", "", "", :arr_time, :start_time)';
        }

        $query_absent = 'INSERT INTO `teacher_attendance`(`teacher_id`, `class_id`, `date_recorded`, `Present`, `T_late`, `Absent`, `time_arrived`, `start_period`) 
                         VALUES (:teach_id, :t_class, :t_date, "", "", "1", :arr_time, :start_time)';

        $insert_stmt = $connection_instance->prepare($t_stat == 1 ? $query_present : $query_absent);
        $insert_stmt->bindParam(':teach_id', $teach_id);
        $insert_stmt->bindParam(':t_class', $target_class_id);
        $insert_stmt->bindParam(':t_date', $stud_date);
        $insert_stmt->bindParam(':arr_time', $teach_time_arrive);
        $insert_stmt->bindParam(':start_time', $start);
        $insert_stmt->execute();

        break;
    }
}

if (!$teacher_found) {
    echo json_encode(["status" => "error", "message" => "Teacher not found in database."]);
    exit;
}

//  Final success response
echo json_encode([
    "status" => "success",
    "message" => "Attendance successfully submitted."
]);
exit;
