<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
//database connection
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
$connection_instance = connect($config);

$errorMessage = null;

if (!isset($_GET['student_id']) || !is_numeric($_GET['student_id'])) {
    $errorMessage = 'Invalid student ID.';
} else {
    $student_id = (int)$_GET['student_id'];

    $stmt = $connection_instance->prepare("SELECT Student_name, Student_class FROM student_info WHERE student_id = ?");
    $stmt->execute([$student_id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        $errorMessage = 'Student not found.';
    } else {
        // Fetch current classes
        $classStmt = $connection_instance->prepare("
            SELECT lc.class_id, lc.Subject_name
            FROM Student_subject_assignments usa
            JOIN Subject_info lc ON usa.class_id = lc.class_id
            WHERE usa.student_id = ?
        ");
        $classStmt->execute([$student_id]);
        $currentClasses = $classStmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch all classes
        $allClassesStmt = $connection_instance->prepare("SELECT class_id, Subject_name FROM Subject_info WHERE Form_class = ?");
        $allClassesStmt->execute([$student['Student_class']]);
        $allClasses = $allClassesStmt->fetchAll(PDO::FETCH_ASSOC);

        // Handle POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_class_id'])) {
                $add_class_id = (int)$_POST['add_class_id'];

                $verifyStmt = $connection_instance->prepare("SELECT COUNT(*) FROM Subject_info WHERE class_id = ? AND Form_class = ?");
                $verifyStmt->execute([$add_class_id, $student['Student_class']]);

                if ($verifyStmt->fetchColumn() == 0) {
                    $errorMessage = "Error: Class does not belong to the student's form.";
                } else {
                    $checkStmt = $connection_instance->prepare("SELECT COUNT(*) FROM Student_subject_assignments WHERE student_id = ? AND Form_class=? AND class_id = ?");
                    $checkStmt->execute([$student_id, $student['Student_class'], $add_class_id]);

                    if ($checkStmt->fetchColumn() == 0) {
                        $insertStmt = $connection_instance->prepare("INSERT INTO Student_subject_assignments (student_id, Form_class, class_id) VALUES (?, ?, ?)");
                        $insertStmt->execute([$student_id, $student['Student_class'], $add_class_id]);
                    }
                }
            }

            if (isset($_POST['remove_class_id'])) {
                $remove_class_id = (int)$_POST['remove_class_id'];
                $deleteStmt = $connection_instance->prepare("DELETE FROM Student_subject_assignments WHERE student_id = ? AND Form_class = ? AND class_id = ?");
                $deleteStmt->execute([$student_id, $student['Student_class'], $remove_class_id]);
            }

            header("Location: " . $_SERVER['REQUEST_URI']);
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Classes</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
    <link href="http://localhost/campion_Beadle_Slip/CSS_stylesheets/Classmanagement_styles.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php if (!empty($errorMessage)): ?>
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: <?= json_encode($errorMessage) ?>,
        }).then(() => {
            window.location.href = 'admin_dashboard.php';
        });
    </script>
<?php else: ?>
    <div class="container">
        <h1>Manage Classes for <?= htmlspecialchars($student['Student_name']) ?> (<?= htmlspecialchars($student['Student_class']) ?>)</h1>
        <h2>Current Classes</h2>
        <table>
            <thead>
                <tr>
                    <th>Subject Name</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($currentClasses as $class): ?>
                <tr>
                    <td><?= htmlspecialchars($class['Subject_name']) ?></td>
                    <td>
                        <form method="POST" class="remove-class-form">
                            <input type="hidden" name="remove_class_id" value="<?= $class['class_id'] ?>">
                            <button type="button" class="remove-button">Remove</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Add Class</h2>
        <form method="POST">
            <select name="add_class_id" required>
                <option value="">Select Class</option>
                <?php
                $currentClassIds = array_column($currentClasses, 'class_id');
                foreach ($allClasses as $class) {
                    if (!in_array($class['class_id'], $currentClassIds)) {
                        echo '<option value="'.htmlspecialchars($class['class_id']).'">'.htmlspecialchars($class['Subject_name']).'</option>';
                    }
                }
                ?>
            </select>
            <button type="submit">Add Class</button>
        </form>

        <p style="margin-top: 30px;"><a href="admin_dashboard.php">← Back to Dashboard</a></p>
    </div>

    <script>
        document.querySelectorAll('.remove-button').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to remove this class?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, remove it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
<?php endif; ?>
</body>
</html>
