<?php
require '../db/database.php';
require '../session-admin.php';

// Initialize counts
function getAppointments()
{
  require '../db/database.php';
  $appointmentCount = 0;

  // Count total appointments
  $queryAppointments = "SELECT COUNT(*) AS total_appointments FROM appointments";
  $stmtAppointments = mysqli_prepare($connection, $queryAppointments);

  if ($stmtAppointments) {
    mysqli_stmt_execute($stmtAppointments);
    $resultAppointments = mysqli_stmt_get_result($stmtAppointments);

    if ($row = mysqli_fetch_assoc($resultAppointments)) {
      $appointmentCount = $row['total_appointments'];
      return $appointmentCount;
    }

    mysqli_stmt_close($stmtAppointments);
  } else {
    $appointmentCount = "Error fetching appointment count";
  }
}

function getUserCount()
{
  require '../db/database.php';
  $queryUsers = "SELECT COUNT(*) AS total_users FROM useraccounts";
  $stmtUsers = mysqli_prepare($connection, $queryUsers);

  if ($stmtUsers) {
    mysqli_stmt_execute($stmtUsers);
    $resultUsers = mysqli_stmt_get_result($stmtUsers);

    if ($row = mysqli_fetch_assoc($resultUsers)) {
      $userCount = $row['total_users'];
      return $userCount;
    }

    mysqli_stmt_close($stmtUsers);
  } else {
    $userCount = "Error fetching user count";
  }
}

function getDateAppointmentCount()
{
    require '../db/database.php';

    // Set the timezone to Philippine Time
    date_default_timezone_set('Asia/Manila');

    // Get the current date in the Philippine timezone
    $currentDate = date('Y-m-d'); // Format as Y-m-d (e.g., 2025-01-19)

    // Query to count appointments for the current date
    $queryDate = "SELECT COUNT(*) AS total_count FROM appointments WHERE Date = ?";
    $stmtDate = mysqli_prepare($connection, $queryDate);

    if ($stmtDate) {
        // Bind the current date as a parameter to the query
        mysqli_stmt_bind_param($stmtDate, "s", $currentDate);

        // Execute the prepared statement
        mysqli_stmt_execute($stmtDate);

        // Get the result set
        $resultDate = mysqli_stmt_get_result($stmtDate);

        if ($resultDate && $row = mysqli_fetch_assoc($resultDate)) {
            $totalCount = $row['total_count']; // Fetch the total count
            mysqli_stmt_close($stmtDate); // Close the statement
            return $totalCount; // Return the total count
        } else {
            mysqli_stmt_close($stmtDate); // Close the statement
            return 0; // Return 0 if no data found
        }
    } else {
        echo "Error preparing the query: " . mysqli_error($connection);
        return 0; // Return 0 in case of an error
    }
}


$totalAppointmentsToday = getDateAppointmentCount();

function getServiceTypeCounts()
{
  require '../db/database.php';
  $query = "SELECT Service_Type, COUNT(*) AS total_count FROM appointments GROUP BY Service_Type";
  $stmt = mysqli_prepare($connection, $query);

  if ($stmt) {
    // Execute the prepared statement
    mysqli_stmt_execute($stmt);

    // Get the result set
    $result = mysqli_stmt_get_result($stmt);

    $serviceCounts = [];
    while ($row = mysqli_fetch_assoc($result)) {
      $serviceCounts[] = $row; // Add each row to the array
    }

    mysqli_stmt_close($stmt); // Close the statement
    return $serviceCounts; // Return the array of counts
  } else {
    echo "Error preparing the query: " . mysqli_error($connection);
    return null;
  }
}

$serviceTypeCounts = getServiceTypeCounts();

// Close the database connection
mysqli_close($connection);
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <link rel="stylesheet" href="../styles/generals.css">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
  <title>Admin</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      background-color: #e6e8ed;
    }

    .material-icons-outlined {
      vertical-align: middle;
      line-height: 1px;
    }



    .grid-container {
      display: grid;
      grid-template-columns: 260px 1fr 1fr 1fr;
      grid-template-rows: 0.2fr 3fr;
      grid-template-areas:
        "sidebar header header header"
        "sidebar main main main";
      height: 100vh;
    }



    /* ---------- MAIN ---------- */

    .main-container {
      grid-area: main;
      overflow-y: auto;
      padding: 20px 20px;
    }

    .main-title {
      display: flex;
      justify-content: space-between;
    }

    .main-title>p {
      font-size: 20px;
    }

    .main-cards {
      display: grid;
      grid-template-columns: 1fr 1fr 1fr;
      gap: 20px;
      margin: 20px 0;
    }

    .card {
      display: flex;
      flex-direction: column;
      justify-content: space-around;
      padding: 25px;
      background-color: #ffffff;
      box-sizing: border-box;
      border: 1px solid #d2d2d3;
      border-radius: 5px;
      box-shadow: 0 6px 7px -4px rgba(0, 0, 0, 0.2);
    }


    .card-inner {
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .card-inner>p {
      font-size: 18px;
    }

    .card-inner>span {
      font-size: 35px;
    }


    .charts-card {
      background-color: #ffffff;
      margin-bottom: 20px;
      padding: 25px;
      box-sizing: border-box;
      -webkit-column-break-inside: avoid;
      border: 1px solid #d2d2d3;
      border-radius: 5px;
      box-shadow: 0 6px 7px -4px rgba(0, 0, 0, 0.2);
    }

    .chart-title {
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      font-weight: 600;
    }





    /* ---------- MEDIA QUERIES ---------- */


    /* Medium <= 992px */
    @media screen and (max-width: 992px) {
      .grid-container {
        grid-template-columns: 1fr;
        grid-template-areas:
          "header"
          "main";
      }




      .sidebar-title>span {
        display: inline;
      }
    }

    /* Small <= 768px */
    @media screen and (max-width: 768px) {
      .main-cards {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 0;
      }

      .charts {
        grid-template-columns: 1fr;
        margin-top: 30px;
      }
    }

    /* Extra Small <= 576px */
    @media screen and (max-width: 576px) {
      .header-left {
        display: none;
      }
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
            <p class="admin-name"><?php echo htmlspecialchars($_SESSION['Username']); ?></p>
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
      <div class="grid-container">
        <main class="main-container">
          <div class="main-title">
            <p class="font-weight-bold">DASHBOARD</p>
          </div>

          <div class="main-cards">
            <div class="card">
              <div class="card-inner">
                <p class="text-primary">Pet Owners</p>
                <span class="material-icons-outlined text-blue">people</span>
              </div>
              <span class="text-primary font-weight-bold"><?php echo htmlspecialchars(getUserCount()); ?></span>
            </div>

            <div class="card">
              <div class="card-inner">
                <p class="text-primary">New Booking</p>
                <span class="material-icons-outlined text-orange">bookmark</span>
              </div>
              <span class="text-primary font-weight-bold"><?php echo htmlspecialchars(getAppointments()); ?></span>
            </div>

            <div class="card">
              <div class="card-inner">
                <p class="text-primary">Today Session</p>
                <span class="material-icons-outlined text-green">event</span>
              </div>
              <span class="text-primary font-weight-bold"><?php echo htmlspecialchars($totalAppointmentsToday); ?></span>
            </div>
          </div>

          <div class="charts">
            <div class="charts-card">
              <p class="chart-title">Services</p>
              <div id="bar-chart"></div>
            </div>
          </div>
        </main>
      </div>
    </section>

    <script src="../script/adminRedirect.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.3/apexcharts.min.js"></script>
    <script>
      // Prepare the data for the chart dynamically
      const serviceTypes = <?php echo json_encode(array_column($serviceTypeCounts, 'Service_Type')); ?>;
      const serviceCounts = <?php echo json_encode(array_column($serviceTypeCounts, 'total_count')); ?>;

      const barChartOptions = {
        series: [{
          data: serviceCounts, // Use the dynamic service counts
        }],
        chart: {
          type: 'bar',
          height: 350,
          toolbar: {
            show: false,
          },
        },
        colors: ['#246dec', '#cc3c43', '#18392B'],
        plotOptions: {
          bar: {
            distributed: true,
            borderRadius: 5,
            horizontal: false,
            columnWidth: '60%',
          },
        },
        dataLabels: {
          enabled: false,
        },
        legend: {
          show: false,
        },
        xaxis: {
          categories: serviceTypes, // Use the dynamic service types
        },
        yaxis: {
          title: {
            text: '',
          },
        },
      };

      const barChart = new ApexCharts(
        document.querySelector('#bar-chart'),
        barChartOptions
      );
      barChart.render();
    </script>
  </div>
</body>

</html>