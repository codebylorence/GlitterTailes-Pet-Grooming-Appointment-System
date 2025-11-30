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
  <title>Settings</title>
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      margin-top: 0;
      background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
      margin-left: 280px;
      font-family: 'Poppins', sans-serif;
      padding: 40px 20px 20px;
      min-height: 100vh;
    }

    h3 {
      text-align: center;
      color: #28496b;
      font-size: 1.8rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      margin-bottom: 20px;
    }

    .update-section {
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 70vh;
    }

    .update-main {
      display: flex;
      flex-direction: column;
      gap: 15px;
      width: 450px;
      padding: 40px;
      background: white;
      border: none;
      border-radius: 20px;
      box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
    }

    .update-main label {
      font-weight: 600;
      color: #28496b;
      font-size: 0.95rem;
      margin-top: 5px;
    }

    .change-info {
      padding: 14px 18px;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: #f8f9fa;
    }

    .change-info:focus {
      outline: none;
      border-color: #4db6e9;
      background: white;
      box-shadow: 0 0 0 4px rgba(77, 182, 233, 0.1);
    }

    .update-btn {
      margin-top: 25px;
      padding: 16px;
      color: white;
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      border: none;
      border-radius: 12px;
      font-weight: 700;
      font-size: 1.1rem;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 5px 20px rgba(40, 73, 107, 0.3);
    }

    .update-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(40, 73, 107, 0.4);
    }

    .update-btn:active {
      transform: translateY(0);
    }
  </style>
</head>

<body>
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
      <div class="side-btn-container bookSched-js">
        <div><img src="../assets/event.png" class="img-btn"></div>
        <div class="side-btn">Schedule</div>
      </div>
      <div class="side-btn-container bookings-js">
        <div><img src="../assets/ribbon.png" class="img-btn"></div>
        <div class="side-btn">My Bookings</div>
        <?php
        // Check for unread notifications
        if (isset($userData['UserId'])) {
            $userId = $userData['UserId'];
            $queryNotif = "SELECT COUNT(*) as unread FROM notifications WHERE UserId = ? AND Is_Read = 0";
            $stmtNotif = mysqli_prepare($connection, $queryNotif);
            mysqli_stmt_bind_param($stmtNotif, "i", $userId);
            mysqli_stmt_execute($stmtNotif);
            $resultNotif = mysqli_stmt_get_result($stmtNotif);
            $notifData = mysqli_fetch_assoc($resultNotif);
            $unreadCount = $notifData['unread'];
            if ($unreadCount > 0) {
                echo '<span style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 700; margin-left: 8px; position: absolute; right: 15px;">' . $unreadCount . '</span>';
            }
        }
        ?>
      </div>
      <div class="side-btn-container settings-js active">
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
</body>

</html>
