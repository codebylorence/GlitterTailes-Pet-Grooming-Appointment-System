<?php
require '../db/database.php';
require '../session.php';

// Set the timezone to Philippine Time (UTC+8)
date_default_timezone_set('Asia/Manila');
// Automatically delete appointments that have already passed the current date and time
$currentDateTime = date('Y-m-d g:i A');  // Current time in 12-hour format with AM/PM
$queryDeletePast = "DELETE FROM appointments WHERE STR_TO_DATE(CONCAT(Date, ' ', Time), '%Y-%m-%d %l:%i %p') < STR_TO_DATE(?, '%Y-%m-%d %l:%i %p')";
$stmtDeletePast = mysqli_prepare($connection, $queryDeletePast);
mysqli_stmt_bind_param($stmtDeletePast, "s", $currentDateTime);
mysqli_stmt_execute($stmtDeletePast);

if (isset($_POST['submit'])) {
    // Sanitize user input
    $emailAddress = mysqli_real_escape_string($connection, $_POST['emailaddress']);
    $phoneNumber = mysqli_real_escape_string($connection, $_POST['phonenumber']);
    $address = mysqli_real_escape_string($connection, $_POST['address']);
    $petName = mysqli_real_escape_string($connection, $_POST['petname']);
    $petBreed = mysqli_real_escape_string($connection, $_POST['petbreed']);
    $petAge = (int)$_POST['petage'];
    $date = mysqli_real_escape_string($connection, $_POST['date']);
    $serviceType = mysqli_real_escape_string($connection, $_POST['services']);
    $time = mysqli_real_escape_string($connection, $_POST['aptmtime']);

    // Convert the 12-hour time format to 24-hour format (H:i:s)
    $formattedTime = date('H:i:s', strtotime($time));

    // Combine the selected date and time into one datetime value
    $selectedDateTime = date('Y-m-d H:i:s', strtotime("$date $formattedTime"));

    // Separate current date and time in Philippine Time
    $currentDate = date('Y-m-d', strtotime($currentDateTime));
    $currentTime = date('H:i:s', strtotime($currentDateTime));

    // Check if the selected date and time are in the past
    if ($date < $currentDate || ($date == $currentDate && $formattedTime < $currentTime)) {
        echo '<script>alert("Failed, the selected date and time are in the past. Please choose a valid date and time.");</script>';
        echo '<script>window.location.href = "./user-home.php";</script>';
        exit;
    }

    // Validate session username
    if (isset($_SESSION['Username'])) {
        $username = $_SESSION['Username'];

        // Fetch the UserId of the logged-in user
        $queryAccounts = "SELECT UserId FROM useraccounts WHERE Username = ?";
        $stmt = mysqli_prepare($connection, $queryAccounts);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $userId = $row['UserId'];

            // Check if the selected date and time are already booked
            $validTime = "SELECT * FROM appointments WHERE Date = ? AND Time = ?";
            $stmtValidTime = mysqli_prepare($connection, $validTime);
            mysqli_stmt_bind_param($stmtValidTime, "ss", $date, $formattedTime);
            mysqli_stmt_execute($stmtValidTime);
            $resultValidTime = mysqli_stmt_get_result($stmtValidTime);

            if ($resultValidTime && mysqli_num_rows($resultValidTime) > 0) {
                echo '<script>alert("Failed, Date and time already taken. Please try again.");</script>';
                echo '<script>window.location.href = "./user-home.php";</script>';
            } else {


                // Convert to 12-hour format (g:i A)
                $time12hr = date('g:i A', strtotime($formattedTime));
                // Insert appointment into the database
                $queryCreate = "INSERT INTO appointments 
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
                    $time12hr,
                    $serviceType
                );

                // Insert into history table
                $aptmquery = "INSERT INTO history 
                              (UserId, Email_Address, Phone_Number, Address, Pet_Name, Pet_Breed, Pet_Age, Date, Time, Service_Type)
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtAptm = mysqli_prepare($connection, $aptmquery);
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
                    $time12hr,
                    $serviceType
                );

                if (mysqli_stmt_execute($stmtCreate) && mysqli_stmt_execute($stmtAptm)) {
                    echo '<script>alert("Successfully submitted!");</script>';
                    echo '<script>window.location.href = "./user-home.php";</script>';
                } else {
                    echo '<script>alert("Failed to submit your appointment. Please try again.");</script>';
                }
            }
        } else {
            echo '<script>alert("User not found. Please log in again.");</script>';
        }
    } else {
        echo '<script>alert("You must be logged in to submit an appointment.");</script>';
    }
}
