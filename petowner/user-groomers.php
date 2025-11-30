<?php
require '../session.php';
require '../db/database.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <title>All Groomers</title>
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

    .cards {
      font-family: 'Poppins', Arial, sans-serif;
      margin: 0;
      padding: 0;
    }

    .team-section {
      text-align: center;
      padding: 2rem;
    }

    .team-section h1 {
      font-size: 2.5rem;
      color: #28496b;
      margin-bottom: 2rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .team-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 5rem;
    }

    .card {
      background: white;
      border: none;
      border-radius: 20px;
      width: 300px;
      height: 320px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      overflow: hidden;
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .card-image {
      position: relative;
      height: 80px;
      margin: 0;
      padding: 20px;
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .card h3 {
      font-size: 1.3rem;
      margin-top: 60px;
      margin-bottom: 15px;
      color: #28496b;
      font-weight: 700;
    }

    .card p {
      font-size: 0.95rem;
      color: #6c757d;
      margin-bottom: 1rem;
      padding: 0 20px;
      line-height: 1.5;
    }

    .card-image img {
      height: 120px;
      width: 120px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid white;
      box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
      position: absolute;
      top: 20px;
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
      <div class="side-btn-container groomers-js active">
        <div><img src="../assets/brush.png" class="img-btn"></div>
        <div class="side-btn">All Groomers</div>
      </div>
      <div class="side-btn-container bookSched-js">
        <div><img src="../assets/event.png" class="img-btn"></div>
        <div class="side-btn">Schedule</div>
      </div>
      <div class="side-btn-container bookings-js">
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

  <div class="cards">
      <section class="team-section">
        <h1>OUR TEAM</h1>
        <div class="team-container">
          <div class="card">
            <div class="card-image">
              <img src="../assets/img/1.jpg" alt="picture" class="image">
            </div>
            <h3>Alex Dela Cruz</h3>
            <p>I have been grooming pets for over 10 years. I love working with dogs and cats alike!</p>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="../assets/img/3.jpg" alt="picture" class="image">
            </div>
            <h3>Tchard Rojon</h3>
            <p>Specializing in styling different pets. Grooming is my passion!</p>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="../assets/img/6.jpg" alt="picture" class="image">
            </div>
            <h3>Bunnie Joy</h3>
            <p>From rabbits to birds, I ensure every pet feels at home during their grooming session.</p>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="../assets/img/5.jpg" alt="picture" class="image">
            </div>
            <h3>Taylor De Leon</h3>
            <p>Known for trendy grooming styles and calming anxious pets.</p>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="../assets/img/2.jpg" alt="picture" class="image">
            </div>
            <h3>Chris Corpuz</h3>
            <p>I pride myself in creating a fun and safe grooming experience for all pets.</p>
          </div>
          <div class="card">
            <div class="card-image">
              <img src="../assets/img/4.jpg" alt="picture" class="image">
            </div>
            <h3>Jebron Lames</h3>
            <p>Providing top-notch grooming services for pets of all sizes and temperaments.</p>
          </div>
        </div>
      </section>
  </div>

  <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
  <script src="../script/groomer.js"></script>
  <script src="../script/userRedirect.js"></script>
</body>

</html>