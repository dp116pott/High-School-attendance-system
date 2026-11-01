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
                    location.replace("../Login_Page/Signin_page.php");
                });
            </script>
        </body>

        </html>
        <?php
        exit();
    endif;
    if (isset($_SESSION['supervisor_name'])):
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
        }
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);

        // Get supervisor ID from GET
        if (!isset($_GET['supervisor_id'])) {
            // If AJAX POST, respond with JSON error
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                echo json_encode(['status' => 'error', 'message' => 'Missing supervisor ID.']);
                exit;
            }
            die("Missing supervisor ID.");
        }

        $supervisor_id = $_GET['supervisor_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $new_id = $_POST['sp_id'] ?? '';
            $original_id = $_POST['original_id'] ?? '';
            $password = $_POST['admin_password'] ?? '';
            $name = $_POST['Name'] ?? '';
            $form_class = $_POST['Form_class'] ?? '';

            // Basic field check (password allowed to be empty here)
            if (!$new_id || !$original_id || !$name || !$form_class) {
                echo json_encode(['status' => 'error', 'message' => $form_class . 'All fields except password are required.']);
                exit;
            }

            $update_sql = "UPDATE admin_credentials 
                   SET Admin_ID = :new_id, Name = :name, Form_class = :form_class";

            $params = [
                ':new_id' => $new_id,
                ':name' => $name,
                ':form_class' => $form_class,
                ':original_id' => $original_id
            ];

            // Only include password in the query if it's not empty
            if (trim($password) !== '') {
                $update_sql .= ", admin_password = :sp_password";
                $params[':sp_password'] = password_hash($password, PASSWORD_DEFAULT); // password
            }

            $update_sql .= " WHERE Admin_ID = :original_id";

            $stmt = $connection_instance->prepare($update_sql);

            try {
                $stmt->execute($params);
                echo json_encode(['status' => 'success', 'message' => 'Supervisor updated successfully!']);
                exit;
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()]);
                exit;
            }
        }
        // Fetch existing data for GET request (page load)
        $select_sql = "SELECT * FROM admin_credentials WHERE Admin_ID = :id";
        $stmt = $connection_instance->prepare($select_sql);
        $stmt->execute([':id' => $supervisor_id]);
        $supervisor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$supervisor) {
            die("Supervisor not found.");
        }
        $original_id = $supervisor['Admin_ID'];
        ?>

        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Edit Supervisor</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
            <!-- Alerts design reference -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background-color: #f8f9fa;
                    margin: 0;
                    display: flex;
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
                    margin-left: 260px;
                    padding: 40px;
                    width: 100%;
                }

                h1 {
                    color: #002147;
                    margin-bottom: 20px;
                }

                form {
                    background: #fff;
                    padding: 30px;
                    border-radius: 8px;
                    max-width: 600px;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                }

                label {
                    font-weight: 600;
                    display: block;
                    margin-top: 15px;
                    margin-bottom: 5px;
                }

                input[type="text"],
                select {
                    width: 100%;
                    padding: 10px;
                    border-radius: 6px;
                    border: 1px solid #ccc;
                    margin-bottom: 15px;
                    font-size: 16px;
                }

                input[type="password"],
                select {
                    width: 100%;
                    padding: 10px;
                    border-radius: 6px;
                    border: 1px solid #ccc;
                    margin-bottom: 15px;
                    font-size: 16px;
                }

                button {
                    background-color: #002147;
                    color: white;
                    padding: 10px 20px;
                    border: none;
                    border-radius: 6px;
                    cursor: pointer;
                    font-weight: 600;
                }

                button:hover {
                    background-color: #001530;
                }

                @media (max-width: 768px) {
                    .sidebar {
                        width: 60px;
                        padding: 20px 10px;
                    }

                    .sidebar h2 {
                        display: none;
                    }

                    .sidebar ul li a {
                        font-size: 0;
                        padding: 10px 5px;
                    }

                    .sidebar ul li a::before {
                        content: attr(data-icon);
                        font-size: 20px;
                        display: inline-block;
                        color: #cce0ff;
                    }

                    .main-content {
                        margin-left: 80px;
                        padding: 20px;
                    }
                }
            </style>
        </head>

        <body>

            <!-- Sidebar -->
            <div class="sidebar">
                <h2>Dashboard</h2>
                <ul>
                    <li><a href="webmaster_dashboard.php">🏠 Home</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <h1>Edit Supervisor</h1>
                <!-- Post request that is sent back to this same page which contains all of the relevant details for the update -->
                <form id="update-supervisor-form">
                    <!-- inputs as before -->
                    <label for="sp_id">Supervisor ID</label>
                    <input type="text" name="sp_id" id="sp_id" value="<?= htmlspecialchars($supervisor['Admin_ID']) ?>"
                        required>

                    <label for="admin_password">Password</label>
                    <input type="password" name="admin_password" id="admin_password" value=""
                        placeholder="Only enter if updating password">

                    <label for="Name">Supervisor Name</label>
                    <input type="text" name="Name" id="Name" value="<?= htmlspecialchars($supervisor['Name']) ?>" required>

                    <label for="Form_class">Form Class</label>
                    <input type="text" name="Form_class" id="Form_class"
                        value="<?= htmlspecialchars($supervisor['Form_class']) ?>" required>

                    <input type="hidden" name="original_id" value="<?= htmlspecialchars($original_id) ?>">

                    <button type="submit">Update Supervisor</button>
                </form>

            </div>
            <script>
                document.getElementById('update-supervisor-form').addEventListener('submit', function (e) {
                    e.preventDefault();

                    const form = e.target;
                    const formData = new FormData(form);

                    // Note: URL includes supervisor_id GET parameter, required by PHP
                    const urlParams = new URLSearchParams(window.location.search);
                    const supervisorId = urlParams.get('supervisor_id');

                    if (!supervisorId) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Supervisor ID missing from URL.'
                        });
                        return;
                    }

                    fetch(`?supervisor_id=${encodeURIComponent(supervisorId)}`, {
                        method: 'POST',
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: data.message
                                }).then(() => {
                                    // Redirect to dashboard after success
                                    window.location.href = 'Supervisor_management.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.message
                                });
                            }
                        })
                        .catch(() => {
                            Swal.fire({
                                icon: 'error',
                                title: 'AJAX Error',
                                text: 'Failed to submit. Please try again.'
                            });
                        });
                });
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
                window.location.href = '../Login_Page/Signin_page.php';
            });
        </script>
    </body>

    </html>
    <?php
endif;
?>