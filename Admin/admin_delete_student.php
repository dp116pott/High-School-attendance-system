<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
header('Content-Type: application/json');

if (!isset($_POST['student_id'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing student ID.']);
    exit;
}

$student_id = $_POST['student_id'];

include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';

try {
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->beginTransaction();

    $stmt = $pdo->prepare("DELETE FROM Student_info WHERE student_id = ?");
    $stmt->execute([$student_id]);

    $pdo->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Student was successfully deleted.'
    ]);
    exit;

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Error deleting student: ' . $e->getMessage()
    ]);
}
