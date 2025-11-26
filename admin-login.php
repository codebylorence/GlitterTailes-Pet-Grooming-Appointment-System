<?php
require './db/database.php';

session_start();

function pathTo($destination)
{
  echo "<script>window.location.href = 'admin/$destination.php'</script>";
}

if ($_SESSION['status'] == 'invalid' || empty($_SESSION['status'])) {
  /* Set Default Invalid */
  $_SESSION['status'] = 'invalid';
}

if ($_SESSION['status'] == 'valid') {
  pathTo('dashboard');
}
//LOGIN
// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Validate that both fields are present
  if (!empty($_POST['Lusername']) && !empty($_POST['Lpassword'])) {
    $username = trim($_POST['Lusername']); // Trim to remove unnecessary whitespace
    $password = trim($_POST['Lpassword']); // Trim to remove unnecessary whitespace

    $queryValidate = "SELECT * FROM admin WHERE Username = '$username' AND Password = '$password' ";
    $sqlValidate = mysqli_query($connection, $queryValidate);
    $rowValidate = mysqli_fetch_array($sqlValidate);

    if (mysqli_num_rows($sqlValidate) > 0) {
      $_SESSION['status'] = 'valid';
      $_SESSION['Username'] = $rowValidate['Username'];

      pathTo('dashboard');
    } else {
      $_SESSION['status'] = 'invalid';

      echo '<script>alert("Invalid username or password.")</script>';
    }
  } else {
    // Handle missing fields
    // echo "Please fill in both username and password.";
    echo '<script>alert("Please fill in both username and password.")</script>';
  }
} else {
  // Handle requests that are not POST
  // echo "Invalid request method.";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Login</title>
  <style>
    body {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #f4f4f9;
    }

    .login-container {
      width: 300px;
      padding: 20px;
      background: #ffffff;
      box-shadow: 0 48ppx 8px rgba(0, 0, 0, 0.1);
      border-radius: x;
      border-radius: 1%;
    }

    .login-container h2 {
      margin-bottom: 20px;
      font-size: 24px;
      text-align: center;
      color: #333333;
    }

    .login-container label {
      display: block;
      margin-bottom: 5px;
      font-size: 14px;
      color: #555555;
    }

    .login-container input {
      width: 93%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #363636;
      border-radius: 4px;
      font-size: 14px;
    }

    .login-container .login-btn {
      width: 100%;
      padding: 10px;
      background-color: #4f719e;
      border: none;
      border-radius: 4px;
      font-size: 16px;
      color: #ffffff;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .login-container button:hover {
      background-color: #4f719e;
    }
  </style>
</head>

<body>

  <div class="login-container">
    <h2 class="headtext">Admin Login</h2>
    <form action="./admin-login.php" method="post">
      <label for="email">Email:</label>
      <input
        input type="text" name="Lusername"
        placeholder="Enter your email"
        required />

      <label for="password">Password:</label>
      <input
        input type="password" name="Lpassword"
        placeholder="Enter your password"
        required />

      <input type="submit" value="Log in" class="login-btn">
    </form>
  </div>
</body>

</html>