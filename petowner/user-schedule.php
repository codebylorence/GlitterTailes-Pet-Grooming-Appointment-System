<?php
require '../session.php';
require '../db/database.php';

// Set the timezone to Philippine Time (UTC+8)
date_default_timezone_set('Asia/Manila');

// Check if the user is logged in
if (!isset($_SESSION['Username'])) {
    echo '<script>alert("You must be logged in to rebook an appointment.");</script>';
    echo '<script>window.location.href = "../index.php";</script>';
    exit();
}

// Remove an appointment
if (isset($_POST['remove'])) {
    $removeId = $_POST['removehis'];

    $queryRemove = "DELETE FROM history WHERE History_id = ?";
    $stmtRemove = mysqli_prepare($connection, $queryRemove);
    mysqli_stmt_bind_param($stmtRemove, "i", $removeId);

    if (mysqli_stmt_execute($stmtRemove)) {
        echo '<script>alert("Successfully deleted!");</script>';
        echo '<script>window.location.href = "./user-schedule.php";</script>';
    } else {
        echo '<script>alert("Failed to delete the appointment. Please try again.");</script>';
    }
}

// Rebook an appointment
if (isset($_POST['submit'])) {
    $historyId = $_POST['submitaptm'];
    $date = $_POST['date'];
    $time = $_POST['aptmtime'];

    // Combine the selected date and time into one datetime value
    $selectedDateTime = date('Y-m-d H:i:s', strtotime("$date $time"));
    $currentDateTime = date('Y-m-d H:i:s'); // Current time in Philippine Time

    // Check if the selected date and time are in the past
    if ($selectedDateTime < $currentDateTime) {
        echo '<script>alert("The selected date and time have already passed. Please choose a future date and time.");</script>';
        echo '<script>window.location.href = "./user-schedule.php";</script>';
        exit();
    }

    $username = $_SESSION['Username'];

    // Get the UserId
    $queryUserId = "SELECT UserId FROM useraccounts WHERE Username = ?";
    $stmtUserId = mysqli_prepare($connection, $queryUserId);
    mysqli_stmt_bind_param($stmtUserId, "s", $username);
    mysqli_stmt_execute($stmtUserId);
    $resultUserId = mysqli_stmt_get_result($stmtUserId);

    if ($rowUser = mysqli_fetch_assoc($resultUserId)) {
        $userId = $rowUser['UserId'];

        // Fetch appointment details from history
        $queryDetails = "SELECT Email_Address, Phone_Number, Address, Pet_Name, Pet_Breed, Pet_Age, Service_Type 
                         FROM history WHERE History_id = ?";
        $stmtDetails = mysqli_prepare($connection, $queryDetails);
        mysqli_stmt_bind_param($stmtDetails, "i", $historyId);
        mysqli_stmt_execute($stmtDetails);
        $resultDetails = mysqli_stmt_get_result($stmtDetails);

        if ($row = mysqli_fetch_assoc($resultDetails)) {
            $emailAddress = $row['Email_Address'];
            $phoneNumber = $row['Phone_Number'];
            $address = $row['Address'];
            $petName = $row['Pet_Name'];
            $petBreed = $row['Pet_Breed'];
            $petAge = $row['Pet_Age'];
            $serviceType = $row['Service_Type'];

            // Check for conflicting appointments
            $validTime = "SELECT 1 FROM (
                            SELECT Date, Time FROM history WHERE Date = ? AND Time = ? AND History_id != ?
                            UNION
                            SELECT Date, Time FROM appointments WHERE Date = ? AND Time = ?
                          ) AS conflicts";
            $stmtValidTime = mysqli_prepare($connection, $validTime);
            mysqli_stmt_bind_param($stmtValidTime, "sssss", $date, $time, $historyId, $date, $time);
            mysqli_stmt_execute($stmtValidTime);
            $resultValidTime = mysqli_stmt_get_result($stmtValidTime);

            if ($resultValidTime && mysqli_num_rows($resultValidTime) > 0) {
                echo '<script>alert("Failed, Date and time already taken. Please select another slot.");</script>';
                echo '<script>window.location.href = "./user-schedule.php";</script>';
            } else {
                // Insert into history table
                $queryCreate = "INSERT INTO history 
                                (UserId, Email_Address, Phone_Number, Address, Pet_Name, Pet_Breed, Pet_Age, Date, Time, Service_Type) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtCreate = mysqli_prepare($connection, $queryCreate);
                mysqli_stmt_bind_param(
                    $stmtCreate,
                    "isssssssss",
                    $userId,
                    $emailAddress,
                    $phoneNumber,
                    $address,
                    $petName,
                    $petBreed,
                    $petAge,
                    $date,
                    $time,
                    $serviceType
                );

                // Insert into appointments table
                $queryAptm = "INSERT INTO appointments 
                              (UserId, Email_Address, Phone_Number, Address, Pet_Name, Pet_Breed, Pet_Age, Date, Time, Service_Type) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtAptm = mysqli_prepare($connection, $queryAptm);
                mysqli_stmt_bind_param(
                    $stmtAptm,
                    "isssssssss",
                    $userId,
                    $emailAddress,
                    $phoneNumber,
                    $address,
                    $petName,
                    $petBreed,
                    $petAge,
                    $date,
                    $time,
                    $serviceType
                );

                if (mysqli_stmt_execute($stmtCreate) && mysqli_stmt_execute($stmtAptm)) {
                    echo '<script>alert("Successfully rebooked the appointment!");</script>';
                    echo '<script>window.location.href = "./user-schedule.php";</script>';
                } else {
                    echo '<script>alert("Failed to rebook. Please try again.");</script>';
                }
            }
        } else {
            echo '<script>alert("Error fetching appointment details.");</script>';
        }
    } else {
        echo '<script>alert("Error: User not found.");</script>';
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
    .delete{
      width: 5rem;
      height: 30px;
      background-color:rgb(247, 85, 85);
      color: white;
      border: none;
      font-weight: bold;
    }
    .container{
      position: relative;
    }
    .top-right{
      position: absolute;
      right:1.1rem;
    }

    .input, select{
      padding: 10px;
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
      <h1>APPOINTMENT HISTORY</h1>

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

          // Fetch history for the logged-in user [TO BE DISPLAYED]
          $query = "SELECT History_id, Pet_Name, Pet_Breed, Pet_Age, Date, Time, Service_Type
                FROM history 
                WHERE UserId = ?";
          $stmt = mysqli_prepare($connection, $query);
          mysqli_stmt_bind_param($stmt, "i", $userId);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);

          // Display the history
          if ($result && mysqli_num_rows($result) > 0) {
            while ($aptmInfo = mysqli_fetch_assoc($result)) { ?>
              <div class="aptm-container container">
                <form method="post"> 
                  <input class="delete top-right" type="submit" name="remove" value="Remove" onclick="return confirm('Are you sure you want to delete this appointment?');"/>
                  <input type="hidden" name="removehis" value="<?php echo $aptmInfo['History_id']; ?>"/>
                </form> 
                <p>Appointment Date: <?php echo htmlspecialchars($aptmInfo['Date']); ?></p>
                <p>Appointment Time: <?php echo htmlspecialchars($aptmInfo['Time']); ?></p>
                <p>Pet Name: <?php echo htmlspecialchars($aptmInfo['Pet_Name']); ?></p>
                <p>Pet Breed: <?php echo htmlspecialchars($aptmInfo['Pet_Breed']); ?></p>
                <p>Service Type: <?php echo htmlspecialchars($aptmInfo['Service_Type']); ?></p>
                <form class="form-container" method="post">
                <div style="display: flex; gap: 40px;">
                <input type="date" name="date" class="input" required>
                <label for="time">
                  <select name="aptmtime" id="time" reqiured>
                    <option value="8:00 AM">8:00 AM</option>
                    <option value="1:00 PM">1:00 PM</option>
                    <option value="4:00 PM">4:00 PM</option>
                    <option value="7:00 PM">7:00 PM</option>
                  </select>
                </label>                
              </div>
                  <br>
                  <input type="submit" name="submit" value="Book Again" style="width: 100%; height: 30px; background-color: #4db6e9; color: white; border: none; font-weight: bold;">
                  <input type="hidden" name="submitaptm" value="<?php echo $aptmInfo['History_id']; ?>">
                </form>
              </div>
            <?php }
          } else {
            echo "<p>No History found for the logged-in user.</p>";
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