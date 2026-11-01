<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
// DB details
include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';

try {
    // New db connection
    $pdo = new PDO("mysql:host=$dbServername;dbname=$dbName", $dbUsername, $dbPassword);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST["submit"])) {
        $table = $_POST["table_name"] ?? '';

        if (empty($table)) {
            die("No table selected.");
        }

        // Whitelist allowed tables
        $allowed_tables = ['student_info', 'teacher_info', 'class_list', 'Subject_info', 'Student_subejct_assignments', 'beadle_info'];
        if (!in_array($table, $allowed_tables)) {
            die("Invalid table name.");
        }

        if (isset($_FILES["csv_file"]) && $_FILES["csv_file"]["error"] == 0) {
            $filename = $_FILES["csv_file"]["tmp_name"];

            if (($handle = fopen($filename, "r")) !== false) {
                // Delete existing data from the table
                $pdo->exec("DELETE FROM `$table`");

                $header = fgetcsv($handle); // Get column headers
                $placeholders = rtrim(str_repeat("?,", count($header)), ",");
                $sql = "INSERT INTO `$table` (" . implode(",", $header) . ") VALUES ($placeholders)";
                $stmt = $pdo->prepare($sql);

                // Identify index of password column (for beadle_info only)
                $password_index = -1;
                if ($table === 'beadle_info') {
                    foreach ($header as $index => $column_name) {
                        if (strtolower($column_name) === 'beadle_pass') {
                            $password_index = $index;
                            break;
                        }
                    }
                }

                $rowCount = 0;
                while (($data = fgetcsv($handle)) !== false) {
                    if (count(array_filter($data)) === 0) continue;

                    // Hash password if uploading into beadle_info table
                    if ($password_index !== -1 && isset($data[$password_index])) {
                        $data[$password_index] = password_hash($data[$password_index], PASSWORD_DEFAULT);
                    }

                    $stmt->execute($data);
                    $rowCount++;
                }

                fclose($handle);

                // Success popup
                echo "<div id='popup' style='
                    position: fixed;
                    top: 20px;
                    left: 50%;
                    transform: translateX(-50%);
                    background-color: #4CAF50;
                    color: white;
                    padding: 15px 20px;
                    border-radius: 8px;
                    z-index: 9999;
                    box-shadow: 0 0 10px rgba(0,0,0,0.2);
                    font-family: Roboto, sans-serif;
                    font-size: 16px;'>Successfully imported $rowCount records into <strong>$table</strong>.
                </div>
                <script>
                    setTimeout(() => document.getElementById('popup')?.remove(), 10000);
                    setTimeout(() => window.location.href = 'Webmaster_Dashboard.php', 10000);
                </script>";
            } else {
                echo "Failed to open the uploaded CSV file.";
            }
        } else {
            echo "No file uploaded or file upload error.";
        }
    }
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
}
?>
