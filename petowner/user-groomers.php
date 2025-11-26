<?php
require '../session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">

  <title>Home</title>
  <style>
    * {
      margin: 0;
      padding: 0;
    }

    body {
      background-color: #efefef;
      margin-left: 250px;
      margin-right: 50px;
    }

    .cards {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background-color: #efefef;
    }

    .team-section {
      text-align: center;
      padding: 2rem;
    }

    .team-section h1 {
      font-size: 2.5rem;
      color: #4db6e9;
      margin-bottom: 1.5rem;
    }

    .team-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 5rem;
    }

    .card {
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 8px;
      width: 300px;
      height: 280px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      text-align: center;
      transition: transform 0.2s, box-shadow 0.2s;

    }

    .card:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
    }

    .card-image {
      position: relative;
      height: 50px;
      margin: 0;
      padding: 15px;
      background-color: #ffdd57;
      border-radius: 5px;
    }

    .card h3 {
      font-size: 1.25rem;
      margin-top: 50px;
      color: #333;
    }

    .card p {
      font-size: 1rem;
      color: #666;
      margin-bottom: 1rem;
    }


    .card-image img {
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 1px solid #4db6e9;
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
        <div class="side-btn-container  bookSched-js">
          <div><img src="../assets/event.png" class="img-btn"></div>
          <div class="side-btn">Schedule</div>
        </div>
        <div class="side-btn-container bookings-js">
          <div><img src="../assets/ribbon.png" class="img-btn"></div>
          <div class="side-btn">My Bookings</div>
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
  </div>
</body>

</html>