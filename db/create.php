<?php 
require('../db/database.php');

if (isset($_POST['create'])) {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $cpassword = $_POST['confirmpassword'];

    // Check if username already exists
    $queryValidate = "SELECT * FROM useraccounts WHERE Username = ?";
    $stmtValidate = $connection->prepare($queryValidate);
    $stmtValidate->bind_param("s", $username);
    $stmtValidate->execute();
    $resultValidate = $stmtValidate->get_result();

    if ($resultValidate->num_rows > 0) {
        echo '<script>alert("Username already exists!")</script>';
        echo '<script>window.location.href = "../index.php"</script>';
    } else {
        // Check if passwords match
        if ($password === $cpassword) {
            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user with hashed password
            $queryCreate = "INSERT INTO useraccounts (Firstname, Lastname, Username, Password) VALUES (?, ?, ?, ?)";
            $stmtCreate = $connection->prepare($queryCreate);
            $stmtCreate->bind_param("ssss", $firstname, $lastname, $username, $hashedPassword);

            if ($stmtCreate->execute()) {
                echo '<script>alert("Successfully created!")</script>';
                echo '<script>window.location.href = "../index.php"</script>';
            } else {
                echo '<script>alert("An error occurred while creating your account. Please try again.")</script>';
                echo '<script>window.location.href = "../index.php"</script>';
                echo "Error: " . $stmtCreate->error; // Show MySQL error
            }
        } else {
            echo '<script>alert("Your confirm password does not match your password.")</script>';
            echo '<script>window.location.href = "../index.php"</script>';
        }
    }
}
?>
