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
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
      <meta charset="UTF-8" />
      <meta name="viewport" content="width=device-width, initial-scale=1" />
      <title>Admin Dashboard</title>
      <!-- References the CSS used to build this page's display -->
      <link href="http://localhost/campion_Beadle_Slip/CSS_stylesheets/Webmasterdash_styles.css" rel="stylesheet">
      <!-- Alerts design reference -->
      <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    </head>

    <body>
      <nav class="sidebar">
        <h2>Dashboard</h2>
        <ul>
          <li><a href="http://localhost/campion_Beadle_Slip/Webmaster_Folder/webmaster_dashboard.php" class="active">🏠
              Home</a></li><!--Shows the sidebar active tab as current page -->
        </ul>
      </nav>

      <main class="main-content">
        <h1>Welcome to Your Admin Dashboard, Web Master</h1>

        <section class="dashboard" aria-label="Main dashboard options">
          <article class="dashcard"
            data-link="http://localhost/campion_Beadle_Slip/Webmaster_Folder/Supervisor_management.php" tabindex="0"
            role="button" aria-pressed="false">
            <h3>Supervisor Management</h3>
            <p>View current supervisors and manipulate their stored information</p>
          </article>

          <article class="dashcard" id="import-card" tabindex="0" role="button" aria-pressed="false">
            <h3>Imports</h3>
            <p>Allows for the uploading of new data files to update current tables</p>
          </article>
        </section>

        <section id="upload-container" aria-label="File upload section">
          <form action="import_tables.php" method="post" enctype="multipart/form-data">
            <div id="upload-section" style="display: none;">
              <select name="table_name" id="table" required aria-required="true">
                <option value="" disabled selected>Choose table to upload to</option>
                <option value="student_info">Student Info</option>
                <option value="teacher_info">Teacher Info</option>
                <option value="class_list">Class List</option>
                <option value="Subject_info">Subject Information</option>
                <option value="Student_subject_assignments">Student Subject Assignments</option>
                <option value="beadle_info">Beadle Information</option>
              </select>

              <input type="file" name="csv_file" id="file" accept=".csv" required aria-required="true" />

              <button type="submit" name="submit">Upload</button>
            </div>
          </form>
        </section>
      </main>
      <script src="http://localhost/campion_Beadle_Slip/JS_docs/Webmasterdash.js"></script>
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