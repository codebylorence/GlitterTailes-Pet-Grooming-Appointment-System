<?php
require('../db/database.php');
require '../session.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../styles/admin-sidebar.css">
  <link rel="stylesheet" href="../styles/user-home.css">
  <style>
    *{
      margin: 0;
      padding: 0;
    }

    body{
      margin-top: 50px;
      background-color: #efefef;
      margin-left: 350px;
    }

    .table-container {
      display: grid;
      width: 80%;
      margin-bottom: 20px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }

    table,
    th,
    td {
      border: 1px solid #000;
    }

    th,
    td {
      text-align: center;
      padding: 10px;
    }

    th {
      background-color: #007bff;
      color: #fff;
    }

    h1 {
      text-align: center;
      margin-bottom: 20px;
    }

    .service-container {
      display: grid;
      gap: 100px;
      justify-content: center;
      align-items: center;
    }

    .container {
      width: 400px;
      height: 500px;
      padding: 20px;
      background-color: white;
    }

    .form-container {
      display: grid;
      grid-template-columns: 1fr;
      width: 50%;
      gap: 30px;
    }

    table {
      width: 100%;
    }

    .title {
      text-align: center;
    }

    .service-container {
      display: grid;
      row-gap: 30px;
    }

    .input {
      height: 30px;
      padding-left: 10px;
    }

    select {
      width: 100%;
      height: 50px;
    }

    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 100%;
      max-width: 400px;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      padding: 20px;
      display: none;
      /* Hidden by default */
      z-index: 1000;
    }

    .popup.active {
      display: block;
      /* Show when active */
    }

    .popup-header {
      font-size: 20px;
      margin-bottom: 15px;
      text-align: center;
    }

    .popup .input{
      width: 94%;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    .popup select{
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
    }

    .popup input[type="submit"] {
      background-color: #007bff;
      color: white;
      border: none;
      cursor: pointer;
    }

    .popup input[type="submit"]:hover {
      background-color: #0056b3;
    }

    /* Overlay Styles */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;  
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      display: none;
      z-index: 999;
    }

    .overlay.active {
      display: block;
    }

    .lalabas {
      background-color: #28496b;
      border-radius: 5%;
      padding: 10px;
      font-family: Arial, Helvetica, sans-serif;
      font-weight: 600;
      border: 1px solid;
      border-color: #5c5c5c;
      color: #fff;
    }

    .lorence {
      background-color: #28496b;
    }

    .submit-btn{
      align-self: center;
    }

    .book-btn {
      width: 100%;
      background-color: #4db6e9;
      color: white;
      border: none;
      padding: 10px;
      margin-top: 20px;
    }

  </style>
  <title>Home</title>
</head>


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
  <div class="table-container">
    <table>
      <thead>
        <tr>
          <th colspan="3" class="lorence">FULL GROOMING</th>
        </tr>
        <tr>
          <th>Pet Size</th>
          <th>Pet Weight</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Small Dog</td>
          <td>10kg Below</td>
          <td>&#8369; 500</td>
        </tr>
        <tr>
          <td>Medium Dog</td>
          <td>11kg - 20kg</td>
          <td>&#8369; 600</td>
        </tr>
        <tr>
          <td>Large Dog</td>
          <td>21kg - 30kg</td>
          <td>&#8369; 700</td>
        </tr>
      </tbody>
    </table>

    <table>
      <thead>
        <tr>
          <th colspan="3" class="lorence">BASIC GROOMING</th>
        </tr>
        <tr>
          <th>Pet Size</th>
          <th>Pet Weight</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Small Dog</td>
          <td>10kg Below</td>
          <td>&#8369; 300</td>
        </tr>
        <tr>
          <td>Medium Dog</td>
          <td>11kg - 20kg</td>
          <td>&#8369; 400</td>
        </tr>
        <tr>
          <td>Large Dog</td>
          <td>21kg - 30kg</td>
          <td>&#8369; 500</td>
        </tr>
      </tbody>
    </table>

    <table>
      <thead>
        <tr>
          <th colspan="2" class="lorence">INDIVIDUAL GROOMING</th>
        </tr>
        <tr>
          <th>Pet Service</th>
          <th>Price</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Nail Trim and Filing</td>
          <td>&#8369; 100</td>
        </tr>
        <tr>
          <td>Teeth Brushing</td>
          <td>&#8369; 150</td>
        </tr>
        <tr>
          <td>Facial Trimming</td>
          <td>&#8369; 150</td>
        </tr>
      </tbody>
    </table>
    <button id="openFormButton" class="lalabas">Book Now</button>
  </div>



  <!-- Overlay -->
  <div class="overlay" id="overlay"></div>

  <!-- Pop-Up Form -->
  <div class="popup" id="popupForm">
    <div class="popup-header">Fill Out Form</div>
    <form action="./appointment-info.php"  method="post">
          <input type="text" name="emailaddress" placeholder="Email Address" class="input" required>
          <input type="text" name="phonenumber" placeholder="Phone Number" class="input" required>
          <input type="text" name="address" placeholder="Address" class="input" required>
          <input type="text" name="petname" placeholder="Pet's Name" class="input" required>

          <input type="text" name="petbreed" placeholder="Pet's Breed" class="input" required>
          <input type="text" name="petage" placeholder="Pet's Age" class="input" required>
            <input type="date" name="date" class="input" required>
            <label for="time" required>
              <select name="aptmtime" id="time" place>
                <option value="8:00 AM">8:00 AM</option>
                <option value="1:00 PM">1:00 PM</option>
                <option value="4:00 PM">4:00 PM</option>
                <option value="7:00 PM">7:0 0 PM</option>
              </select>
            </label>
            <label for="service" required>
              <select name="services" id="service" place>
                <option value="Full Grooming">Full Grooming</option>
                <option value="Basic Grooming">Basic Grooming</option>
                <option value="Individual Grooming">Individual Grooming</option>
              </select>
            </label>
          <input type="submit" name="submit" value="Book Now" class="book-btn">
        </form>
  </div>
  <script>
    // JavaScript to handle pop-up form
    const openFormButton = document.getElementById("openFormButton");
    const popupForm = document.getElementById("popupForm");
    const overlay = document.getElementById("overlay");

    openFormButton.addEventListener("click", () => {
      popupForm.classList.add("active");
      overlay.classList.add("active");
    });

    overlay.addEventListener("click", () => {
      popupForm.classList.remove("active");
      overlay.classList.remove("active");
    });
  </script>
        <script src="../script/userRedirect.js"></script>
</body>

</html>