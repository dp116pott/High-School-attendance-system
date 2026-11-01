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
        $groupBy = $_GET['group_by'] ?? 'date'; // default fallback
        // Database connection
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);

        $type = $_GET['type'] ?? null;
        $id = $_GET['id'] ?? null;
        $start = $_GET['start_date'] ?? null;
        $end = $_GET['end_date'] ?? null;

        if (!$type || !$id || !$start || !$end) {
            die("Missing parameters.");
        }

        function fetchTeacherReport($db, $id, $start, $end)
        {
            $stmt = $db->prepare("SELECT t.date_recorded, t.Present, t.T_late, t.Absent, t.time_arrived, t.start_period, s.Subject_name
                          FROM teacher_attendance t
                          JOIN class_list c ON t.class_id = c.class_id
                          JOIN subject_info s ON t.class_id = s.class_id
                          WHERE t.teacher_id = :id AND t.date_recorded BETWEEN :start AND :end
                          ORDER BY s.Subject_name, t.date_recorded");
            $stmt->execute([':id' => $id, ':start' => $start, ':end' => $end]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        function fetchStudentReport($db, $id, $start, $end)
        {
            $stmt = $db->prepare("SELECT s.date_recorded, s.Late, s.Absent, s.start_period, c.form_class, si.Subject_name
                          FROM student_attendance s
                          JOIN class_list c ON s.class_id = c.class_id
                          JOIN subject_info si ON s.class_id = si.class_id
                          WHERE s.student_id = :id AND s.date_recorded BETWEEN :start AND :end");
            $stmt->execute([':id' => $id, ':start' => $start, ':end' => $end]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        function fetchClassReport($db, $id, $start, $end)
        {
            $stmt = $db->prepare("SELECT s.date_recorded, s.Late, s.Absent, s.start_period, c.form_class, si.Subject_name, so.Student_name
                          FROM student_attendance s
                          JOIN class_list c ON s.class_id = c.class_id
                          JOIN subject_info si ON s.class_id = si.class_id
                          JOIN student_info so ON s.student_id = so.Student_id
                          WHERE si.class_id = :id AND s.date_recorded BETWEEN :start AND :end");
            $stmt->execute([':id' => $id, ':start' => $start, ':end' => $end]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        function calculatePeriod($time)
        {
            if (!$time || $time === 'N/A')
                return 'N/A';
            $startTime = strtotime('08:00');
            $arrived = strtotime($time);
            $diff = ($arrived - $startTime) / 60;
            if ($diff < 0)
                return 'Before school';
            $period = floor($diff / 35) + 1;
            return $period > 12 ? 'After school' : "Period $period";
        }

        $records = $type === 'teacher'
            ? fetchTeacherReport($connection_instance, $id, $start, $end)
            : ($type === 'student'
                ? fetchStudentReport($connection_instance, $id, $start, $end)
                : fetchClassReport($connection_instance, $id, $start, $end));
        if (empty($records)) {
            echo <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <title>No Records Found</title>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>
    <body>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'No Records Found',
                text: 'No attendance data is available for the selected criteria.',
                confirmButtonText: 'Back'
            }).then(() => {
                window.location.replace('admin_analytics.php');
            });
        </script>
    </body>
    </html>
    HTML;
            exit;
        }

        usort($records, function ($a, $b) {
            return strtotime($a['date_recorded']) <=> strtotime($b['date_recorded']);
        });
        ?>
        <!DOCTYPE html>
        <html>

        <head>
            <title>Admin Report</title>
            <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600&display=swap" rel="stylesheet">
            <!-- Alerts design reference -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body {
                    font-family: 'Montserrat', sans-serif;
                    background: #f4f4f4;
                    padding: 20px;
                }

                h2 {
                    color: #003057;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-top: 20px;
                }

                th,
                td {
                    border: 1px solid #ccc;
                    padding: 10px;
                    text-align: left;
                }

                th {
                    background-color: #003057;
                    color: white;
                }

                tr:nth-child(even) {
                    background-color: #e9f1f7;
                }

                .details {
                    display: none;
                    background-color: #eef6fc;
                }

                .clickable {
                    cursor: pointer;
                    background-color: #d8e9f5;
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
            <script>
                function toggleDetails(id) {
                    const el = document.getElementById(id);
                    el.style.display = el.style.display === 'none' ? 'table-row-group' : 'none';
                }
            </script>
        </head>

        <body>
            <h2><?= ucfirst($type) ?> Report</h2>
            <p><strong>ID:</strong> <?= htmlspecialchars($id) ?></p>
            <p><strong>Date Range:</strong> <?= htmlspecialchars($start) ?> to <?= htmlspecialchars($end) ?></p>

            <?php if ($type === 'teacher'): ?>
                <?php
                $name_query = 'SELECT teacher_name from teacher_info WHERE teacher_id = :id';
                $name_stmt = $connection_instance->prepare($name_query);
                $name_stmt->bindParam(":id", $id);
                $name_stmt->execute();
                $tname = $name_stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <p><strong>Name:</strong> <?= htmlspecialchars($tname['teacher_name']) ?></p>
                <?php
                $grouped = [];

                foreach ($records as $row) {
                    if ($row['Absent'] == 1) {
                        $row['time_arrived'] = 'N/A';
                    }
                    $row['period'] = calculatePeriod($row['start_period']);

                    if ($groupBy === 'date') {
                        $key = $row['date_recorded'];
                    } else {
                        $key = $row['Subject_name'];
                    }

                    $grouped[$key][] = $row;
                }

                ?>
                <form method="get" id="groupForm">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($start) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($end) ?>">
                    <div class="group-filter">
                        <label for="group_by">Group Report By:</label>
                        <select name="group_by" id="group_by" required onchange="document.getElementById('groupForm').submit()">
                            <option value="subject" <?= $groupBy === 'subject' ? 'selected' : '' ?>>Subject</option>
                            <option value="date" <?= $groupBy === 'date' ? 'selected' : '' ?>>Date</option>
                        </select>
                    </div>
                </form>
                <table>
                    <thead>
                        <tr>
                            <th><?= $groupBy === 'date' ? 'Date' : 'Subject Name' ?></th>
                            <th>Total Records</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0;
                        foreach ($grouped as $subject => $rows): ?>
                            <tr class="clickable" onclick="toggleDetails('detail<?= $i ?>')">
                                <td>
                                    <?php
                                    if ($groupBy === 'date') {
                                        $day = date("F j,Y ", strtotime($subject));
                                        echo $day;
                                    } else {
                                        echo htmlspecialchars($subject);
                                    }
                                    ?>
                                </td>

                                <td><?= count($rows) ?></td>
                            </tr>
                        <tbody class="details" id="detail<?= $i ?>">
                            <tr>
                                <th>Late</th>
                                <th>Absent</th>
                                <th>Time Arrived</th>
                                <th>Start Period</th>
                                <th>Period</th>
                            </tr>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars($r['T_late']) ?></td>
                                    <td><?= htmlspecialchars($r['Absent']) ?></td>
                                    <td><?= htmlspecialchars($r['time_arrived']) ?></td>
                                    <td><?= htmlspecialchars($r['start_period']) ?></td>
                                    <td><?= htmlspecialchars($r['period']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            <?php elseif ($type === 'student'): ?>
                <?php
                $name_query = 'SELECT Student_name from student_info WHERE Student_id = :id';
                $name_stmt = $connection_instance->prepare($name_query);
                $name_stmt->bindParam(":id", $id);
                $name_stmt->execute();
                $sname = $name_stmt->fetch(PDO::FETCH_ASSOC);
                ?>
                <p><strong>Name:</strong> <?= htmlspecialchars($sname['Student_name']) ?></p>
                <form method="get">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($start) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($end) ?>">
                </form>

                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Day</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $groupedByDate = [];
                        foreach ($records as $row) {
                            $groupedByDate[$row['date_recorded']][] = $row;

                        }
                        $i = 0;
                        foreach ($groupedByDate as $date => $rows):
                            $dayOfWeek = date('l', strtotime($date));
                            ?>
                            <tr class="clickable" onclick="toggleDetails('studentDetail<?= $i ?>')">
                                <td><strong><?= htmlspecialchars(date("F j,Y ", strtotime($date))) ?></strong> (<?= count($rows) ?>
                                    record<?= count($rows) > 1 ? 's' : '' ?>)</td>
                                <td><?= htmlspecialchars($dayOfWeek) ?></td>
                            </tr>
                        <tbody class="details" id="studentDetail<?= $i ?>">
                            <tr>
                                <th>Late</th>
                                <th>Absent</th>
                                <th>Start Time</th>
                                <th>Start Period</th>
                                <th>Form Class</th>
                                <th>Subject</th>
                            </tr>
                            <?php foreach ($rows as $r): ?>
                                <?php $r['period'] = calculatePeriod($r['start_period']); ?>
                                <tr>
                                    <td><?= $r['Late'] == 1 ? 'Yes' : 'No' ?></td>
                                    <td><?= $r['Absent'] == 1 ? 'Yes' : 'No' ?></td>
                                    <td><?= htmlspecialchars($r['start_period']) ?></td>
                                    <td><?= htmlspecialchars($r['period']) ?></td>
                                    <td><?= htmlspecialchars($r['form_class']) ?></td>
                                    <td><?= htmlspecialchars($r['Subject_name']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>

                        <?php $i++; endforeach; ?>

                    </tbody>
                </table>
            <?php else: // assume class report ?>
                <form method="get">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <input type="hidden" name="start_date" value="<?= htmlspecialchars($start) ?>">
                    <input type="hidden" name="end_date" value="<?= htmlspecialchars($end) ?>">
                </form>

                <?php
                // Group by calculated period
                $groupedByPeriod = [];
                foreach ($records as $row) {
                    $row['period'] = calculatePeriod($row['start_period']);
                    $groupedByPeriod[$row['period']][] = $row;
                }
                ?>

                <table>
                    <thead>
                        <tr>
                            <th>Period</th>
                            <th>Absent Count</th>
                            <th>Late Count</th>
                            <th>Total Records</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 0;
                        foreach ($groupedByPeriod as $period => $rows): ?>
                            <?php
                            $absentCount = count(array_filter($rows, fn($r) => $r['Absent'] == 1));
                            $lateCount = count(array_filter($rows, fn($r) => $r['Late'] == 1));
                            $firstStartTime = $rows[0]['start_period'];
                            ?>
                            <tr class="clickable" onclick="toggleDetails('classDetail<?= $i ?>')">
                                <td><?= htmlspecialchars($period) ?> (<?= htmlspecialchars($firstStartTime) ?>)</td>
                                <td><?= $absentCount ?></td>
                                <td><?= $lateCount ?></td>
                                <td><?= count($rows) ?></td>
                            </tr>
                        <tbody class="details" id="classDetail<?= $i ?>">
                            <tr>
                                <th>Date</th>
                                <th>Form Class</th>
                                <th>Student Name</th>
                                <th>Late</th>
                                <th>Absent</th>
                            </tr>
                            <?php foreach ($rows as $r): ?>
                                <tr>
                                    <td><?= htmlspecialchars(date("F j,Y ", strtotime($r['date_recorded']))) ?></td>
                                    <td><?= htmlspecialchars($r['form_class']) ?></td>
                                    <td><?= htmlspecialchars($r['Student_name']) ?></td>
                                    <td><?= $r['Late'] == 1 ? 'Yes' : 'No' ?></td>
                                    <td><?= $r['Absent'] == 1 ? 'Yes' : 'No' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <?php $i++; endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <p style="margin-top: 30px;"><a href="admin_dashboard.php">← Back to Dashboard</a></p>
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