<?php
require '../session.php';
require '../db/database.php';

// Get the logged-in user's Username from the session
if (isset($_SESSION['Username'])) {
    $loggedInUsername = $_SESSION['Username'];

    // Fetch the logged-in user's account details
    $queryAccount = "SELECT UserId, Firstname, Lastname, Username, Password FROM useraccounts WHERE Username = ?";
    $stmtAccount = mysqli_prepare($connection, $queryAccount);
    mysqli_stmt_bind_param($stmtAccount, "s", $loggedInUsername);
    mysqli_stmt_execute($stmtAccount);
    $resultAccount = mysqli_stmt_get_result($stmtAccount);
    $userData = mysqli_fetch_assoc($resultAccount);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update'])) {
            // Update user account
            $updateFirstname = htmlspecialchars($_POST['updateFirstname']);
            $updateLastname = htmlspecialchars($_POST['updateLastname']);
            $updateUsername = htmlspecialchars($_POST['updateUsername']);
            $updatePassword = htmlspecialchars($_POST['updatePassword']);

            $userId = $userData['UserId'];

            // Check if a new password is provided
            if (!empty($updatePassword)) {
                // Hash the password before updating
                $hashedPassword = password_hash($updatePassword, PASSWORD_DEFAULT);
            } else {
                // Keep the current password if no new password is provided
                $hashedPassword = $userData['Password'];
            }

            // Update query with prepared statement
            $queryUpdate = "UPDATE useraccounts 
                            SET Firstname = ?, Lastname = ?, Username = ?, Password = ? 
                            WHERE UserId = ?";
            $stmtUpdate = mysqli_prepare($connection, $queryUpdate);
            mysqli_stmt_bind_param($stmtUpdate, "ssssi", $updateFirstname, $updateLastname, $updateUsername, $hashedPassword, $userId);
          
            if (mysqli_stmt_execute($stmtUpdate)) {
                echo '<script>alert("Account successfully updated!");</script>';
                session_destroy();
                echo '<script>window.location.href = "../index.php";</script>';
            } else {
                echo '<script>alert("Error updating account.");</script>';
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <link rel="stylesheet" href="../styles/generals.css">
  <title>Edit Account</title>
  <style>

    h3{
      text-align: center;
    }

    .update-section{
      display: flex;
      justify-content: center;
    }

    .update-main{
      display: flex;
      flex-direction: column;
      gap: 10px;
      width: 400px;
      height: 450px;
      padding: 20px;
      background-color: white;
      border: 1px gray solid;
      border-radius: 10px;

    }

    .change-info, .update-btn{
      padding: 10px;
    }

    .update-btn{
      margin-top: 25px;
      color: white;
      background-color:  #4db6e9;
      border: none;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <div>
    <section class="side-bar">
      <div class="admin-profile">
        <div class="admin-info">
          <div class="img-container">
            <div class="img-border">
              <img src="../assets/people.png" class="admin-img">
            </div>
          </div>
          <div class="admin-acc">
            <p class="admin-name"><?php echo htmlspecialchars($_SESSION['Username']); ?></p>
            <p class="admin-email">user@sstails.com</p>
          </div>
        </div>
        <div>
          <form action="../logout.php" method="post">
            <input type="submit" value="Log out" class="logout-btn">
          </form>
        </div>
      </div>
      <hr class="line">
      <div class="sidebar-btns">
      <div class="side-btn-container home-js">
          <div><img src="../assets/house-outline.png" class="img-btn"></div>
          <div class="side-btn">Home</div>
        </div>
        <div class="side-btn-container groomers-js">
          <div><img src="../assets/brush.png" class="img-btn"></div>
          <div class="side-btn">All Groomers</div>
        </div>
        <div class="side-btn-container  bookSched-js">
          <div><img src="../assets/event.png" class="img-btn"></div>
          <div class="side-btn">Schedule</div>
        </div>
        <div class="side-btn-container bookings-js">
          <div><img src="../assets/ribbon.png" class="img-btn"></div>
          <div class="side-btn">My Bookings</div>
        </div>
        <div class="side-btn-container settings-js">
          <div><img src="../assets/settings.png" class="img-btn"></div>
          <div class="side-btn">Settings</div>
        </div>
      </div>
    </section>
    <section class="update-section">
      <?php if ($userData): ?>
        <form class="update-main" action="" method="post">
          <h3>ACCOUNT SETTINGS</h3>
          <label>Firstname</label>
          <input type="text" name="updateFirstname" placeholder="Enter your firstname" value="<?php echo htmlspecialchars($userData['Firstname']); ?>" class="change-info">
          <label>Lastname</label>
          <input type="text" name="updateLastname" placeholder="Enter your lastname" value="<?php echo htmlspecialchars($userData['Lastname']); ?>" class="change-info">
          <label>Username</label>
          <input type="text" name="updateUsername" placeholder="Enter your username" value="<?php echo htmlspecialchars($userData['Username']); ?>" class="change-info">
          <label>Password</label>
          <input type="password" name="updatePassword" placeholder="Enter your password" value="<?php echo htmlspecialchars($userData['Password']); ?>" class="change-info">
          <input type="submit" name="update" value="UPDATE" class="update-btn">
        </form>
      <?php else: ?>
        <p>No user data found. Please try again later.</p>
      <?php endif; ?>
    </section>
    <script src="../script/userRedirect.js"></script>
  </div>
</body>

</html>
