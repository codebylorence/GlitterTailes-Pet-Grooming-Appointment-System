<?php
require 'db/database.php';

echo "<h2>Setting up Appointment Approval System...</h2>";

// Check if Status column exists
$checkStatus = mysqli_query($connection, "SHOW COLUMNS FROM appointments LIKE 'Status'");
if (mysqli_num_rows($checkStatus) == 0) {
    $sql1 = "ALTER TABLE appointments ADD COLUMN Status VARCHAR(20) DEFAULT 'Pending'";
    if (mysqli_query($connection, $sql1)) {
        echo "<p style='color: green;'>✓ Added Status column to appointments table</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding Status: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Status column already exists</p>";
}

// Check if Approved_Date column exists
$checkApproved = mysqli_query($connection, "SHOW COLUMNS FROM appointments LIKE 'Approved_Date'");
if (mysqli_num_rows($checkApproved) == 0) {
    $sql2 = "ALTER TABLE appointments ADD COLUMN Approved_Date DATETIME NULL";
    if (mysqli_query($connection, $sql2)) {
        echo "<p style='color: green;'>✓ Added Approved_Date column</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding Approved_Date: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Approved_Date column already exists</p>";
}

// Check if Rejection_Reason column exists
$checkRejection = mysqli_query($connection, "SHOW COLUMNS FROM appointments LIKE 'Rejection_Reason'");
if (mysqli_num_rows($checkRejection) == 0) {
    $sql3 = "ALTER TABLE appointments ADD COLUMN Rejection_Reason TEXT NULL";
    if (mysqli_query($connection, $sql3)) {
        echo "<p style='color: green;'>✓ Added Rejection_Reason column</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding Rejection_Reason: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Rejection_Reason column already exists</p>";
}

// Update existing appointments to 'Approved' status
$sql4 = "UPDATE appointments SET Status = 'Approved' WHERE Status IS NULL OR Status = ''";

if (mysqli_query($connection, $sql4)) {
    $affected = mysqli_affected_rows($connection);
    echo "<p style='color: green;'>✓ Updated $affected existing appointments to 'Approved' status</p>";
} else {
    echo "<p style='color: red;'>✗ Error updating appointments: " . mysqli_error($connection) . "</p>";
}

// Check if Status column exists in history table
$checkHistoryStatus = mysqli_query($connection, "SHOW COLUMNS FROM history LIKE 'Status'");
if (mysqli_num_rows($checkHistoryStatus) == 0) {
    $sql5 = "ALTER TABLE history ADD COLUMN Status VARCHAR(20) DEFAULT 'Completed'";
    if (mysqli_query($connection, $sql5)) {
        echo "<p style='color: green;'>✓ Added Status column to history table</p>";
    } else {
        echo "<p style='color: red;'>✗ Error adding Status to history: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Status column already exists in history table</p>";
}

// Check if notifications table exists
$checkTable = mysqli_query($connection, "SHOW TABLES LIKE 'notifications'");
if (mysqli_num_rows($checkTable) == 0) {
    $sql6 = "CREATE TABLE notifications (
        Notification_id INT AUTO_INCREMENT PRIMARY KEY,
        UserId INT NOT NULL,
        Appointment_id INT NULL,
        Message TEXT NOT NULL,
        Type VARCHAR(20) NOT NULL,
        Is_Read TINYINT(1) DEFAULT 0,
        Created_Date DATETIME NOT NULL,
        FOREIGN KEY (UserId) REFERENCES useraccounts(UserId) ON DELETE CASCADE,
        FOREIGN KEY (Appointment_id) REFERENCES appointments(Appointment_id) ON DELETE SET NULL
    )";
    
    if (mysqli_query($connection, $sql6)) {
        echo "<p style='color: green;'>✓ Created notifications table</p>";
    } else {
        echo "<p style='color: red;'>✗ Error creating notifications table: " . mysqli_error($connection) . "</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠ Notifications table already exists</p>";
}

mysqli_close($connection);

echo "<br><h3 style='color: green;'>✓ Setup Complete!</h3>";
echo "<p>You can now use the appointment approval system with notifications.</p>";
echo "<p><strong>Important:</strong> Delete this file (setup-approval-system.php) after running it for security.</p>";
echo "<br><a href='index.php' style='padding: 10px 20px; background: #28496b; color: white; text-decoration: none; border-radius: 5px;'>Go to Home Page</a>";
?>
