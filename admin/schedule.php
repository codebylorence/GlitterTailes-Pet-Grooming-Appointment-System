<?php
require '../db/database.php';
require '../session-admin.php';

// Set timezone to Philippine Time
date_default_timezone_set('Asia/Manila');

// Get the current date and time
$currentDate = date('Y-m-d');

// Modify the query to include Firstname and Lastname from useraccounts table
// Only show APPROVED appointments for today
$sql = "SELECT 
            SQL_CALC_FOUND_ROWS
            a.UserId, 
            u.Firstname, 
            u.Lastname, 
            a.Email_Address, 
            a.Phone_Number, 
            a.Address, 
            a.Pet_Name, 
            a.Pet_Breed, 
            a.Date, 
            a.Pet_Age, 
            a.Time, 
            a.Service_Type, 
            a.Appointment_id,
            a.Status
        FROM appointments a
        INNER JOIN useraccounts u ON a.UserId = u.UserId
        WHERE a.Date = ? AND a.Status = 'Approved'";

$stmt = mysqli_prepare($connection, $sql);
mysqli_stmt_bind_param($stmt, "s", $currentDate);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Fetch total number of appointments
$totalResult = mysqli_query($connection, "SELECT FOUND_ROWS() AS total");
$totalAppointments = mysqli_fetch_assoc($totalResult)['total'];

// Collect appointments
$appointments = [];
if ($result && mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // You can format the time here as well, e.g., converting it to a 12-hour format
    $row['Time'] = date('h:i A', strtotime($row['Time']));  // Converts to 12-hour format (e.g., 8:00 PM)

    $appointments[] = $row;
  }
} else {
  echo "No appointments found for today or query error: " . mysqli_error($connection);
}

// Handle appointment cancellation
if (isset($_GET['cancel_id'])) {
  $cancel_id = $_GET['cancel_id'];
  $deleteSql = "DELETE FROM appointments WHERE Appointment_id = ?";
  $stmt = mysqli_prepare($connection, $deleteSql);
  mysqli_stmt_bind_param($stmt, "i", $cancel_id);

  if (mysqli_stmt_execute($stmt)) {
    header("Location: " . $_SERVER['PHP_SELF']); // Reload the page
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
      <div class="side-btn-container schedule-js active">
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
    <h1>Today's Appointments (<?php echo $totalAppointments; ?>)</h1>
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
                <div class="action-btns">
                  <button type="button" class="edit-btn" onclick="openEditModal(<?php echo htmlspecialchars(json_encode($appointment)); ?>)">Edit</button>
                  <a href="?cancel_id=<?php echo $appointment['Appointment_id']; ?>" onclick="return confirm('Are you sure you want to cancel this appointment?');">
                    <button type="button" class="delete-btn">Delete</button>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    </div>
  </section>

  <!-- Edit Modal -->
  <div id="editModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px);">
    <div class="modal-content" style="background: white; margin: 5% auto; padding: 30px; border-radius: 20px; width: 90%; max-width: 600px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);">
      <span class="close" onclick="closeEditModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
      <h2 style="font-size: 1.8rem; font-weight: 700; color: #28496b; margin-bottom: 20px; text-transform: uppercase;">Edit Appointment</h2>
      <form id="editForm" method="post" action="update-appointment.php">
        <input type="hidden" name="appointment_id" id="edit_appointment_id">
        
        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Email Address</label>
          <input type="email" name="email" id="edit_email" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Phone Number</label>
          <input type="text" name="phone" id="edit_phone" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Address</label>
          <input type="text" name="address" id="edit_address" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Pet Name</label>
          <input type="text" name="pet_name" id="edit_pet_name" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Pet Breed</label>
          <input type="text" name="pet_breed" id="edit_pet_breed" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Pet Age</label>
          <input type="text" name="pet_age" id="edit_pet_age" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Appointment Date</label>
          <input type="date" name="date" id="edit_date" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Appointment Time</label>
          <select name="time" id="edit_time" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
            <option value="8:00 AM">8:00 AM</option>
            <option value="1:00 PM">1:00 PM</option>
            <option value="4:00 PM">4:00 PM</option>
            <option value="7:00 PM">7:00 PM</option>
          </select>
        </div>

        <div style="margin-bottom: 15px;">
          <label style="display: block; font-weight: 600; color: #28496b; margin-bottom: 5px;">Service Type</label>
          <select name="service_type" id="edit_service_type" required style="width: 100%; padding: 10px 15px; border: 2px solid #e9ecef; border-radius: 8px; background: #f8f9fa;">
            <option value="Full Grooming">Full Grooming</option>
            <option value="Basic Grooming">Basic Grooming</option>
            <option value="Individual Grooming">Individual Grooming</option>
          </select>
        </div>

        <button type="submit" style="width: 100%; padding: 12px; background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%); color: white; border: none; border-radius: 10px; font-size: 1rem; font-weight: 700; cursor: pointer; text-transform: uppercase;">Save Changes</button>
      </form>
    </div>
  </div>

  <script src="../script/adminRedirect.js"></script>
  <script>
    function openEditModal(appointment) {
      document.getElementById('edit_appointment_id').value = appointment.Appointment_id;
      document.getElementById('edit_email').value = appointment.Email_Address;
      document.getElementById('edit_phone').value = appointment.Phone_Number;
      document.getElementById('edit_address').value = appointment.Address;
      document.getElementById('edit_pet_name').value = appointment.Pet_Name;
      document.getElementById('edit_pet_breed').value = appointment.Pet_Breed;
      document.getElementById('edit_pet_age').value = appointment.Pet_Age;
      document.getElementById('edit_date').value = appointment.Date;
      document.getElementById('edit_time').value = appointment.Time;
      document.getElementById('edit_service_type').value = appointment.Service_Type;
      
      document.getElementById('editModal').style.display = 'block';
    }

    function closeEditModal() {
      document.getElementById('editModal').style.display = 'none';
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