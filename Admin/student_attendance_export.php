<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
//db connection
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';

try {//new db connection
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sup_form = $_SESSION['form_managed'] ?? '';
    $filter = $_GET['filter'] ?? 'day';  // default to 'day' if not set

    // Defines class groupings
    $form_classes = [
        '1stForm' => ['1-1', '1-2', '1-3', '1-4', '1-5', '1-6', '1-8'],
        '2ndForm' => ['2-1', '2-2', '2-3', '2-4', '2-5', '2-6', '2-8'],
        '3rdForm' => ['3-1', '3-2', '3-3', '3-4', '3-5', '3-6', '3-8'],
        '4thForm' => ['4-1', '4-2', '4-3', '4-4', '4-5', '4-6', '4-7'],
        '5thForm' => ['5-1', '5-2', '5-3', '5-4', '5-5', '5-6', '5-7'],
        '6A'      => ['6A'],
        '6B'      => ['6B']
    ];

    $filter_classes = $form_classes[$sup_form] ?? [];//if not defined given as an empty obj

    if (empty($filter_classes)) {
        die("Invalid or missing form selection.");
    }

    // Calculate the start date based on filter range from today
    $today = new DateTime();  // current date

    switch (strtolower($filter)) {
        case 'day':
            // Only today
            $filter_date = $today->format('Y-m-d');
            break;

        case 'week':
            // Last 7 days including today
            $today->modify('-6 days');
            $filter_date = $today->format('Y-m-d');
            break;

        case 'month':
            // Last 30 days including today
            $today->modify('-29 days');
            $filter_date = $today->format('Y-m-d');
            break;
            case 'term':
                // Last 100 days including today
                $today->modify('-99 days');
                $filter_date = $today->format('Y-m-d');
                break;

        case 'year':
            // Last 365 days including today
            $today->modify('-364 days');
            $filter_date = $today->format('Y-m-d');
            break;

        default:
            // If unknown filter, default to today
            $filter_date = (new DateTime())->format('Y-m-d');
            break;
    }

    $placeholders = implode(',', array_fill(0, count($filter_classes), '?'));

    // SQL which filters based on date
    $sql = "
        SELECT si.Student_name, sa.student_id, sa.class_id, sa.date_recorded, sa.Late, sa.Absent 
        FROM student_attendance sa 
        JOIN Student_info si ON sa.student_id = si.Student_id 
        WHERE si.Student_class IN ($placeholders)
          AND sa.date_recorded >= ?
    ";

    $stmt = $pdo->prepare($sql);

    // Merge class values and the computed date for execution
    $params = array_merge($filter_classes, [$filter_date]);

    $stmt->execute($params);
    $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($attendanceData)) {
        die("No attendance data found.");
    }

    // Output CSV
    $date = date("Y-m-d");
    $filename = "student_attendance [$sup_form - $date] Timeframe: [$filter].csv";

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    //writing to export file using fetched data
    $output = fopen('php://output', 'w');
    fputcsv($output, array_keys($attendanceData[0]));

    foreach ($attendanceData as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>