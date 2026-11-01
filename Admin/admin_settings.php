<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
if (isset($_SESSION['admin_id'], $_SESSION['admin_id_expiry'])):
    if (time() > $_SESSION['admin_id_expiry']):
        session_unset();
        session_destroy();
        echo "<script>location.reload();</script>";
        exit();
    endif;
    if (isset($_SESSION['supervisor_name'])):
        // IDB information
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);
        $grade_filter = null;
        // Capture selected PD class from form
        $selected_pd_class = $_GET['selected_pd_class'] ?? null;
        $sup_form = $_SESSION['form_managed'];
        $grade_filter = []; // Default fallback in the case that the session is not set

        switch ($sup_form) {
            case "1st Form":
                $grade_filter = ['1-1', '1-2', '1-3', '1-4', '1-5', '1-6', '1-8'];
                break;
            case "2nd Form":
                $grade_filter = ['2-1', '2-2', '2-3', '2-4', '2-5', '2-6', '2-8'];
                break;
            case "3rd Form":
                $grade_filter = ['3-1', '3-2', '3-3', '3-4', '3-5', '3-6', '3-8'];
                break;
            case "4th Form":
                $grade_filter = ['4-1', '4-2', '4-3', '4-4', '4-5', '4-6', '4-7'];
                break;
            case "5th Form":
                $grade_filter = ['5-1', '5-2', '5-3', '5-4', '5-5', '5-6', '5-7'];
                break;
            case "6B":
                $grade_filter = ['6B'];
                break;
            case "6A":
                $grade_filter = ['6A'];
                break;
        }
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Admin Settings</title>
            <!-- References the CSS used to build this page's display -->
            <link href="http://localhost/Campion_Beadle_Slip/CSS_stylesheets/Adminsettings_styles.css" rel="stylesheet">
            <!-- Alerts design reference -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>

        <body>

            <div class="sidebar">
                <h2>Dashboard</h2>
                <ul>
                    <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_dashboard.php">Home</a></li>
                    <li><a
                            href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_attendance_management.php">Attendance</a>
                    </li>
                    <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_analytics.php">Analytics</a></li>
                    <li><a href="http://localhost/campion_Beadle_Slip/Admin_Folder/admin_settings.php"
                            class="active">Settings</a></li>
                </ul>
            </div>

            <div class="container">
                <h1>Admin Settings</h1>

                <!-- Manage Students -->
                <div class="settings-section">
                    <h2>Manage Students</h2>

                    <!-- Add Student -->
                    <form id="add-student-form">
                        <div class="form-group">
                            <label>Student Name:</label>
                            <input type="text" name="student_name" required>
                        </div>
                        <div class="form-group">
                            <label>Student Id:</label>
                            <input type="text" name="student_id" required>
                        </div>
                        <div class="form-group">
                            <label>Form Class:</label>
                            <select name="form_class" required>
                                <option value="">-- Select Form Class --</option>
                                <?php
                                //Allows for form class selection
                                switch ($sup_form) {
                                    case "1st Form":
                                        for ($i = 1; $i <= 8; $i++) {
                                            if ($i != 7) {
                                                $val = "1-$i";
                                                echo "<option value=\"$val\">$val</option>";
                                            }
                                        }
                                        break;
                                    case "2nd Form":
                                        for ($i = 1; $i <= 8; $i++) {
                                            if ($i != 7) {
                                                $val = "2-$i";
                                                echo "<option value=\"$val\">$val</option>";
                                            }
                                        }
                                        break;
                                    case "3rd Form":
                                        for ($i = 1; $i <= 8; $i++) {
                                            if ($i != 7) {
                                                $val = "3-$i";
                                                echo "<option value=\"$val\">$val</option>";
                                            }
                                        }
                                        break;
                                    case "4th Form":
                                        for ($i = 1; $i <= 8; $i++) {
                                            if ($i != 8) {
                                                $val = "4-$i";
                                                echo "<option value=\"$val\">$val</option>";
                                            }
                                        }

                                        break;
                                    case "5th Form":
                                        for ($i = 1; $i <= 8; $i++) {
                                            if ($i != 8) {
                                                $val = "5-$i";
                                                echo "<option value=\"$val\">$val</option>";
                                            }
                                        }
                                        break;
                                    case "6B":
                                        $val = "6B";
                                        echo "<option value=\"$val\">$val</option>";
                                        break;
                                    case "6A":
                                        $val = "6A";
                                        echo "<option value=\"$val\">$val</option>";
                                        break;
                                    default:
                                        echo "<option value=\"\">No form classes available</option>";
                                }

                                ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>PD Class:</label>
                            <select name="class_pd" required>
                                <option value="">-- Select PD Class --</option>
                                <option value="PD1">PD1</option>
                                <option value="PD2">PD2</option>
                                <option value="PD3">PD3</option>
                                <option value="PD4">PD4</option>
                                <option value="PD5">PD5</option>
                                <option value="PD6">PD6</option>
                                <option value="PD7">PD7/PD8</option>
                            </select>
                        </div>
                        <button type="submit">Add Student</button>
                    </form>
                    <br><br>
                    <!-- Select PD Class -->
                    <form method="GET" action="admin_settings.php">
                        <div class="form-group">
                            <label>Form Class:</label>
                            <select name="selected_pd_class" onchange="this.form.submit()">
                                <option value="">-- Select Form Class --</option>
                                <?php
                                foreach ($grade_filter as $g_fil):
                                    ?>
                                    <option value='<?= $g_fil ?>' <?= ($selected_pd_class == $g_fil) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g_fil) ?>
                                    </option>
                                    <?php
                                endforeach;
                                ?>
                            </select>

                        </div>
                    </form>
                    <!-- Existing Students Table -->
                    <table>
                        <thead>
                            <tr>
                                <th>Student Name</th>
                                <th>Student Class</th>
                                <th>Form Class</th>

                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $params = [];
                            //THIS CODE BLOCK IS WHAT ALLOWS FOR THE QUERY BELOW TO BE FILTERED BASED ON THE GRADE LEVEL THAT THE SUPERVISOR IS AUTHORIZED TO VIEW(VERY IMPORTANT)
                            if ($selected_pd_class) {
                                if ($selected_pd_class) {
                                    $grade_sql = "si.Student_class = :selected_class";
                                    $params['selected_class'] = $selected_pd_class;
                                } elseif (is_array($grade_filter)) {
                                    $placeholders = [];
                                    foreach ($grade_filter as $index => $grade) {
                                        $key = ":grade$index";
                                        $placeholders[] = $key;
                                        $params["grade$index"] = $grade;
                                    }
                                    $grade_sql = "si.Student_class IN (" . implode(",", $placeholders) . ")";
                                } elseif ($grade_filter !== null) {
                                    $grade_sql = "si.Student_class = :grade";
                                    $params['grade'] = $grade_filter;
                                } else {
                                    $grade_sql = "1 = 0";
                                }

                                // Query to fetch students based on selected class
                                $query = "SELECT DISTINCT si.Student_id, si.Student_name, si.Student_class FROM Student_subject_assignments us JOIN student_info si ON us.Student_id = si.Student_id WHERE $grade_sql";
                                $stmt = $connection_instance->prepare($query);
                                $stmt->execute($params);

                                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['Student_name']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['Student_class']) . "</td>";
                                    echo "<td>Homeroom</td>";
                                    echo "<td>
<form method='POST' class='delete-form'>
    <input type='hidden' name='student_id' value='" . htmlspecialchars($row['Student_id']) . "'>
    <button type='button' class='delete-btn'>Remove</button>
</form>
        </td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='4'>Please select a homeroom to view students.</td></tr>";
                            }

                            ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        document.getElementById('add-student-form').addEventListener('submit', function (e) {
                            e.preventDefault();

                            const form = e.target;
                            const formData = new FormData(form);

                            fetch('admin_add_student.php', {
                                method: 'POST',
                                body: formData
                            })
                                .then(res => res.json())
                                .then(data => {
                                    if (data.status === 'success') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Student Added',
                                            text: data.message
                                        }).then(() => {
                                            form.reset(); // Resets form
                                            window.location.reload();
                                        });
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: data.message
                                        });
                                    }
                                })
                                .catch(err => {
                                    console.error(err);
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Submission Failed',
                                        text: 'Could not contact the server. Please try again.'
                                    });
                                });
                        });
                        // Get all cards and add click event listener
                        document.querySelectorAll(".card").forEach(card => {
                            card.addEventListener("click", function () {
                                const link = this.getAttribute("data-link");
                                if (link) {
                                    window.location.href = link; // Redirect on click
                                }
                            });
                        });
                    });

                </script>

                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.querySelectorAll('.delete-btn').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                const form = btn.closest('.delete-form');
                                const formData = new FormData(form);
                                const studentId = formData.get('student_id');

                                Swal.fire({
                                    title: 'Are you sure?',
                                    text: 'Do you really want to remove this student?',
                                    icon: 'warning',
                                    showCancelButton: true,
                                    confirmButtonText: 'Yes, delete',
                                    cancelButtonText: 'Cancel'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        // Send AJAX request
                                        fetch('admin_delete_student.php', {
                                            method: 'POST',
                                            body: formData
                                        })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (data.status === 'success') {
                                                    Swal.fire({
                                                        icon: 'success',
                                                        title: 'Student Deleted',
                                                        text: data.message
                                                    }).then(() => {
                                                        // Optional: reload or remove row
                                                        location.reload();
                                                    });
                                                } else {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: data.message
                                                    });
                                                }
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                Swal.fire({
                                                    icon: 'error',
                                                    title: 'AJAX Error',
                                                    text: 'Something went wrong. Please try again.'
                                                });
                                            });
                                    }
                                });
                            });
                        });
                    });
                </script>
        </body>

        </html>
        <?php
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
                    window.location.href = '../Login_Page/Signin_page.php';
                });
            </script>
        </body>

        </html>
        <?php
    endif;
endif;
?>