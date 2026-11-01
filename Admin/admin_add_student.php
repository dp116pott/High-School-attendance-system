<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
header('Content-Type: application/json');
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';

try {
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Connection failed: ' . $e->getMessage()]);
    exit;
}

$student_name = $_POST['student_name'] ?? null;
$student_id = $_POST['student_id'] ?? null;
$class_pd = $_POST['class_pd'] ?? null;
$form_class = $_POST['form_class'] ?? null;

if (!$student_name || !$student_id || !$form_class) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

try {
    // Step 1: Insert into student_info
    $stmt1 = $pdo->prepare("INSERT INTO student_info (Student_id, Student_name, Student_class) VALUES (?, ?, ?)");
    $stmt1->execute([$student_id, $student_name, $form_class]);

    // Step 2: Find matching class_id in Subject_info table
    $stmt2 = $pdo->prepare("SELECT class_id FROM Subject_info WHERE Subject_name = ?");
    $stmt2->execute([$class_pd]);
    $class_id_result = $stmt2->fetch(PDO::FETCH_ASSOC);

    if ($class_id_result) {
        $class_id = $class_id_result['class_id'];

        // Step 3: Insert into Student_subject_assignments
        $stmt3 = $pdo->prepare("INSERT INTO Student_subject_assignments (Student_id, Form_class, class_id) VALUES (?, ?, ?)");
        $stmt3->execute([$student_id, $form_class, $class_id]);

        echo json_encode(['status' => 'success', 'message' => 'Student added successfully.']);
        exit;
    } else {
        echo json_encode(['status' => 'error', 'message' => "PD Class '$class_pd' not found in Subject_info table."]);
        exit;
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    exit;
}
