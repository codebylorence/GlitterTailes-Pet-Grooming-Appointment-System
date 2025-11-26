<?php
require '../db/database.php';
require '../session-admin.php';


$sql = "
    SELECT 
        SQL_CALC_FOUND_ROWS
        appointments.UserId, 
        useraccounts.Firstname, 
        useraccounts.Lastname, 
        appointments.Email_Address, 
        appointments.Phone_Number, 
        appointments.Address, 
        appointments.Pet_Name, 
        appointments.Pet_Breed, 
        appointments.Date, 
        appointments.Pet_Age, 
        appointments.Time, 
        appointments.Service_Type, 
        appointments.Appointment_id 
    FROM appointments
    INNER JOIN useraccounts ON appointments.UserId = useraccounts.UserId
";


$result = mysqli_query($connection, $sql);


$totalResult = mysqli_query($connection, "SELECT FOUND_ROWS() AS total");
$totalAppointments = mysqli_fetch_assoc($totalResult)['total'];

$appointments = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $appointments[] = $row;
    }
} else {
    echo "No appointments found or query error: " . mysqli_error($connection);
}


if (isset($_GET['cancel_id'])) {
    $cancel_id = $_GET['cancel_id'];
    $deleteSql = "DELETE FROM appointments WHERE Appointment_id = ?";
    $stmt = mysqli_prepare($connection, $deleteSql);
    mysqli_stmt_bind_param($stmt, "i", $cancel_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: " . $_SERVER['PHP_SELF']); // 
        exit;
    } else {
        echo "Error canceling the appointment: " . mysqli_error($connection);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/admin-sidebar.css">
    <title>Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            margin-left: 280px;
            margin-right: 40px;
            margin-top: 20px;
            background-color: #EBEBEB;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 16px;
            text-align: left;
        }

        table thead tr {
            background-color: #4db6e9;
            color: #003366;
            text-align: left;
        }


        table td {
            border: 1px solid #dddddd;
            padding: 8px 12px;
        }

        table th {
            border: 1px solid #dddddd;
            padding: 8px 12px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f3f3f3;
        }

        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .delete-btn {
            border: none;
            width: 60px;
            height: 26px;
            font-size: 13px;
            color: white;
            font-family: Arial, Helvetica, sans-serif;
            font-weight: bold;
            border-radius: 3px;
            cursor: pointer;
        }

        .delete-btn {
            background-color: #D11A2A;
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
                        <p class="admin-email">admin@sstails.com</p>
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
                <div class="side-btn-container dashboard-js">
                    <div><img src="../assets/menu.png" class="img-btn"></div>
                    <div class="side-btn">Dashboard</div>
                </div>
                <div class="side-btn-container schedule-js">
                    <div><img src="../assets/event.png" class="img-btn"></div>
                    <div class="side-btn">Schedule</div>
                </div>
                <div class="side-btn-container appointment-js">
                    <div><img src="../assets/ribbon.png" class="img-btn"></div>
                    <div class="side-btn">Appointment</div>
                </div>
                <div class="side-btn-container petowners-js">
                    <div><img src="../assets/dog.png" class="img-btn"></div>
                    <div class="side-btn">Pet Owners</div>
                </div>
            </div>
        </section>
        <section>
            <h1>Appointments (<?php echo $totalAppointments; ?>)</h1>
            <div>
            </div>
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>Appointment ID</th>
                            <th>Full Name</th>
                            <th>Email Address</th>
                            <th>Phone Number</th>
                            <th>Address</th>
                            <th>Pet Name</th>
                            <th>Pet Breed</th>
                            <th>Appointment Date</th>
                            <th>Pet Age</th>
                            <th>Appointment Time</th>
                            <th>Service Type</th>
                            <th>Events</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['Appointment_id']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Firstname'] . " ");
                                    echo htmlspecialchars($appointment['Lastname']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Email_Address']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Phone_Number']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Address']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Pet_Name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Pet_Breed']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Date']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Pet_Age']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Time']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Service_Type']); ?></td>
                                <td>

                                    <a href="?cancel_id=<?php echo $appointment['Appointment_id']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">
                                        <button type="button" class="delete-btn">Remove</button>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
        </section>
        <script src="../script/adminRedirect.js"></script>
    </div>
</body>

</html>