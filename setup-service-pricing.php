<?php
/**
 * GlitterTails Service Pricing System Setup
 * This script adds Dog_Size and Price columns to the database
 * Run this file once in your browser: http://localhost/your-project/setup-service-pricing.php
 */

require 'db/database.php';

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Service Pricing Setup</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 30px;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #28a745;
            margin: 15px 0;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #dc3545;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            color: #0c5460;
            padding: 15px;
            border-radius: 8px;
            border-left: 5px solid #17a2b8;
            margin: 15px 0;
        }
        .step {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            font-weight: bold;
            transition: transform 0.3s ease;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🐾 GlitterTails Service Pricing Setup</h1>";

// Check if database connection exists
if (!$connection) {
    echo "<div class='error'><strong>❌ Database Connection Failed!</strong><br>";
    echo "Error: " . mysqli_connect_error() . "</div>";
    echo "</div></body></html>";
    exit();
}

echo "<div class='info'><strong>✓ Database Connected Successfully</strong></div>";

$errors = [];
$success = [];

// Step 1: Check if columns already exist in appointments table
echo "<div class='step'><strong>Step 1:</strong> Checking appointments table...</div>";

$checkAppointments = "SHOW COLUMNS FROM appointments LIKE 'Dog_Size'";
$result = mysqli_query($connection, $checkAppointments);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='info'>Column <code>Dog_Size</code> already exists in appointments table.</div>";
} else {
    // Add Dog_Size column to appointments
    $sql1 = "ALTER TABLE appointments ADD COLUMN Dog_Size VARCHAR(255) AFTER Service_Type";
    if (mysqli_query($connection, $sql1)) {
        $success[] = "Added Dog_Size column to appointments table";
        echo "<div class='success'>✓ Added <code>Dog_Size</code> column to appointments table</div>";
    } else {
        $errors[] = "Failed to add Dog_Size to appointments: " . mysqli_error($connection);
        echo "<div class='error'>❌ Failed to add Dog_Size column: " . mysqli_error($connection) . "</div>";
    }
}

// Check Price column in appointments
$checkPrice = "SHOW COLUMNS FROM appointments LIKE 'Price'";
$result = mysqli_query($connection, $checkPrice);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='info'>Column <code>Price</code> already exists in appointments table.</div>";
} else {
    // Add Price column to appointments
    $sql2 = "ALTER TABLE appointments ADD COLUMN Price DECIMAL(10,2) AFTER Dog_Size";
    if (mysqli_query($connection, $sql2)) {
        $success[] = "Added Price column to appointments table";
        echo "<div class='success'>✓ Added <code>Price</code> column to appointments table</div>";
    } else {
        $errors[] = "Failed to add Price to appointments: " . mysqli_error($connection);
        echo "<div class='error'>❌ Failed to add Price column: " . mysqli_error($connection) . "</div>";
    }
}

// Step 2: Check if columns already exist in history table
echo "<div class='step'><strong>Step 2:</strong> Checking history table...</div>";

$checkHistory = "SHOW COLUMNS FROM history LIKE 'Dog_Size'";
$result = mysqli_query($connection, $checkHistory);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='info'>Column <code>Dog_Size</code> already exists in history table.</div>";
} else {
    // Add Dog_Size column to history
    $sql3 = "ALTER TABLE history ADD COLUMN Dog_Size VARCHAR(255) AFTER Service_Type";
    if (mysqli_query($connection, $sql3)) {
        $success[] = "Added Dog_Size column to history table";
        echo "<div class='success'>✓ Added <code>Dog_Size</code> column to history table</div>";
    } else {
        $errors[] = "Failed to add Dog_Size to history: " . mysqli_error($connection);
        echo "<div class='error'>❌ Failed to add Dog_Size column: " . mysqli_error($connection) . "</div>";
    }
}

// Check Price column in history
$checkPriceHistory = "SHOW COLUMNS FROM history LIKE 'Price'";
$result = mysqli_query($connection, $checkPriceHistory);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='info'>Column <code>Price</code> already exists in history table.</div>";
} else {
    // Add Price column to history
    $sql4 = "ALTER TABLE history ADD COLUMN Price DECIMAL(10,2) AFTER Dog_Size";
    if (mysqli_query($connection, $sql4)) {
        $success[] = "Added Price column to history table";
        echo "<div class='success'>✓ Added <code>Price</code> column to history table</div>";
    } else {
        $errors[] = "Failed to add Price to history: " . mysqli_error($connection);
        echo "<div class='error'>❌ Failed to add Price column: " . mysqli_error($connection) . "</div>";
    }
}

// Step 3: Create admin notifications table
echo "<div class='step'><strong>Step 3:</strong> Creating admin notifications table...</div>";

$checkAdminNotif = "SHOW TABLES LIKE 'admin_notifications'";
$result = mysqli_query($connection, $checkAdminNotif);

if (mysqli_num_rows($result) > 0) {
    echo "<div class='info'>Table <code>admin_notifications</code> already exists.</div>";
} else {
    $sql5 = "CREATE TABLE admin_notifications (
        Admin_Notification_id INT AUTO_INCREMENT PRIMARY KEY,
        Appointment_id INT NULL,
        Message TEXT NOT NULL,
        Type VARCHAR(20) NOT NULL,
        Is_Read TINYINT(1) DEFAULT 0,
        Created_Date DATETIME NOT NULL,
        INDEX idx_is_read (Is_Read),
        INDEX idx_created_date (Created_Date)
    )";
    
    if (mysqli_query($connection, $sql5)) {
        $success[] = "Created admin_notifications table";
        echo "<div class='success'>✓ Created <code>admin_notifications</code> table</div>";
    } else {
        $errors[] = "Failed to create admin_notifications table: " . mysqli_error($connection);
        echo "<div class='error'>❌ Failed to create admin_notifications table: " . mysqli_error($connection) . "</div>";
    }
}

// Summary
echo "<hr style='margin: 30px 0; border: none; border-top: 2px solid #e9ecef;'>";
echo "<h2>📊 Setup Summary</h2>";

if (count($success) > 0) {
    echo "<div class='success'><strong>✓ Successful Operations:</strong><ul>";
    foreach ($success as $msg) {
        echo "<li>$msg</li>";
    }
    echo "</ul></div>";
}

if (count($errors) > 0) {
    echo "<div class='error'><strong>❌ Errors:</strong><ul>";
    foreach ($errors as $msg) {
        echo "<li>$msg</li>";
    }
    echo "</ul></div>";
} else {
    echo "<div class='success'><strong>🎉 Setup Completed Successfully!</strong><br>";
    echo "Your database is now ready to use the service pricing system.</div>";
    echo "<a href='index.php' class='btn'>Go to Home Page</a>";
}

mysqli_close($connection);

echo "
    </div>
</body>
</html>";
?>
