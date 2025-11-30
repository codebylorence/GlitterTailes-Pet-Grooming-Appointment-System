<?php
require '../db/database.php';
require '../session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $pet_name = $_POST['pet_name'];
    $pet_breed = $_POST['pet_breed'];
    $pet_age = $_POST['pet_age'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $service_type = $_POST['service_type'];
    
    // Handle dog size and price
    $dogSize = '';
    $price = 0;
    
    // Service pricing
    $servicePricing = [
        'Full Grooming' => [
            'Small Dog (10kg Below)' => 500,
            'Medium Dog (11kg - 20kg)' => 600,
            'Large Dog (21kg - 30kg)' => 700
        ],
        'Basic Grooming' => [
            'Small Dog (10kg Below)' => 300,
            'Medium Dog (11kg - 20kg)' => 400,
            'Large Dog (21kg - 30kg)' => 500
        ],
        'Individual Grooming' => [
            'Nail Trim and Filing' => 100,
            'Teeth Brushing' => 150,
            'Facial Trimming' => 150
        ]
    ];
    
    // Process dog size selection
    if ($service_type === 'Individual Grooming') {
        // Multiple selections for individual grooming
        if (isset($_POST['dog_size']) && is_array($_POST['dog_size'])) {
            $selectedServices = $_POST['dog_size'];
            $dogSize = implode(', ', array_map(function($s) use ($connection) {
                return mysqli_real_escape_string($connection, $s);
            }, $selectedServices));
            
            // Calculate total price
            foreach ($selectedServices as $service) {
                if (isset($servicePricing['Individual Grooming'][$service])) {
                    $price += $servicePricing['Individual Grooming'][$service];
                }
            }
        }
    } else {
        // Single selection for Full/Basic Grooming
        if (isset($_POST['dog_size'])) {
            $dogSize = mysqli_real_escape_string($connection, $_POST['dog_size']);
            if (isset($servicePricing[$service_type][$dogSize])) {
                $price = $servicePricing[$service_type][$dogSize];
            }
        }
    }

    // Verify the appointment belongs to the logged-in user
    $username = $_SESSION['Username'];
    $queryUserId = "SELECT UserId FROM useraccounts WHERE Username = ?";
    $stmtUserId = mysqli_prepare($connection, $queryUserId);
    mysqli_stmt_bind_param($stmtUserId, "s", $username);
    mysqli_stmt_execute($stmtUserId);
    $resultUserId = mysqli_stmt_get_result($stmtUserId);
    
    if ($row = mysqli_fetch_assoc($resultUserId)) {
        $userId = $row['UserId'];

        // Update the appointment
        $sql = "UPDATE appointments SET 
                Email_Address = ?, 
                Phone_Number = ?, 
                Pet_Name = ?, 
                Pet_Breed = ?, 
                Pet_Age = ?, 
                Date = ?, 
                Time = ?, 
                Service_Type = ?,
                Dog_Size = ?,
                Price = ?
                WHERE Appointment_id = ? AND UserId = ?";
        
        $stmt = mysqli_prepare($connection, $sql);
        mysqli_stmt_bind_param($stmt, "sssssssssdii", 
            $email, 
            $phone, 
            $pet_name, 
            $pet_breed, 
            $pet_age, 
            $date, 
            $time, 
            $service_type,
            $dogSize,
            $price,
            $appointment_id,
            $userId
        );

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("Booking updated successfully!");</script>';
            echo '<script>window.location.href = "user-bookings.php";</script>';
        } else {
            echo '<script>alert("Error updating booking. Please try again.");</script>';
            echo '<script>window.location.href = "user-bookings.php";</script>';
        }

        mysqli_stmt_close($stmt);
    }
    
    mysqli_close($connection);
} else {
    header("Location: user-bookings.php");
    exit();
}
?>
