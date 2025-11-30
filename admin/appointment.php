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
        appointments.Dog_Size,
        appointments.Price,
        appointments.Appointment_id,
        appointments.Status
    FROM appointments
    INNER JOIN useraccounts ON appointments.UserId = useraccounts.UserId
    ORDER BY 
        CASE 
            WHEN appointments.Status = 'Pending' THEN 1
            WHEN appointments.Status = 'Approved' THEN 2
            WHEN appointments.Status = 'Rejected' THEN 3
        END,
        appointments.Date ASC
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


// Handle appointment approval
if (isset($_POST['approve_id'])) {
    $approve_id = $_POST['approve_id'];
    
    // Get appointment details for notification
    $getApptSql = "SELECT a.*, u.Firstname, u.Lastname, u.Username 
                   FROM appointments a 
                   JOIN useraccounts u ON a.UserId = u.UserId 
                   WHERE a.Appointment_id = ?";
    $stmtGet = mysqli_prepare($connection, $getApptSql);
    mysqli_stmt_bind_param($stmtGet, "i", $approve_id);
    mysqli_stmt_execute($stmtGet);
    $resultGet = mysqli_stmt_get_result($stmtGet);
    $apptData = mysqli_fetch_assoc($resultGet);
    
    // Update appointment status
    $approveSql = "UPDATE appointments SET Status = 'Approved', Approved_Date = NOW() WHERE Appointment_id = ?";
    $stmt = mysqli_prepare($connection, $approveSql);
    mysqli_stmt_bind_param($stmt, "i", $approve_id);

    if (mysqli_stmt_execute($stmt)) {
        // Create notification for customer
        $notifMessage = "Your appointment for " . $apptData['Pet_Name'] . " on " . date('F d, Y', strtotime($apptData['Date'])) . " at " . $apptData['Time'] . " has been APPROVED!";
        $notifSql = "INSERT INTO notifications (UserId, Appointment_id, Message, Type, Created_Date) VALUES (?, ?, ?, 'approved', NOW())";
        $stmtNotif = mysqli_prepare($connection, $notifSql);
        mysqli_stmt_bind_param($stmtNotif, "iis", $apptData['UserId'], $approve_id, $notifMessage);
        mysqli_stmt_execute($stmtNotif);
        
        echo '<script>alert("Appointment approved! Customer will be notified.");</script>';
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        echo "Error approving the appointment: " . mysqli_error($connection);
    }
}

// Handle appointment rejection
if (isset($_POST['reject_id']) && isset($_POST['rejection_reason'])) {
    $reject_id = $_POST['reject_id'];
    $reason = trim($_POST['rejection_reason']);
    
    if (empty($reason)) {
        echo '<script>alert("Please provide a reason for rejection.");</script>';
    } else {
        // Get appointment details for notification
        $getApptSql = "SELECT a.*, u.Firstname, u.Lastname, u.Username 
                       FROM appointments a 
                       JOIN useraccounts u ON a.UserId = u.UserId 
                       WHERE a.Appointment_id = ?";
        $stmtGet = mysqli_prepare($connection, $getApptSql);
        mysqli_stmt_bind_param($stmtGet, "i", $reject_id);
        mysqli_stmt_execute($stmtGet);
        $resultGet = mysqli_stmt_get_result($stmtGet);
        $apptData = mysqli_fetch_assoc($resultGet);
        
        // Update appointment status
        $rejectSql = "UPDATE appointments SET Status = 'Rejected', Rejection_Reason = ? WHERE Appointment_id = ?";
        $stmt = mysqli_prepare($connection, $rejectSql);
        mysqli_stmt_bind_param($stmt, "si", $reason, $reject_id);

        if (mysqli_stmt_execute($stmt)) {
            // Create notification for customer
            $notifMessage = "Your appointment for " . $apptData['Pet_Name'] . " on " . date('F d, Y', strtotime($apptData['Date'])) . " at " . $apptData['Time'] . " has been REJECTED. Reason: " . $reason;
            $notifSql = "INSERT INTO notifications (UserId, Appointment_id, Message, Type, Created_Date) VALUES (?, ?, ?, 'rejected', NOW())";
            $stmtNotif = mysqli_prepare($connection, $notifSql);
            mysqli_stmt_bind_param($stmtNotif, "iis", $apptData['UserId'], $reject_id, $notifMessage);
            mysqli_stmt_execute($stmtNotif);
            
            echo '<script>alert("Appointment rejected. Customer will be notified.");</script>';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Error rejecting the appointment: " . mysqli_error($connection);
        }
    }
}

// Handle appointment cancellation by admin
if (isset($_POST['cancel_id']) && isset($_POST['cancellation_reason'])) {
    $cancel_id = $_POST['cancel_id'];
    $reason = trim($_POST['cancellation_reason']);
    
    if (empty($reason)) {
        echo '<script>alert("Please provide a reason for cancellation.");</script>';
    } else {
        // Get appointment details for notification
        $getApptSql = "SELECT a.*, u.Firstname, u.Lastname, u.Username 
                       FROM appointments a 
                       JOIN useraccounts u ON a.UserId = u.UserId 
                       WHERE a.Appointment_id = ?";
        $stmtGet = mysqli_prepare($connection, $getApptSql);
        mysqli_stmt_bind_param($stmtGet, "i", $cancel_id);
        mysqli_stmt_execute($stmtGet);
        $resultGet = mysqli_stmt_get_result($stmtGet);
        $apptData = mysqli_fetch_assoc($resultGet);
        
        if ($apptData) {
            // Create notification for customer
            $notifMessage = "Your appointment for " . $apptData['Pet_Name'] . " on " . date('F d, Y', strtotime($apptData['Date'])) . " at " . $apptData['Time'] . " has been CANCELLED by admin. Reason: " . $reason;
            $notifSql = "INSERT INTO notifications (UserId, Appointment_id, Message, Type, Created_Date) VALUES (?, ?, ?, 'cancelled', NOW())";
            $stmtNotif = mysqli_prepare($connection, $notifSql);
            mysqli_stmt_bind_param($stmtNotif, "iis", $apptData['UserId'], $cancel_id, $notifMessage);
            mysqli_stmt_execute($stmtNotif);
        }
        
        // Delete the appointment
        $deleteSql = "DELETE FROM appointments WHERE Appointment_id = ?";
        $stmt = mysqli_prepare($connection, $deleteSql);
        mysqli_stmt_bind_param($stmt, "i", $cancel_id);

        if (mysqli_stmt_execute($stmt)) {
            echo '<script>alert("Appointment cancelled. Customer will be notified.");</script>';
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            echo "Error canceling the appointment: " . mysqli_error($connection);
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
    <title>Admin</title>
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

        h1 {
            color: #28496b;
            font-size: 2rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            text-align: left;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        table thead tr {
            background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
            color: white;
            text-align: left;
        }

        table td {
            border: 1px solid #e9ecef;
            padding: 10px 12px;
            font-size: 12px;
        }

        table th {
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 12px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        table tbody tr:hover {
            background-color: #e9ecef;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .action-btns {
            display: flex;
            gap: 5px;
            justify-content: center;
        }

        .edit-btn, .delete-btn {
            border: none;
            width: 55px;
            height: 28px;
            font-size: 11px;
            color: white;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .edit-btn {
            background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
            box-shadow: 0 2px 8px rgba(40, 73, 107, 0.3);
        }

        .edit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 73, 107, 0.4);
        }

        .delete-btn {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }

        .delete-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            background: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 20px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-header {
            font-size: 1.8rem;
            font-weight: 700;
            color: #28496b;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .close:hover {
            color: #28496b;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            color: #28496b;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 15px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4db6e9;
            background: white;
            box-shadow: 0 0 0 4px rgba(77, 182, 233, 0.1);
        }

        .save-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .save-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(40, 73, 107, 0.4);
        }

        .status-badge {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: #ffc107;
            color: #856404;
        }

        .status-approved {
            background: #28a745;
            color: white;
        }

        .status-rejected {
            background: #dc3545;
            color: white;
        }

        .approve-btn, .reject-btn {
            border: none;
            width: 60px;
            height: 28px;
            font-size: 11px;
            color: white;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .approve-btn {
            background: linear-gradient(135deg, #28a745 0%, #218838 100%);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .approve-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
        }

        .reject-btn {
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.3);
            color: #856404;
        }

        .reject-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 193, 7, 0.4);
        }

        .cancel-btn-admin {
            border: none;
            width: 60px;
            height: 28px;
            font-size: 11px;
            color: white;
            font-family: 'Poppins', Arial, sans-serif;
            font-weight: 700;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.3);
        }

        .cancel-btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
        }

        .reject-modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
        }

        .reject-modal-content {
            background: white;
            margin: 10% auto;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .reject-modal-header {
            font-size: 1.5rem;
            font-weight: 700;
            color: #28496b;
            margin-bottom: 20px;
        }

        .reject-textarea {
            width: 100%;
            min-height: 100px;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: 'Poppins', sans-serif;
            resize: vertical;
            background: #f8f9fa;
        }

        .reject-textarea:focus {
            outline: none;
            border-color: #ffc107;
            background: white;
        }

        .reject-submit-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
            color: #856404;
            border: none;
            border-radius: 10px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 15px;
            text-transform: uppercase;
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
                    <p class="admin-name"><?php echo $_SESSION['Username']; ?></p>
                    <p class="admin-email">admin@sstails.com</p>
                </div>
            </div>
            <div>
                <form action="../logout-admin.php" method="post">
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
            <div class="side-btn-container appointment-js active">
                <div><img src="../assets/ribbon.png" class="img-btn"></div>
                <div class="side-btn">Appointment</div>
                <?php
                // Show notification badge for unread admin notifications
                $queryUnreadCount = "SELECT COUNT(*) as unread FROM admin_notifications WHERE Is_Read = 0";
                $resultUnread = mysqli_query($connection, $queryUnreadCount);
                if ($resultUnread) {
                    $unreadData = mysqli_fetch_assoc($resultUnread);
                    $unreadCount = $unreadData['unread'];
                    if ($unreadCount > 0) {
                        echo '<span style="background: #dc3545; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 700; margin-left: 8px;">' . $unreadCount . '</span>';
                    }
                }
                ?>
            </div>
            <div class="side-btn-container petowners-js">
                <div><img src="../assets/dog.png" class="img-btn"></div>
                <div class="side-btn">Pet Owners</div>
            </div>
        </div>
    </section>

    <section>
            <h1>Appointments (<?php echo $totalAppointments; ?>)</h1>
            
            <?php
            // Display admin notifications for cancelled appointments
            $queryAdminNotif = "SELECT * FROM admin_notifications WHERE Is_Read = 0 ORDER BY Created_Date DESC LIMIT 5";
            $resultAdminNotif = mysqli_query($connection, $queryAdminNotif);
            
            if ($resultAdminNotif && mysqli_num_rows($resultAdminNotif) > 0) {
                while ($notif = mysqli_fetch_assoc($resultAdminNotif)) {
                    echo '<div style="background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%); padding: 15px 20px; border-radius: 12px; margin-bottom: 15px; border-left: 5px solid #ffc107; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">';
                    echo '<span style="font-size: 1.5rem;">⚠️</span>';
                    echo '<span style="flex: 1; color: #856404; font-weight: 600; font-size: 0.95rem;">' . htmlspecialchars($notif['Message']) . '</span>';
                    echo '<button onclick="markAdminNotifRead(' . $notif['Admin_Notification_id'] . ')" style="background: rgba(0,0,0,0.1); border: none; width: 30px; height: 30px; border-radius: 50%; cursor: pointer; color: #856404; font-size: 1.2rem; transition: all 0.3s ease;">×</button>';
                    echo '</div>';
                }
            }
            ?>
            
            <br>
            <div>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Pet Name</th>
                            <th>Pet Breed</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Service</th>
                            <th>Dog Size</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($appointments as $appointment): 
                            $status = $appointment['Status'];
                            $statusClass = '';
                            if ($status == 'Pending') $statusClass = 'status-pending';
                            elseif ($status == 'Approved') $statusClass = 'status-approved';
                            elseif ($status == 'Rejected') $statusClass = 'status-rejected';
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($appointment['Appointment_id']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Firstname'] . " " . $appointment['Lastname']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Pet_Name']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Pet_Breed']); ?></td>
                                <td><?php echo date('M d, Y', strtotime($appointment['Date'])); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Time']); ?></td>
                                <td><?php echo htmlspecialchars($appointment['Service_Type']); ?></td>
                                <td><?php echo !empty($appointment['Dog_Size']) ? htmlspecialchars($appointment['Dog_Size']) : '-'; ?></td>
                                <td><?php echo !empty($appointment['Price']) ? '₱ ' . number_format($appointment['Price'], 2) : '-'; ?></td>
                                <td><span class="status-badge <?php echo $statusClass; ?>"><?php echo htmlspecialchars($status); ?></span></td>
                                <td>
                                    <div class="action-btns">
                                        <?php if ($status == 'Pending'): ?>
                                            <form method="post" style="display: inline;">
                                                <input type="hidden" name="approve_id" value="<?php echo $appointment['Appointment_id']; ?>">
                                                <button type="submit" class="approve-btn" onclick="return confirm('Approve this appointment?');">Approve</button>
                                            </form>
                                            <button type="button" class="reject-btn" onclick="openRejectModal(<?php echo $appointment['Appointment_id']; ?>)">Reject</button>
                                        <?php else: ?>
                                            <button type="button" class="cancel-btn-admin" onclick="openCancelModal(<?php echo $appointment['Appointment_id']; ?>)">Cancel</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>
    </section>

    <!-- Reject Modal -->
    <div id="rejectModal" class="reject-modal">
        <div class="reject-modal-content">
            <span class="close" onclick="closeRejectModal()">&times;</span>
            <h2 class="reject-modal-header">Reject Appointment</h2>
            <p style="color: #6c757d; margin-bottom: 20px;">Please provide a reason for rejecting this appointment:</p>
            <form method="post">
                <input type="hidden" name="reject_id" id="reject_appointment_id">
                <textarea name="rejection_reason" id="rejection_reason" class="reject-textarea" placeholder="Enter rejection reason..." required></textarea>
                <button type="submit" class="reject-submit-btn">Reject Appointment</button>
            </form>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="reject-modal">
        <div class="reject-modal-content">
            <span class="close" onclick="closeCancelModal()">&times;</span>
            <h2 class="reject-modal-header" style="color: #dc3545;">Cancel Appointment</h2>
            <p style="color: #6c757d; margin-bottom: 20px;">Please provide a reason for cancelling this appointment:</p>
            <form method="post">
                <input type="hidden" name="cancel_id" id="cancel_appointment_id">
                <textarea name="cancellation_reason" id="cancellation_reason" class="reject-textarea" placeholder="Enter cancellation reason..." required></textarea>
                <button type="submit" class="reject-submit-btn" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); color: white;">Cancel Appointment</button>
            </form>
        </div>
    </div>

    <script src="../script/adminRedirect.js"></script>
    <script>
        function openRejectModal(appointmentId) {
            document.getElementById('reject_appointment_id').value = appointmentId;
            document.getElementById('rejectModal').style.display = 'block';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
            document.getElementById('rejection_reason').value = '';
        }

        function openCancelModal(appointmentId) {
            document.getElementById('cancel_appointment_id').value = appointmentId;
            document.getElementById('cancelModal').style.display = 'block';
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').style.display = 'none';
            document.getElementById('cancellation_reason').value = '';
        }

        function markAdminNotifRead(notificationId) {
            fetch('mark-admin-notification-read.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'notification_id=' + notificationId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            });
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const rejectModal = document.getElementById('rejectModal');
            const cancelModal = document.getElementById('cancelModal');
            
            if (event.target == rejectModal) {
                closeRejectModal();
            }
            if (event.target == cancelModal) {
                closeCancelModal();
            }
        }
    </script>
</body>

</html>