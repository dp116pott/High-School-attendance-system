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
    if (isset($_SESSION['supervisor_name'])):
        // db info
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);

        function fetchRecord($db, $query, $param)
        {
            $stmt = $db->prepare($query);
            $stmt->execute([':val_search' => $param]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        }

        $record = null;
        $type = null;
        if (isset($_POST['search_val'])) {
            $val_search = $_POST['search_val'];

            // Try student first
            $record = fetchRecord($connection_instance, "SELECT * FROM student_info WHERE Student_id = :val_search", $val_search);
            if ($record) {
                $type = 'student';
            } else {
                $record = fetchRecord($connection_instance, "SELECT * FROM teacher_info WHERE teacher_id = :val_search", $val_search);
                if ($record) {
                    $type = 'teacher';
                } else {
                    $record = fetchRecord($connection_instance, "SELECT * FROM subject_info WHERE Subject_name = :val_search", $val_search);
                    if ($record) {
                        $type = 'class';
                    }
                }
            }
        }
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Admin Report Search</title>
            <link href="https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;700&display=swap" rel="stylesheet">
            <!-- Alerts design reference -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body {
                    margin: 0;
                    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                    background: #f9fbfc;
                    color: #333;
                    display: flex;
                    min-height: 100vh;
                }

                .sidebar {
                    width: 240px;
                    background-color: #8a1619;
                    color: #fff;
                    padding: 30px 20px;
                    display: flex;
                    flex-direction: column;
                    position: fixed;
                    top: 0;
                    bottom: 0;
                    box-shadow: 3px 0 6px rgb(0 0 0 / 0.1);
                }

                .sidebar h2 {
                    font-weight: 700;
                    font-size: 1.8rem;
                    margin-bottom: 40px;
                    letter-spacing: 1px;
                }

                .sidebar h1 {
                    font-weight: 700;
                    font-size: 1.8rem;
                    margin-bottom: 40px;
                    letter-spacing: 1px;
                }

                .sidebar ul {
                    list-style: none;
                    padding-left: 0;
                    margin: 0;
                    flex-grow: 1;
                }

                .sidebar ul li {
                    margin-bottom: 18px;
                }

                .sidebar ul li a {
                    color: #cce0ff;
                    text-decoration: none;
                    font-weight: 600;
                    padding: 10px 15px;
                    display: block;
                    border-radius: 6px;
                    transition: background-color 0.3s ease, color 0.3s ease;
                }

                .sidebar ul li a:hover,
                .sidebar ul li a.active {
                    background-color: #0055aa;
                    color: #fff;
                }

                .main-content {
                    margin-left: 240px;
                    padding: 40px 50px;
                    flex-grow: 1;
                    background: #f9fbfc;
                    min-height: 100vh;
                }

                .container {
                    max-width: 700px;
                    background-color: white;
                    padding: 30px 40px;
                    border-radius: 8px;
                    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
                    margin: auto;
                }

                h2 {
                    color: rgb(255, 255, 255);
                    margin-bottom: 30px;
                }

                h1 {
                    color: rgb(0, 0, 0);
                    text-align: center;
                    margin-bottom: 30px;
                }

                form {
                    display: flex;
                    flex-direction: column;
                }

                input[type="text"],
                input[type="date"] {
                    padding: 10px;
                    margin-bottom: 20px;
                    font-size: 16px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                }

                label {
                    font-weight: 500;
                    margin-bottom: 5px;
                }

                button {
                    background-color: #cc9a00;
                    color: white;
                    padding: 12px;
                    border: none;
                    font-size: 16px;
                    border-radius: 6px;
                    cursor: pointer;
                    transition: background-color 0.3s ease;
                }

                button:hover {
                    background-color: #b88800;
                }

                .record {
                    margin-top: 25px;
                    padding: 15px 20px;
                    background-color: #f9fafc;
                    border-left: 5px solid #002c5f;
                }

                .record p {
                    margin: 5px 0;
                }

                .error {
                    color: red;
                    text-align: center;
                }

                .group-filter {
                    display: flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 20px;
                    font-family: 'Segoe UI', sans-serif;
                }

                .group-filter label {
                    font-size: 1rem;
                    font-weight: 600;
                    color: #333;
                }

                .group-filter select {
                    padding: 8px 12px;
                    font-size: 0.95rem;
                    border: 1px solid #ccc;
                    border-radius: 8px;
                    background-color: #fff;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
                    transition: border-color 0.2s, box-shadow 0.2s;
                }

                .group-filter select:focus {
                    border-color: #007bff;
                    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
                    outline: none;
                }
            </style>
        </head>

        <body>
            <div class="sidebar">
                <h2>Dashboard</h2>
                <ul>
                    <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_dashboard.php">Dashboard</a></li>
                    <li><a
                            href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_attendance_management.php">Attendance</a>
                    </li>
                    <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_analytics.php"
                            class="active">Analytics</a></li>
                    <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_settings.php">Settings</a></li>
                </ul>
            </div>

            <div class="main-content">
                <div class="container">
                    <h1>Admin Report Search</h1>
                    <form method="POST">
                        <input type="text" name="search_val" placeholder="Enter Class name, Student or Teacher ID" required>
                        <button type="submit">Search</button>
                    </form>

                    <?php if ($record): ?>
                        <div class="record">
                            <h3>Record Found (<?= htmlspecialchars($type) ?>)</h3>
                            <p><strong>ID:</strong>
                                <?= htmlspecialchars($record[
                                    $type === 'student' ? 'Student_id' :
                                    ($type === 'teacher' ? 'teacher_id' : 'class_id')
                                ]) ?>
                            </p>
                            <p><strong>Name:</strong>
                                <?= htmlspecialchars($record[
                                    $type === 'student' ? 'Student_name' :
                                    ($type === 'teacher' ? 'teacher_name' : 'Subject_name')
                                ]) ?>
                            </p>
                            <?php if ($type === 'student'): ?>
                                <p><strong>Form Class:</strong> <?= htmlspecialchars($record['Student_class']) ?></p>
                            <?php endif; ?>
                        </div>

                        <form method="GET" action="admin_reportgen.php" style="margin-top: 20px;">
                            <input type="hidden" name="admin_repgen" value="1">
                            <input type="hidden" name="type" value="<?= $type ?>">
                            <input type="hidden" name="id" value="<?= $record[
                                $type === 'student' ? 'Student_id' :
                                ($type === 'teacher' ? 'teacher_id' : 'class_id')
                            ] ?>">
                            <label>Select Start Date:</label>
                            <input type="date" name="start_date" required>

                            <label>Select End Date:</label>
                            <input type="date" name="end_date" required>
                            <?php
                            if (strtolower($type) === 'teacher'):
                                ?>
                                <div class="group-filter">
                                    <label for="group_by">Group Report By:</label>
                                    <select name="group_by" id="group_by" required>
                                        <option value="subject">Subject</option>
                                        <option value="date">Date</option>
                                    </select>
                                </div>
                                <?php
                            endif;
                            ?>
                            <button type="submit">Generate Report</button>
                        </form>

                    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                        <p class="error">No record found for ID <?= htmlspecialchars($_POST['search_id']) ?>.</p>
                    <?php endif; ?>
                </div>
            </div>
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