<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
//db information
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';

try {
    //establish new db connection
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get form and filter range (day, week, month, year)
    $sup_form = $_SESSION['form_managed'] ?? '';
    $filter = $_GET['filter'] ?? 'day';  // default to 'day' if not set

    // Define class groupings
    $form_classes = [
        '1stForm' => ['1-1', '1-2', '1-3', '1-4', '1-5', '1-6', '1-8'],
        '2ndForm' => ['2-1', '2-2', '2-3', '2-4', '2-5', '2-6', '2-8'],
        '3rdForm' => ['3-1', '3-2', '3-3', '3-4', '3-5', '3-6', '3-8'],
        '4thForm' => ['4-1', '4-2', '4-3', '4-4', '4-5', '4-6', '4-7'],
        '5thForm' => ['5-1', '5-2', '5-3', '5-4', '5-5', '5-6', '5-7'],
        '6A'      => ['6A'],
        '6B'      => ['6B']
    ];

    $filter_classes = $form_classes[$sup_form] ?? [];

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
                //last 100 days including today
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

    // Create placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($filter_classes), '?'));

    //SQL statement that searches for the relevant teachers who meet the date criteria
    $sql = "
        SELECT ti.teacher_name, ta.teacher_id, ta.class_id ,ta.date_recorded, ta.Absent, ta.time_arrived 
        FROM teacher_attendance ta 
        JOIN teacher_info ti ON ta.teacher_id = ti.teacher_id 
        JOIN class_list cl ON ta.teacher_id = cl.teacher_id
        WHERE cl.form_class IN ($placeholders)
          AND ta.date_recorded >= ?
    ";

    $stmt = $pdo->prepare($sql);

    // Merge class values and the computed date for execution
    $params = array_merge($filter_classes, [$filter_date]);

    $stmt->execute($params);
    $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($attendanceData)) {
        die("No attendance data found.");
    }

    // Output as a CSV file
    $date = date("Y-m-d");
    $filename = "teacher_attendance [$sup_form - $date] Timeframe: [$filter].csv";

    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");
    //writes to the file using the information stored queried
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