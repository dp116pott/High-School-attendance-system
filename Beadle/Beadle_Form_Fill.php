<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
session_start();
if (isset($_SESSION['beadle_id'], $_SESSION['beadle_id_expiry'])):
    if (time() > $_SESSION['beadle_id_expiry']):
        session_unset();
        session_destroy();
        echo "<script>location.reload();</script>";
        exit();
    endif;
    if (isset($_SESSION['stud_name'])):
        //database information
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);
        //ensures that necessary data values are still available
        if (isset($_SESSION['stud_name']) && isset($_SESSION['beadle_id']) && isset($_SESSION['class_filter'])) {
            $check_name = $_SESSION['stud_name'];
            $check_id = $_SESSION['beadle_id'];
            $classification = $_SESSION['class_filter'];
        } else {
            die('Error: Missing student ID or password!');
        }
        //finds the form-class/year group that the beadle is apart of
        $query_SQL = 'SELECT `beadle_class`FROM `beadle_info` WHERE `beadle_id` = :id_num';
        $process_stmt = $connection_instance->prepare($query_SQL);
        $process_stmt->bindParam("id_num", $check_id);
        $process_stmt->execute();
        $auto_class = $process_stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($auto_class as $class) {
            $temp_class = $class['beadle_class'];
        }
        //finds the specific class that the beadle is associated with
        $query_SQL2 = 'SELECT `class_id` FROM `class_list` WHERE `beadle_id` = :id_num';
        $process_stmt2 = $connection_instance->prepare($query_SQL2);
        $process_stmt2->bindParam("id_num", $check_id);
        $process_stmt2->execute();
        $id_store = $process_stmt2->fetchAll(PDO::FETCH_ASSOC);
        foreach ($id_store as $id) {
            $temp_id = $id['class_id'];
        }
        //Finds the teacher associated with the same class id found for the beadle
        $query_SQL3 = 'SELECT `teacher_name` FROM `teacher_info` WHERE `class_id` = :id_num2';
        $process_stmt3 = $connection_instance->prepare($query_SQL3);
        $process_stmt3->bindParam("id_num2", $temp_id);
        $process_stmt3->execute();
        $id_class = $process_stmt3->fetchAll(PDO::FETCH_ASSOC);
        foreach ($id_class as $class) {
            $teacher_name = $class['teacher_name'];
        }
        //finds all students apart of the particular class
//(since beadles in lowerschool have dominion over the same students for every class and in upperschool only have multiple classes when they are a core subject beadle and thus will have the same students to account for everytime, the function thus finds the students associated with the beadle id and not requiring it to be accounted for by the teacher id as well)
        $query_SQL5 = 'SELECT si.Student_name FROM student_info si JOIN student_subject_assignments sa ON sa.Student_id = si.Student_id JOIN class_list cl ON cl.beadle_id = :b_id WHERE `Student_class`=:class AND sa.class_id =cl.class_id ';
        $process_stmt5 = $connection_instance->prepare($query_SQL5);
        $process_stmt5->bindParam('class', $temp_class);
        $process_stmt5->bindParam('b_id', $check_id);
        $process_stmt5->execute();
        $form_class = $process_stmt5->fetchAll(PDO::FETCH_ASSOC);
        $i = 0;
        foreach ($form_class as $form_reg) {
            $std_name_class[$i] = $form_reg['Student_name'];
            $i = $i + 1;
        }
        $name_store = $std_name_class;
        $teachers = [];
        // Get the list of teacher IDs associated with the beadle
        $query_SQL7 = 'SELECT teacher_id FROM class_list WHERE beadle_id = :b_id';
        $process_stmt7 = $connection_instance->prepare($query_SQL7);
        $process_stmt7->bindParam('b_id', $check_id);
        $process_stmt7->execute();
        $t_ids = $process_stmt7->fetchAll(PDO::FETCH_ASSOC);
        $teacher_subjects = [];
        // For each teacher, get teacher_name and subject_name from the class
        foreach ($t_ids as $t_id) {
            // Join teacher_info and Subject_info tables to get the name of the subject
            $query_SQL8 = '
        SELECT t.teacher_name, lc.Subject_name
        FROM teacher_info t
        JOIN Subject_info lc ON t.class_id = lc.class_id
        JOIN class_list cl ON lc.class_id = cl.class_id
        AND cl.beadle_id = :b_id
        WHERE t.teacher_id = :t_id
    ';
            $process_stmt8 = $connection_instance->prepare($query_SQL8);
            $process_stmt8->bindParam("t_id", $t_id['teacher_id']);
            $process_stmt8->bindParam("b_id", $check_id);
            $process_stmt8->execute();
            $teacher = $process_stmt8->fetch(PDO::FETCH_ASSOC);
            if ($teacher) {
                $teachers[] = ['teacher_name' => $teacher['teacher_name']];
                $teacher_subjects[$teacher['teacher_name']] = $teacher['Subject_name'];
            }
        }
        ?>
        <!DOCTYPE html>
        <html>

        <script>
            var i;
            const name_arr = <?= json_encode($std_name_class) ?>;

        </script>
        <title>Beadle Form</title>

        <head meta charset="UTF-8">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script>
                const teacherSubjects = <?= isset($teacher_subjects) ? json_encode($teacher_subjects) : '{}' ?>;//save all of the teacher's taught subejcts in an array
                const class_name = '';
                const class_data = <?= json_encode($temp_class) ?>;
                const t_namedata = <?= json_encode($teacher_name) ?>;
                const student_name_suggestions = <?= json_encode($name_store) ?>;
            </script>
            <!-- References the CSS used to build this page's display -->
            <link href="http://localhost/campion_Beadle_Slip/CSS_stylesheets/Beadle_styles.css" rel="stylesheet">
            <!-- Alerts design reference -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        </head>

        <body>
            <div class="form-container">
                <h1>Welcome <?= htmlspecialchars($check_name) ?></h1>
                <p>Please carefully fill out the form below.</p>
                <form id="myform">
                    <label for="class">Form Class</label>
                    <input type="text" id="class" name="class" readonly value="<?= htmlspecialchars($temp_class) ?>">
                    <!-- after teacher is selected their relveant information such as the subject name is added and students are loaded for absences and lates -->
                    <label for="teacher">Teacher</label>
                    <select id="teacher" name="teacher[]" required>
                        <option value="">Select Teacher</option>
                        <?php foreach ($teachers as $teacher): ?>
                            <option value="<?= htmlspecialchars($teacher['teacher_name']) ?>">
                                <?= htmlspecialchars($teacher['teacher_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <div class="checkbox-group">
                        <label>Teacher Status</label>
                        <input type="checkbox" id="teach_pres"> Present
                        <input type="checkbox" id="teach_abs"> Absent
                    </div>

                    <label for="time_arr">Arrival Time</label>
                    <input type="time" id="time_arr" name="arrival_time" style="display: none;">

                    <label for="s_name">Subject</label>
                    <input type="text" id="s_name" name="Subject" readonly>
                    <!-- class start time that is used to determine whether teachers are late and help build reports -->
                    <label for="period">Class Start Period</label>
                    <select id="start_period" name="period" required>
                        <option value="">Select a period</option>
                        <option value="8:00">8:00</option>
                        <option value="8:35">8:35</option>
                        <option value="9:10">9:10</option>
                        <option value="9:45">9:45</option>
                        <option value="10:20">10:20</option>
                        <option value="10:55">10:55</option>
                        <option value="11:30">11:30</option>
                        <option value="12:05">12:05</option>
                        <option value="12:40">12:40</option>
                        <option value="13:15">1:15</option>
                        <option value="13:50">1:50</option>
                        <option value="14:25">2:25</option>
                    </select>

                    <label for="d_sub">Date</label>
                    <input type="date" id="d_sub" name="Date" required>

                    <label>Students Late</label>
                    <div class="dropdown" id="lateDropdown">
                        <div class="dropdown-header">Select Students</div>
                        <div class="dropdown-options" id="lateOptions"></div>
                        <div class="selected-options" id="lateSelected"></div>
                    </div>

                    <label>Students Absent</label>
                    <div class="dropdown" id="absentDropdown">
                        <div class="dropdown-header">Select Students</div>
                        <div class="dropdown-options" id="absentOptions"></div>
                        <div class="selected-options" id="absentSelected"></div>
                    </div>

                    <button type="submit" class="submit-button" id="submit">Submit Form</button>
                </form>
            </div>
            <script>
                function hide() {
                    document.getElementById("time_arr").style.display = "none";
                }
                function show() {
                    document.getElementById("time_arr").style.display = "block";
                }
                $(document).ready(function () {

                    $('#teach_pres').on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#teach_abs').prop('checked', false);
                            show();
                        } else {
                            hide();
                        }
                    });

                    $('#teach_abs').on('change', function () {
                        if ($(this).is(':checked')) {
                            $('#teach_pres').prop('checked', false);
                            hide();
                        }
                    });

                    const classification = <?= json_encode($classification) ?>;

                    $('#teacher').on('change', function () {
                        const selectedTeacher = $(this).val();


                        if (Array.isArray(selectedTeacher) && selectedTeacher.length === 1) {
                            const subject = teacherSubjects[selectedTeacher[0]];
                            $('#s_name').val(subject || '');
                        } else if (!Array.isArray(selectedTeacher)) {
                            const subject = teacherSubjects[selectedTeacher];
                            $('#s_name').val(subject || '');
                        } else {
                            $('#s_name').val('');
                        }

                    });
                });


                document.addEventListener("DOMContentLoaded", function () {
                    const studentNameSuggestions = <?= json_encode($name_store) ?>;

                    const lateOptions = document.getElementById('lateOptions');
                    const absentOptions = document.getElementById('absentOptions');
                    const lateSelected = document.getElementById('lateSelected');
                    const absentSelected = document.getElementById('absentSelected');

                    // Helper function to create option divs
                    function createOption(name, container, otherContainer, selectedContainer) {
                        const option = document.createElement('div');
                        option.textContent = name;
                        option.dataset.name = name;
                        option.classList.add('option-item');

                        option.addEventListener('click', function () {
                            if (isSelected(name, selectedContainer)) return; // avoid duplicates

                            addSelectedOption(name, selectedContainer, otherContainer);
                            disableOptionInContainer(name, otherContainer, true);
                        });

                        container.appendChild(option);
                    }

                    // Check if already selected
                    function isSelected(name, container) {
                        return Array.from(container.children).some(child => child.textContent === name);
                    }

                    // Add selected student bubble
                    function addSelectedOption(name, selectedContainer, otherContainer) {
                        const optionBubble = document.createElement('div');
                        optionBubble.classList.add('selected-option');
                        optionBubble.textContent = name;

                        const removeIcon = document.createElement('span');
                        removeIcon.textContent = ' x';
                        removeIcon.style.cursor = 'pointer';

                        removeIcon.addEventListener('click', function () {
                            selectedContainer.removeChild(optionBubble);
                            disableOptionInContainer(name, otherContainer, false);
                            enableOptionInContainer(name, selectedContainer, false); // re-enable in own container
                        });

                        optionBubble.appendChild(removeIcon);
                        selectedContainer.appendChild(optionBubble);
                        disableOptionInContainer(name, selectedContainer, true); // disable in own container
                    }

                    // Disable or enable option in container
                    function disableOptionInContainer(name, container, disable) {
                        Array.from(container.children).forEach(option => {
                            if (option.dataset.name === name) {
                                option.style.pointerEvents = disable ? 'none' : 'auto';
                                option.style.opacity = disable ? 0.4 : 1;
                                option.style.userSelect = disable ? 'none' : 'auto';
                            }
                        });
                    }

                    // Enable or disable option in the same container for selection removal (optional)
                    function enableOptionInContainer(name, container, disable) {
                        disableOptionInContainer(name, container, disable);
                    }

                    // Populate dropdown options with student names
                    studentNameSuggestions.forEach(name => {
                        createOption(name, lateOptions, absentOptions, lateSelected);
                        createOption(name, absentOptions, lateOptions, absentSelected);
                    });

                    // Dropdown toggle handlers
                    function setupDropdown(dropdownId, optionsId) {
                        const dropdown = document.getElementById(dropdownId);
                        const options = document.getElementById(optionsId);

                        dropdown.querySelector('.dropdown-header').addEventListener('click', function () {
                            options.style.display = options.style.display === 'block' ? 'none' : 'block';
                        });

                        document.addEventListener('click', function (event) {
                            if (!dropdown.contains(event.target)) {
                                options.style.display = 'none';
                            }
                        });
                    }

                    setupDropdown('lateDropdown', 'lateOptions');
                    setupDropdown('absentDropdown', 'absentOptions');
                });


                // Set default values for readonly fields
                document.getElementById("class").value = <?= json_encode($temp_class) ?>;
                var s_late;
                document.getElementById("submit").addEventListener('click', function (event) {
                    const selectedLate = Array.from(document.querySelectorAll('#lateSelected .selected-option'))
                        .map(option => option.firstChild.textContent);
                    s_late = selectedLate;
                    if (document.getElementById("teach_abs").checked === true) {
                        document.getElementById("time_arr").value = "00:00"
                        //this ensures that upon submission if a student selects present and then a time and then switches the checked value to absent that the time is logged as a standard irrelevant value.
                    }
                });
                var s_absent;
                document.getElementById("submit").addEventListener('click', function (absent) {
                    const selectedAbsent = Array.from(document.querySelectorAll('#absentSelected .selected-option'))
                        .map(option => option.firstChild.textContent);
                    s_absent = selectedAbsent;
                });
                document.getElementById("myform").addEventListener("submit", function (event) {
                    event.preventDefault();
                });
                document.getElementById("submit").addEventListener('click', function (validate) {
                    if ((document.getElementById('teach_pres').checked == false && document.getElementById('teach_abs').checked == false) || (document.getElementById("start_period").value == "") || (document.getElementById("d_sub").value == "")) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Missing Information',
                            text: 'Fill out all required fields.'
                        });
                        validate.preventDefault();
                        return;
                    }
                    var late_list = JSON.stringify(s_late);
                    var absent_list = JSON.stringify(s_absent);
                    var f_class = document.getElementById('class').value;
                    var t_name = document.getElementById('teacher').value;
                    var s_name = document.getElementById('s_name').value;
                    var d_sub = document.getElementById('d_sub').value;
                    var t_time = document.getElementById('time_arr').value;
                    var t_status = document.getElementById('teach_pres').checked;
                    var start = document.getElementById('start_period').value;
                    const form_obj = {
                        Form_class: f_class,
                        Teacher_name: t_name,
                        Subject_name: s_name,
                        Date_submit: d_sub,
                        s_late: late_list,
                        s_absent: absent_list,
                        time: t_time,
                        teacher_status: t_status,
                        class_start: start,
                    };
                    $.ajax({
                        type: "POST",
                        url: "Form_process.php",
                        data: JSON.stringify(form_obj),
                        contentType: 'application/json',
                        success: function (response) {
                            let res;
                            try {
                                res = typeof response === 'string' ? JSON.parse(response) : response;
                            } catch (e) {
                                res = { status: 'error', message: 'Invalid server response format.' };
                            }

                            if (res.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Form Submitted',
                                    text: res.message || 'Your submission was successful.'
                                }).then(() => {
                                    window.location.replace("http://localhost/campion_Beadle_Slip/Beadle_Form/Post_submission.php");
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Submission Failed',
                                    text: res.message || 'There was an error processing your form.'
                                });
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error occurred:", status, error);
                            Swal.fire({
                                icon: 'error',
                                title: 'AJAX Error',
                                text: 'A network or server error occurred. Please try again.'
                            });
                        }
                    });
                })
            </script>
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