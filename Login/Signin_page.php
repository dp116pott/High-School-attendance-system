
<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Campion College Beadle Slip Sign In</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600&display=swap" rel="stylesheet">

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- References the CSS used to build this page's display -->
    <link  href = "http://localhost/campion_Beadle_Slip/CSS_stylesheets/Login_styles.css" rel = "stylesheet">
    <!-- References the file containing all relevant Javascript for this file -->
    <script src="http://localhost/campion_Beadle_Slip/JS_docs/Login.js"></script>
    <!-- Alerts design reference -->
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<!-- Campion Colloge Logo, this was taken from the assets at campioncollege.com -->
<div class="logo-container">
    <div class="logo-box">
        <img src="https://jamaker.s3.amazonaws.com/flfldr/PfLgLehrvKhciny3y.png" alt="Campion College Crest">
    </div>
</div>
    <!-- Container which houses the entire login portal-->
    <div class="login-container">
        <div class="role-buttons">
            <h1>Select Login Type</h1>
            <!-- Provides a switch in interfaces between admin and student logins when button is selected -->
            <button class="btn btn-primary" onclick="showForm('beadle')">Beadle</button>
            <button class="btn btn-primary" onclick="showForm('admin')">Admin</button>
        </div>
        <!--When the information is submitted this calls a function which executes an ajax POST request to the "info_check.php" verification file-->
        <form id="loginForm" style="display: none;" method="POST" onsubmit="return pushfunc(event);">
            <h1 id="formTitle">Login</h1>
            <label for="check_id" id="labelId">ID</label>
            <input type="text" class="form-control" id="check_id" name="check_id" placeholder="Enter your ID" required>

            <label for="check_pass" id="labelPass">Password</label>
            <input type="password" class="form-control" id="check_pass" name="check_pass" placeholder="Enter your password" required>

            <div class="text-center">
                <input type="submit" class="btn btn-primary" value="Login">
            </div>
        </form>
    </div>
</body>
</html>