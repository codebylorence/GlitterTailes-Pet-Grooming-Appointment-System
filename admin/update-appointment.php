<?php
require '../db/database.php';
require '../session-admin.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['appointment_id'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $pet_name = $_POST['pet_name'];
    $pet_breed = $_POST['pet_breed'];
    $pet_age = $_POST['pet_age'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $service_type = $_POST['service_type'];

    // Update the appointment
    $sql = "UPDATE appointments SET 
            Email_Address = ?, 
            Phone_Number = ?, 
            Address = ?, 
            Pet_Name = ?, 
            Pet_Breed = ?, 
            Pet_Age = ?, 
            Date = ?, 
            Time = ?, 
            Service_Type = ? 
            WHERE Appointment_id = ?";
    
    $stmt = mysqli_prepare($connection, $sql);
    mysqli_stmt_bind_param($stmt, "sssssssssi", 
        $email, 
        $phone, 
        $address, 
        $pet_name, 
        $pet_breed, 
        $pet_age, 
        $date, 
        $time, 
        $service_type, 
        $appointment_id
    );

    if (mysqli_stmt_execute($stmt)) {
        echo '<script>alert("Appointment updated successfully!");</script>';
        echo '<script>window.location.href = "appointment.php";</script>';
    } else {
        echo '<script>alert("Error updating appointment: ' . mysqli_error($connection) . '");</script>';
        echo '<script>window.location.href = "appointment.php";</script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connection);
} else {
    header("Location: appointment.php");
    exit();
}
?>
