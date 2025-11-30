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
    
    // Get appointment details before deleting
    $queryGetAppt = "SELECT a.*, u.Firstname, u.Lastname 
                     FROM appointments a 
                     JOIN useraccounts u ON a.UserId = u.UserId 
                     WHERE a.Appointment_id = ?";
    $stmtGetAppt = mysqli_prepare($connection, $queryGetAppt);
    mysqli_stmt_bind_param($stmtGetAppt, "i", $removeId);
    mysqli_stmt_execute($stmtGetAppt);
    $resultAppt = mysqli_stmt_get_result($stmtGetAppt);
    $apptData = mysqli_fetch_assoc($resultAppt);
    
    if ($apptData) {
        // Create notification message for admin
        $customerName = $apptData['Firstname'] . ' ' . $apptData['Lastname'];
        $petName = $apptData['Pet_Name'];
        $apptDate = date('F d, Y', strtotime($apptData['Date']));
        $apptTime = $apptData['Time'];
        $serviceType = $apptData['Service_Type'];
        
        $notifMessage = "Customer $customerName cancelled appointment for $petName ($serviceType) scheduled on $apptDate at $apptTime.";
        
        // Insert notification for admin (we'll use a simple log table)
        $queryNotif = "INSERT INTO admin_notifications (Appointment_id, Message, Type, Created_Date) 
                       VALUES (?, ?, 'cancelled', NOW())";
        $stmtNotif = mysqli_prepare($connection, $queryNotif);
        mysqli_stmt_bind_param($stmtNotif, "is", $removeId, $notifMessage);
        mysqli_stmt_execute($stmtNotif);
    }

    // Delete the appointment
    $queryRemove = "DELETE FROM appointments WHERE Appointment_id = ?";
    $stmtRemove = mysqli_prepare($connection, $queryRemove);
    mysqli_stmt_bind_param($stmtRemove, "i", $removeId);
    mysqli_stmt_execute($stmtRemove);

    echo '<script>alert("Appointment cancelled successfully!");</script>';
    echo '<script>window.location.href = "./user-bookings.php";</script>';
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <title>My Bookings</title>
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

    .aptm-container {
      background: white;
      border-radius: 12px;
      padding: 0;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
    }

    .aptm-container:hover {
      box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    }

    .booking-header {
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      padding: 12px 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .booking-id {
      color: white;
      font-size: 0.9rem;
      font-weight: 700;
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .booking-id::before {
      content: "📋";
      font-size: 1rem;
    }

    .booking-status {
      padding: 4px 12px;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
    }

    .booking-status.status-pending {
      background: #ffc107;
      color: #856404;
    }

    .booking-status.status-approved {
      background: rgba(255, 255, 255, 0.25);
      color: white;
    }

    .booking-status.status-rejected {
      background: #dc3545;
      color: white;
    }

    .booking-body {
      padding: 20px;
    }

    .booking-row {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 15px 20px;
      margin-bottom: 15px;
    }

    .booking-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    .booking-icon {
      font-size: 1rem;
      min-width: 20px;
    }

    .booking-content {
      flex: 1;
      min-width: 0;
    }

    .booking-label {
      font-size: 0.7rem;
      color: #6c757d;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.3px;
      margin-bottom: 2px;
    }

    .booking-value {
      font-size: 0.9rem;
      color: #28496b;
      font-weight: 600;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .booking-value.highlight {
      color: #4db6e9;
      font-size: 0.95rem;
    }

    .service-badge {
      display: inline-block;
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      color: white;
      padding: 5px 15px;
      border-radius: 15px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .booking-actions {
      display: flex;
      gap: 10px;
      margin-top: 15px;
    }

    .edit-btn, .cancel-btn {
      flex: 1;
      padding: 10px;
      border: none;
      border-radius: 8px;
      font-weight: 700;
      font-size: 0.85rem;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }

    .edit-btn {
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(40, 73, 107, 0.3);
    }

    .edit-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(40, 73, 107, 0.4);
    }

    .cancel-btn {
      background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
      color: white;
      box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
    }

    .cancel-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
    }

    .aptms {
      display: grid;
      gap: 20px;
      max-width: 900px;
      margin: 0 auto;
    }

    .aptms h1 {
      color: #28496b;
      font-size: 2rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
      margin-bottom: 20px;
      text-align: center;
    }

    .no-bookings {
      text-align: center;
      padding: 40px 20px;
      background: white;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    }

    .no-bookings::before {
      content: "📅";
      font-size: 3rem;
      display: block;
      margin-bottom: 15px;
    }

    .no-bookings p {
      font-size: 1rem;
      color: #6c757d;
    }

    /* Edit Modal */
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
      padding: 25px;
      border-radius: 15px;
      width: 90%;
      max-width: 500px;
      max-height: 85vh;
      overflow-y: auto;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .modal-content::-webkit-scrollbar {
      width: 8px;
    }

    .modal-content::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .modal-content::-webkit-scrollbar-thumb {
      background: #4db6e9;
      border-radius: 10px;
    }

    .modal-content::-webkit-scrollbar-thumb:hover {
      background: #28496b;
    }

    .modal-header {
      font-size: 1.5rem;
      font-weight: 700;
      color: #28496b;
      margin-bottom: 20px;
      text-transform: uppercase;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 24px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover {
      color: #28496b;
    }

    .form-group {
      margin-bottom: 12px;
    }

    .form-group label {
      display: block;
      font-weight: 600;
      color: #28496b;
      margin-bottom: 5px;
      font-size: 0.85rem;
    }

    .form-group input,
    .form-group select {
      width: 100%;
      padding: 10px;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      font-size: 0.9rem;
      background: #f8f9fa;
    }

    .form-group input:focus,
    .form-group select:focus {
      outline: none;
      border-color: #4db6e9;
      background: white;
    }

    .save-btn {
      width: 100%;
      padding: 12px;
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      color: white;
      border: none;
      border-radius: 10px;
      font-weight: 700;
      cursor: pointer;
      text-transform: uppercase;
      margin-top: 10px;
    }

    .notification-banner {
      display: flex;
      align-items: center;
      gap: 15px;
      padding: 15px 20px;
      border-radius: 12px;
      margin-bottom: 20px;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
      animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
      from {
        opacity: 0;
        transform: translateY(-20px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    .notif-approved {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      border-left: 5px solid #28a745;
    }

    .notif-rejected {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      border-left: 5px solid #dc3545;
    }

    .notif-icon {
      font-size: 1.5rem;
    }

    .notif-message {
      flex: 1;
      color: #333;
      font-weight: 600;
      font-size: 0.95rem;
    }

    .notif-close {
      background: rgba(0, 0, 0, 0.1);
      border: none;
      width: 30px;
      height: 30px;
      border-radius: 50%;
      font-size: 1.5rem;
      cursor: pointer;
      color: #333;
      transition: all 0.3s ease;
    }

    .notif-close:hover {
      background: rgba(0, 0, 0, 0.2);
      transform: rotate(90deg);
    }

    @media (max-width: 768px) {
      .booking-row {
        grid-template-columns: 1fr;
      }

      .notification-banner {
        flex-direction: column;
        text-align: center;
      }
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
      <div class="side-btn-container bookSched-js">
        <div><img src="../assets/event.png" class="img-btn"></div>
        <div class="side-btn">Schedule</div>
      </div>
      <div class="side-btn-container bookings-js active">
        <div><img src="../assets/ribbon.png" class="img-btn"></div>
        <div class="side-btn">My Bookings</div>
        <?php
        // Check for unread notifications
        if (isset($_SESSION['Username'])) {
          $username = $_SESSION['Username'];
          $queryUserId = "SELECT UserId FROM useraccounts WHERE Username = ?";
          $stmtUserId = mysqli_prepare($connection, $queryUserId);
          mysqli_stmt_bind_param($stmtUserId, "s", $username);
          mysqli_stmt_execute($stmtUserId);
          $resultUserId = mysqli_stmt_get_result($stmtUserId);
          if ($row = mysqli_fetch_assoc($resultUserId)) {
              $userId = $row['UserId'];
              $queryNotif = "SELECT COUNT(*) as unread FROM notifications WHERE UserId = ? AND Is_Read = 0";
              $stmtNotif = mysqli_prepare($connection, $queryNotif);
              mysqli_stmt_bind_param($stmtNotif, "i", $userId);
              mysqli_stmt_execute($stmtNotif);
              $resultNotif = mysqli_stmt_get_result($stmtNotif);
              $notifData = mysqli_fetch_assoc($resultNotif);
              $unreadCount = $notifData['unread'];
              if ($unreadCount > 0) {
                  echo '<span style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 700; margin-left: 8px; position: absolute; right: 15px;">' . $unreadCount . '</span>';
              }
          }
        }
        ?>
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

          // Fetch and display unread notifications
          $queryNotifications = "SELECT * FROM notifications WHERE UserId = ? AND Is_Read = 0 ORDER BY Created_Date DESC";
          $stmtNotif = mysqli_prepare($connection, $queryNotifications);
          mysqli_stmt_bind_param($stmtNotif, "i", $userId);
          mysqli_stmt_execute($stmtNotif);
          $resultNotif = mysqli_stmt_get_result($stmtNotif);

          if ($resultNotif && mysqli_num_rows($resultNotif) > 0) {
            while ($notif = mysqli_fetch_assoc($resultNotif)) {
              $notifClass = ($notif['Type'] == 'approved') ? 'notif-approved' : 'notif-rejected';
              $notifIcon = ($notif['Type'] == 'approved') ? '✅' : '❌';
              echo '<div class="notification-banner ' . $notifClass . '">';
              echo '<span class="notif-icon">' . $notifIcon . '</span>';
              echo '<span class="notif-message">' . htmlspecialchars($notif['Message']) . '</span>';
              echo '<button class="notif-close" onclick="markAsRead(' . $notif['Notification_id'] . ')">×</button>';
              echo '</div>';
            }
          }
        }

        // Reset result pointer
        mysqli_data_seek($resultUserId, 0);

        if ($row = mysqli_fetch_assoc($resultUserId)) {
          $userId = $row['UserId'];

          // Fetch appointments for the logged-in user
          $query = "SELECT Appointment_id, Email_Address, Phone_Number, Pet_Name, Pet_Breed, Pet_Age, Date, Time, Service_Type, Dog_Size, Price, Status, Rejection_Reason 
                FROM appointments 
                WHERE UserId = ?
                ORDER BY 
                    CASE 
                        WHEN Status = 'Pending' THEN 1
                        WHEN Status = 'Approved' THEN 2
                        WHEN Status = 'Rejected' THEN 3
                    END,
                    Date ASC";
          $stmt = mysqli_prepare($connection, $query);
          mysqli_stmt_bind_param($stmt, "i", $userId);
          mysqli_stmt_execute($stmt);
          $result = mysqli_stmt_get_result($stmt);

          // Display the appointments
          if ($result && mysqli_num_rows($result) > 0) {
            while ($aptmInfo = mysqli_fetch_assoc($result)) { 
              $status = $aptmInfo['Status'];
              $statusClass = '';
              $statusText = $status;
              if ($status == 'Pending') {
                $statusClass = 'status-pending';
                $statusText = 'Pending Approval';
              } elseif ($status == 'Approved') {
                $statusClass = 'status-approved';
                $statusText = 'Confirmed';
              } elseif ($status == 'Rejected') {
                $statusClass = 'status-rejected';
                $statusText = 'Rejected';
              }
            ?>
              <div class="aptm-container">
                <div class="booking-header">
                  <div class="booking-id">Booking #<?php echo htmlspecialchars($aptmInfo['Appointment_id']); ?></div>
                  <div class="booking-status <?php echo $statusClass; ?>"><?php echo $statusText; ?></div>
                </div>
                
                <div class="booking-body">
                  <div class="booking-row">
                    <div class="booking-item">
                      <span class="booking-icon">📅</span>
                      <div class="booking-content">
                        <div class="booking-label">Appointment Date</div>
                        <div class="booking-value highlight"><?php echo date('M d, Y', strtotime($aptmInfo['Date'])); ?></div>
                      </div>
                    </div>
                    
                    <div class="booking-item">
                      <span class="booking-icon">🕐</span>
                      <div class="booking-content">
                        <div class="booking-label">Time</div>
                        <div class="booking-value highlight"><?php echo htmlspecialchars($aptmInfo['Time']); ?></div>
                      </div>
                    </div>
                    
                    <div class="booking-item">
                      <span class="booking-icon">🐾</span>
                      <div class="booking-content">
                        <div class="booking-label">Pet Name</div>
                        <div class="booking-value"><?php echo htmlspecialchars($aptmInfo['Pet_Name']); ?></div>
                      </div>
                    </div>
                    
                    <div class="booking-item">
                      <span class="booking-icon">🐕</span>
                      <div class="booking-content">
                        <div class="booking-label">Pet Breed</div>
                        <div class="booking-value"><?php echo htmlspecialchars($aptmInfo['Pet_Breed']); ?></div>
                      </div>
                    </div>
                    
                    <div class="booking-item">
                      <span class="booking-icon">📞</span>
                      <div class="booking-content">
                        <div class="booking-label">Phone</div>
                        <div class="booking-value"><?php echo htmlspecialchars($aptmInfo['Phone_Number']); ?></div>
                      </div>
                    </div>
                    
                    <div class="booking-item">
                      <span class="booking-icon">✨</span>
                      <div class="booking-content">
                        <div class="booking-label">Service</div>
                        <span class="service-badge"><?php echo htmlspecialchars($aptmInfo['Service_Type']); ?></span>
                      </div>
                    </div>
                    
                    <?php if (!empty($aptmInfo['Dog_Size'])): ?>
                    <div class="booking-item">
                      <span class="booking-icon">📏</span>
                      <div class="booking-content">
                        <div class="booking-label">Dog Size / Service</div>
                        <div class="booking-value"><?php echo htmlspecialchars($aptmInfo['Dog_Size']); ?></div>
                      </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($aptmInfo['Price'])): ?>
                    <div class="booking-item">
                      <span class="booking-icon">💰</span>
                      <div class="booking-content">
                        <div class="booking-label">Price</div>
                        <div class="booking-value highlight">₱ <?php echo number_format($aptmInfo['Price'], 2); ?></div>
                      </div>
                    </div>
                    <?php endif; ?>
                  </div>
                  
                  <?php if ($status == 'Rejected' && !empty($aptmInfo['Rejection_Reason'])): ?>
                    <div style="background: #fff3cd; padding: 10px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #ffc107;">
                      <strong style="color: #856404;">Rejection Reason:</strong>
                      <p style="color: #856404; margin: 5px 0 0 0; font-size: 0.85rem;"><?php echo htmlspecialchars($aptmInfo['Rejection_Reason']); ?></p>
                    </div>
                  <?php endif; ?>

                  <div class="booking-actions">
                    <?php if ($status == 'Pending' || $status == 'Approved'): ?>
                      <button type="button" class="edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($aptmInfo)); ?>)">Edit</button>
                    <?php endif; ?>
                    <form action="./user-bookings.php" method="post" style="flex: 1; margin: 0;">
                      <input type="submit" name="remove" value="<?php echo ($status == 'Rejected') ? 'Remove' : 'Cancel'; ?>" class="cancel-btn" onclick="return confirm('Are you sure you want to <?php echo ($status == 'Rejected') ? 'remove' : 'cancel'; ?> this appointment?');">
                      <input type="hidden" name="removeaptm" value="<?php echo $aptmInfo['Appointment_id']; ?>">
                    </form>
                  </div>
                </div>
              </div>
            <?php }
          } else {
            echo '<div class="no-bookings"><p>No appointments found. Book your first grooming session today!</p></div>';
          }
        } else {
          echo "<p>Error: Unable to fetch user information. Please try again.</p>";
        }
      } else {
        echo "<p>Error: You must be logged in to view your appointments.</p>";
      }
      ?>
  </section>

  <!-- Edit Modal -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeEditModal()">&times;</span>
      <h2 class="modal-header">Edit Booking</h2>
      <form id="editForm" method="post" action="update-booking.php">
        <input type="hidden" name="appointment_id" id="edit_appointment_id">
        
        <div class="form-group">
          <label>Email Address</label>
          <input type="email" name="email" id="edit_email" required>
        </div>

        <div class="form-group">
          <label>Phone Number</label>
          <input type="text" name="phone" id="edit_phone" required>
        </div>

        <div class="form-group">
          <label>Pet Name</label>
          <input type="text" name="pet_name" id="edit_pet_name" required>
        </div>

        <div class="form-group">
          <label>Pet Breed</label>
          <input type="text" name="pet_breed" id="edit_pet_breed" required>
        </div>

        <div class="form-group">
          <label>Pet Age</label>
          <input type="text" name="pet_age" id="edit_pet_age" required>
        </div>

        <div class="form-group">
          <label>Appointment Date</label>
          <input type="date" name="date" id="edit_date" required>
        </div>

        <div class="form-group">
          <label>Appointment Time</label>
          <select name="time" id="edit_time" required>
            <option value="8:00 AM">8:00 AM</option>
            <option value="1:00 PM">1:00 PM</option>
            <option value="4:00 PM">4:00 PM</option>
            <option value="7:00 PM">7:00 PM</option>
          </select>
        </div>

        <div class="form-group">
          <label>Service Type</label>
          <select name="service_type" id="edit_service_type" required>
            <option value="Full Grooming">Full Grooming</option>
            <option value="Basic Grooming">Basic Grooming</option>
            <option value="Individual Grooming">Individual Grooming</option>
          </select>
        </div>

        <!-- Dog Size Selection (Hidden by default) -->
        <div class="form-group" id="edit_dogSizeContainer" style="display: none;">
          <label>Select Dog Size</label>
          <div id="edit_dogSizeOptions" style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
            <!-- Options will be populated by JavaScript -->
          </div>
        </div>

        <!-- Individual Services Selection (Hidden by default) -->
        <div class="form-group" id="edit_individualServicesContainer" style="display: none;">
          <label>Select Services</label>
          <div id="edit_individualServiceOptions" style="display: flex; flex-direction: column; gap: 10px; margin-top: 8px;">
            <!-- Options will be populated by JavaScript -->
          </div>
        </div>

        <button type="submit" class="save-btn">Save Changes</button>
      </form>
    </div>
  </div>

  <script src="../script/userRedirect.js"></script>
  <script>
    // Service pricing data
    const servicePricing = {
      'Full Grooming': [
        { size: 'Small Dog (10kg Below)', price: 500 },
        { size: 'Medium Dog (11kg - 20kg)', price: 600 },
        { size: 'Large Dog (21kg - 30kg)', price: 700 }
      ],
      'Basic Grooming': [
        { size: 'Small Dog (10kg Below)', price: 300 },
        { size: 'Medium Dog (11kg - 20kg)', price: 400 },
        { size: 'Large Dog (21kg - 30kg)', price: 500 }
      ],
      'Individual Grooming': [
        { size: 'Nail Trim and Filing', price: 100 },
        { size: 'Teeth Brushing', price: 150 },
        { size: 'Facial Trimming', price: 150 }
      ]
    };

    // Handle service selection change in edit modal
    const editServiceSelect = document.getElementById('edit_service_type');
    const editDogSizeContainer = document.getElementById('edit_dogSizeContainer');
    const editDogSizeOptions = document.getElementById('edit_dogSizeOptions');
    const editIndividualServicesContainer = document.getElementById('edit_individualServicesContainer');
    const editIndividualServiceOptions = document.getElementById('edit_individualServiceOptions');

    editServiceSelect.addEventListener('change', function() {
      const selectedService = this.value;
      
      // Reset containers
      editDogSizeContainer.style.display = 'none';
      editIndividualServicesContainer.style.display = 'none';
      editDogSizeOptions.innerHTML = '';
      editIndividualServiceOptions.innerHTML = '';

      if (selectedService && servicePricing[selectedService]) {
        const options = servicePricing[selectedService];
        
        if (selectedService === 'Individual Grooming') {
          // Show individual services checkboxes
          editIndividualServicesContainer.style.display = 'block';
          options.forEach((option, index) => {
            const checkboxDiv = document.createElement('div');
            checkboxDiv.style.cssText = 'display: flex; align-items: center; padding: 10px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #4db6e9;';
            checkboxDiv.innerHTML = `
              <input type="checkbox" id="edit_service_${index}" name="dog_size[]" value="${option.size}" 
                     style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer; accent-color: #4db6e9;">
              <label for="edit_service_${index}" style="flex: 1; cursor: pointer; font-weight: 500; color: #28496b; font-size: 0.9rem;">
                ${option.size}
              </label>
              <span style="font-weight: 700; color: #4db6e9; font-size: 1rem;">₱ ${option.price}</span>
            `;
            editIndividualServiceOptions.appendChild(checkboxDiv);
          });
        } else {
          // Show dog size radio buttons for Full/Basic Grooming
          editDogSizeContainer.style.display = 'block';
          options.forEach((option, index) => {
            const radioDiv = document.createElement('div');
            radioDiv.style.cssText = 'display: flex; align-items: center; padding: 10px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid #4db6e9;';
            radioDiv.innerHTML = `
              <input type="radio" id="edit_size_${index}" name="dog_size" value="${option.size}" required
                     style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer; accent-color: #4db6e9;">
              <label for="edit_size_${index}" style="flex: 1; cursor: pointer; font-weight: 500; color: #28496b; font-size: 0.9rem;">
                ${option.size}
              </label>
              <span style="font-weight: 700; color: #4db6e9; font-size: 1rem;">₱ ${option.price}</span>
            `;
            editDogSizeOptions.appendChild(radioDiv);
          });
        }
      }
    });

    function openEditModal(appointment) {
      document.getElementById('edit_appointment_id').value = appointment.Appointment_id;
      document.getElementById('edit_email').value = appointment.Email_Address;
      document.getElementById('edit_phone').value = appointment.Phone_Number;
      document.getElementById('edit_pet_name').value = appointment.Pet_Name;
      document.getElementById('edit_pet_breed').value = appointment.Pet_Breed;
      document.getElementById('edit_pet_age').value = appointment.Pet_Age;
      document.getElementById('edit_date').value = appointment.Date;
      document.getElementById('edit_time').value = appointment.Time;
      document.getElementById('edit_service_type').value = appointment.Service_Type;
      
      // Trigger service change to show dog size options
      editServiceSelect.dispatchEvent(new Event('change'));
      
      // Pre-select the dog size if available
      setTimeout(() => {
        if (appointment.Dog_Size) {
          const dogSizes = appointment.Dog_Size.split(', ');
          
          if (appointment.Service_Type === 'Individual Grooming') {
            // Check multiple checkboxes for individual grooming
            dogSizes.forEach(size => {
              const checkbox = document.querySelector(`input[name="dog_size[]"][value="${size}"]`);
              if (checkbox) checkbox.checked = true;
            });
          } else {
            // Select radio button for Full/Basic Grooming
            const radio = document.querySelector(`input[name="dog_size"][value="${appointment.Dog_Size}"]`);
            if (radio) radio.checked = true;
          }
        }
      }, 100);
      
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
    }

    function markAsRead(notificationId) {
      fetch('mark-notification-read.php', {
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

    window.onclick = function(event) {
      const modal = document.getElementById('editModal');
      if (event.target == modal) {
        closeEditModal();
      }
    }
  </script>
</body>

</html>
