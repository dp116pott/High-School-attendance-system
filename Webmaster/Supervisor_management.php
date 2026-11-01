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
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/config.php';
        include_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Beadle_Slip/Connection/database.php';
        $connection_instance = connect($config);
        ?>
        <!DOCTYPE html>
        <html lang="en">

        <head>
            <meta charset="UTF-8">
            <title>Supervisor Dashboard</title>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
            <!-- Alerts design reference -->
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <style>
                body {
                    font-family: 'Poppins', sans-serif;
                    background-color: #f8f9fa;
                    margin: 0;
                    padding: 0;
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

                /* Main content area */
                .main-content {
                    margin-left: 260px;
                    /* space for sidebar */
                    padding: 30px;
                    width: 100%;
                }

                h1 {
                    font-size: 28px;
                    color: #002147;
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                    background: white;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                    overflow: hidden;
                }

                thead {
                    background-color: #900000;
                    /* Campion red */
                    color: white;
                }

                th,
                td {
                    padding: 14px;
                    text-align: left;
                    border-bottom: 1px solid #ddd;
                }

                tr:hover {
                    background-color: #f1f1f1;
                }

                .edit-btn {
                    background-color: #002147;
                    color: white;
                    border: none;
                    padding: 8px 14px;
                    border-radius: 5px;
                    cursor: pointer;
                    transition: background-color 0.3s;
                }

                .edit-btn:hover {
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
                    }
                }
            </style>
        </head>

        <body>

            <!-- Sidebar -->
            <div class="sidebar">
                <h2>Dashboard</h2>
                <ul>
                    <li><a href="http://localhost/campion_Beadle_Slip/Webmaster_Folder/webmaster_dashboard.php">🏠 Home</a></li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <h1>Supervisor Management</h1>
                <table>
                    <thead>
                        <tr>
                            <th>Supervisor ID</th>
                            <th>Supervisor Name</th>
                            <th>Form Managed</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $query_SQL = 'SELECT * FROM admin_credentials WHERE admin_id > 0';//fetches supervisor information(particularly excluding the webmaster credentails)
                        $stmt = $connection_instance->prepare($query_SQL);
                        $stmt->execute();
                        while ($sup_list = $stmt->fetch(PDO::FETCH_ASSOC)) {//This essentially works in the way that as long as the fetch returns viable results the loop will occur
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($sup_list['Admin_ID']) . '</td>';
                            echo '<td>' . htmlspecialchars($sup_list['Name']) . '</td>';
                            echo '<td>' . htmlspecialchars($sup_list['Form_class']) . '</td>';
                            echo "<td>
                    <button class='edit-btn' onclick=\"window.location.href='Adjust_supervisor.php?supervisor_id=" . urlencode($sup_list['Admin_ID']) . "'\">Edit</button>
                </td>";
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
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
                window.location.href = '../Login_Page/Signin_page.php';
            });
        </script>
    </body>

    </html>
    <?php
endif;
?>