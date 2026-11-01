<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
header('Content-Type: application/json');

include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
$connection_instance = connect($config);

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data["name"]) && isset($data["password"])) {
    $id = $data['name'];
    $pass = $data['password'];
} else {
    echo json_encode(['status' => 'error', 'message' => 'Missing student ID or password!']);
    exit;
}

// --- Check for Beadle credentials ---
$query_SQL = 'SELECT * FROM beadle_info WHERE beadle_id = :check_id';
$query_stmnt = $connection_instance->prepare($query_SQL);
$query_stmnt->bindParam(':check_id', $id);
$query_stmnt->execute();
$student_info = $query_stmnt->fetch(PDO::FETCH_OBJ);

if ($student_info && password_verify($pass, $student_info->beadle_pass)) {
    $query_SQL3 = 'SELECT class_id FROM class_list WHERE beadle_id = :check_id';
    $query_stmnt3 = $connection_instance->prepare($query_SQL3);
    $query_stmnt3->bindParam(':check_id', $id);
    $query_stmnt3->execute();
    $class_info = $query_stmnt3->fetch(PDO::FETCH_OBJ);
    $classification = ($class_info && isset($class_info->class_id))
        ? (($class_info->class_id >= 100000) ? "upper_class" : "lower_class")
        : "unknown";

    $beadle_name = $student_info->beadle_name;
    $expiry = time() + 3600; // 1 hour

    // Set session variables with expiration
    $_SESSION['stud_name'] = $beadle_name;
    $_SESSION['stud_name_expiry'] = $expiry;

    $_SESSION['beadle_id'] = $id;
    $_SESSION['beadle_id_expiry'] = $expiry;

    $_SESSION['class_filter'] = $classification;
    $_SESSION['class_filter_expiry'] = $expiry;

    echo json_encode(['status' => 'student', 'name' => $beadle_name, 'classification' => $classification]);
    exit;
}

// --- Check for Admin credentials ---
$query_SQL2 = 'SELECT * FROM admin_credentials WHERE Admin_ID = :a_id';
$query_stmnt2 = $connection_instance->prepare($query_SQL2);
$query_stmnt2->bindParam(':a_id', $id);
$query_stmnt2->execute();
$admin_info = $query_stmnt2->fetch(PDO::FETCH_OBJ);

if ($admin_info && password_verify($pass, $admin_info->admin_password)) {
    $admin_name = $admin_info->Name;
    $supervisor_form = $admin_info->Form_class;
    $expiry = time() + 10800; // 3 hours

    // Set session variables with expiration
    $_SESSION['supervisor_name'] = $admin_name;
    $_SESSION['supervisor_name_expiry'] = $expiry;

    $_SESSION['admin_id'] = $id;
    $_SESSION['admin_id_expiry'] = $expiry;

    $_SESSION['form_managed'] = $supervisor_form;
    $_SESSION['form_managed_expiry'] = $expiry;

    if ($id != 000000) {
        echo json_encode(['status' => 'admin', 'name' => $admin_name]);
    } else {
        echo json_encode(['status' => 'master', 'name' => $admin_name]);
    }
    exit;
}

// --- Invalid credentials ---
echo json_encode(['status' => 'error', 'message' => 'Invalid ID or password.']);
$connection_instance = null;
?>