<?php
// require_once $_SERVER['DOCUMENT_ROOT'] . '/Campion_Bealde_Slip/Connection/force_secure.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <!-- Alerts design reference -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>Thank You</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f9f9f9;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      color: #2c3e50;
      text-align: center;
      padding: 20px;
    }

    h1 {
      font-size: 2.5rem;
      margin-bottom: 10px;
      color: #C90808;
      user-select: none;
    }

    p {
      font-size: 1.2rem;
      margin-bottom: 40px;
      user-select: none;
    }

    .btn-group {
      display: flex;
      gap: 20px;
      justify-content: center;
    }

    button {
      background-color: #C90808;
      border: none;
      color: white;
      padding: 15px 30px;
      font-size: 1.1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
      user-select: none;
    }

    button:hover {
      background-color: #a00505;
    }

    button.secondary {
      background-color: #555;
    }

    button.secondary:hover {
      background-color: #333;
    }
  </style>
</head>

<body>
  <h1>THANK YOU FOR YOUR SUBMISSION</h1>
  <p>Would you like to submit another response?</p>
  <div class="btn-group" role="group" aria-label="Submit another response or finish">
    <button id="yesBtn">Yes, take me to Sign In</button>
    <button id="noBtn" class="secondary">No, I'm done</button>
  </div>

  <script>
    document.getElementById('yesBtn').addEventListener('click', () => {
      // Redirect to sign-in page
      window.location.href = 'http://localhost/campion_Beadle_Slip/Login_Page/Signin_page.php';
    });

    document.getElementById('noBtn').addEventListener('click', () => {
      Swal.fire({
        icon: 'success',
        title: 'Thank You!',
        text: 'Have a great day.'
      });
    });
  </script>
</body>

</html>