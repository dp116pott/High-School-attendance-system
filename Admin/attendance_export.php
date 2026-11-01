<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
//db details
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';

try {//new db connection
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query data from student_attendance
    $stmt = $pdo->query("SELECT * FROM student_attendance");
    $attendanceData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($attendanceData)) {
        die("No attendance data found.");
    }

    $date = date("Y-m-d");
    $filename = "student_attendance [$date].csv";

    // Set headers for CSV download
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename=\"$filename\"");

    $output = fopen('php://output', 'w');

    // Output column headers
    fputcsv($output, array_keys($attendanceData[0]));

    // Output each row
    foreach ($attendanceData as $row) {
        fputcsv($output, $row);
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
