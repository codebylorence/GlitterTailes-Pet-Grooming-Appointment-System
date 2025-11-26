<?php 
require '../session.php';
require './appointment-info.php';

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Get the current date and time in 12-hour format
$currentDateTime = date('Y-m-d g:i A'); // Current date and time in 12-hour format

// Automatically delete past appointments
$queryDeletePast = "DELETE FROM appointments WHERE STR_TO_DATE(CONCAT(Date, ' ', Time), '%Y-%m-%d %l:%i %p') < STR_TO_DATE(?, '%Y-%m-%d %l:%i %p')";
$stmtDeletePast = mysqli_prepare($connection, $queryDeletePast);
mysqli_stmt_bind_param($stmtDeletePast, "s", $currentDateTime);
mysqli_stmt_execute($stmtDeletePast);

// Handle removing an appointment
if (isset($_POST['remove'])) {
    $removeId = $_POST['removeaptm'];

    // Safely prepare the query to delete an appointment using Appointment_id
    $queryRemove = "DELETE FROM appointments WHERE Appointment_id = ?";
    $stmtRemove = mysqli_prepare($connection, $queryRemove);
    mysqli_stmt_bind_param($stmtRemove, "i", $removeId);
    mysqli_stmt_execute($stmtRemove);

    echo '<script>alert("Successfully deleted!");</script>';
    echo '<script>window.location.href = "./user-bookings.php";</script>';
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <link rel="stylesheet" href="../styles/generals.css">
  <title>Home</title>
  <style>
    .aptm-container{
      display: flex;
      flex-direction: column;
      background-color: white;
      border: solid 1px;
      padding: 20px;
      gap: 10px;
    }

    .aptms{
      display: grid;
      gap: 20px;
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
            <p class="admin-name"><?php echo $_SESSION['Username']; ?></p>
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
    <section class="aptms">
      <h1>SCHEDULED APPOINTMENTS</h1>

      <?php
      if (isset($_SESSION['Username'])) {
        $username = $_SESSION['Username'];

        // Fetch the UserId of the logged-in user
        $queryUserId = "SELECT UserId FROM useraccounts WHERE Username = ?";
        $stmtUserId = mysqli_prepare($connection, $queryUserId);
        mysqli_stmt_bind_param($stmtUserId, "s", $username);
        mysqli_stmt_execute($stmtUserId);
        $resultUserId = mysqli_stmt_get_result($stmtUserId);

        if ($row = mysqli_fetch_assoc($resultUserId)) {
          $userId = $row['UserId'];

          // Fetch appointments for the logged-in user
          $query = "SELECT Appointment_id, Email_Address, Phone_Number, Pet_Name, Pet_Breed, Pet_Age, Date, Time, Service_Type 
                FROM appointments 
                WHERE UserId = ?";
          $stmt = mysqli_prepare($connection, $query);
          mysqli_stmt_bind_param($stmt, "i", $userId);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);

          // Display the appointments
          if ($result && mysqli_num_rows($result) > 0) {
            while ($aptmInfo = mysqli_fetch_assoc($result)) { ?>
              <div class="aptm-container">
                <p>Appointment Date: <?php echo htmlspecialchars($aptmInfo['Date']); ?></p>
                <p>Appointment Time: <?php echo htmlspecialchars($aptmInfo['Time']); ?></p>
                <p>Pet Name: <?php echo htmlspecialchars($aptmInfo['Pet_Name']); ?></p>
                <p>Pet Breed: <?php echo htmlspecialchars($aptmInfo['Pet_Breed']); ?></p>
                <p>Appointment Number: <?php echo htmlspecialchars($aptmInfo['Appointment_id']); ?></p>
                <p>Service Type: <?php echo htmlspecialchars($aptmInfo['Service_Type']); ?></p>
                <form action="./user-bookings.php" method="post">
                  <input type="submit" name="remove" value="Cancel Booking" style="width: 100%; height: 30px; background-color: #D11A2A; color: white; border: none; font-weight: bold;"  onclick="return confirm('Are you sure you want to cancel this appointment?');">
                  <input type="hidden" name="removeaptm" value="<?php echo $aptmInfo['Appointment_id']; ?>">
                </form>
              </div>
            <?php }
          } else {
            echo "<p>No appointments found for the logged-in user.</p>";
          }
        } else {
          echo "<p>Error: Unable to fetch user information. Please try again.</p>";
        }
      } else {
        echo "<p>Error: You must be logged in to view your appointments.</p>";
      }
      ?>
    </section>
    <script src="../script/userRedirect.js"></script>
  </div>
</body>

</html>
