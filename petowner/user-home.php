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

    .services-wrapper {
      max-width: 1200px;
      margin: 0 auto;
    }

    .services-title {
      text-align: center;
      font-size: 2.5rem;
      color: #28496b;
      margin-bottom: 40px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    .cards-container {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
      gap: 30px;
      margin-bottom: 40px;
    }

    .service-card {
      background: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      overflow: hidden;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .service-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2);
    }

    .card-header {
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      color: white;
      padding: 25px;
      text-align: center;
      font-size: 1.5rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .card-body {
      padding: 25px;
    }

    .price-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px;
      margin-bottom: 12px;
      background: #f8f9fa;
      border-radius: 10px;
      border-left: 4px solid #4db6e9;
      transition: all 0.3s ease;
    }

    .price-item:hover {
      background: #e9ecef;
      transform: translateX(5px);
    }

    .price-item:last-child {
      margin-bottom: 0;
    }

    .item-details {
      flex: 1;
    }

    .item-name {
      font-weight: 600;
      color: #28496b;
      font-size: 1rem;
      margin-bottom: 4px;
    }

    .item-weight {
      font-size: 0.85rem;
      color: #6c757d;
    }

    .item-price {
      font-size: 1.3rem;
      font-weight: 700;
      color: #4db6e9;
      white-space: nowrap;
      margin-left: 15px;
    }

    .book-now-container {
      text-align: center;
      margin-top: 40px;
    }

    .lalabas {
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      color: white;
      border: none;
      padding: 18px 50px;
      font-size: 1.2rem;
      font-weight: 700;
      border-radius: 50px;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 5px 20px rgba(40, 73, 107, 0.3);
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .lalabas:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 30px rgba(40, 73, 107, 0.4);
    }

    .lalabas:active {
      transform: translateY(-1px);
    }

    /* Overlay Styles */
    .overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(5px);
      display: none;
      z-index: 999;
      animation: fadeIn 0.3s ease;
    }

    .overlay.active {
      display: block;
    }

    @keyframes fadeIn {
      from { opacity: 0; }
      to { opacity: 1; }
    }

    @keyframes slideUp {
      from {
        opacity: 0;
        transform: translate(-50%, -40%);
      }
      to {
        opacity: 1;
        transform: translate(-50%, -50%);
      }
    }

    /* Enhanced Popup Form */
    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 90%;
      max-width: 550px;
      max-height: 90vh;
      overflow-y: auto;
      background: white;
      border-radius: 25px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
      padding: 40px;
      display: none;
      z-index: 1000;
      animation: slideUp 0.4s ease;
    }

    .popup.active {
      display: block;
    }

    .popup::-webkit-scrollbar {
      width: 8px;
    }

    .popup::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    .popup::-webkit-scrollbar-thumb {
      background: #4db6e9;
      border-radius: 10px;
    }

    .popup-header {
      font-size: 2rem;
      font-weight: 700;
      margin-bottom: 10px;
      text-align: center;
      color: #28496b;
      text-transform: uppercase;
      letter-spacing: 1px;
    }

    .popup-subtitle {
      text-align: center;
      color: #6c757d;
      margin-bottom: 30px;
      font-size: 0.95rem;
    }

    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 20px;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group.full-width {
      grid-column: 1 / -1;
    }

    .form-label {
      font-size: 0.9rem;
      font-weight: 600;
      color: #28496b;
      margin-bottom: 8px;
      display: flex;
      align-items: center;
      gap: 5px;
    }

    .form-label::before {
      content: "●";
      color: #4db6e9;
      font-size: 0.7rem;
    }

    .popup .input,
    .popup select {
      width: 100%;
      padding: 14px 18px;
      border: 2px solid #e9ecef;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      font-family: 'Poppins', sans-serif;
      background: #f8f9fa;
    }

    .popup .input:focus,
    .popup select:focus {
      outline: none;
      border-color: #4db6e9;
      background: white;
      box-shadow: 0 0 0 4px rgba(77, 182, 233, 0.1);
    }

    .popup select {
      cursor: pointer;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%2328496b' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
      background-repeat: no-repeat;
      background-position: right 15px center;
      padding-right: 40px;
    }

    .book-btn {
      width: 100%;
      background: linear-gradient(135deg, #28496b 0%, #4db6e9 100%);
      color: white;
      border: none;
      padding: 16px;
      margin-top: 25px;
      font-size: 1.1rem;
      font-weight: 700;
      border-radius: 12px;
      cursor: pointer;
      transition: all 0.3s ease;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 5px 20px rgba(40, 73, 107, 0.3);
    }

    .book-btn:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 30px rgba(40, 73, 107, 0.4);
    }

    .book-btn:active {
      transform: translateY(0);
    }

    .close-btn {
      position: absolute;
      top: 20px;
      right: 20px;
      background: #f8f9fa;
      border: none;
      width: 35px;
      height: 35px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 1.3rem;
      color: #6c757d;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .close-btn:hover {
      background: #e9ecef;
      color: #28496b;
      transform: rotate(90deg);
    }

    @media (max-width: 768px) {
      body {
        margin-left: 0;
        padding: 10px;
      }

      .services-title {
        font-size: 1.8rem;
      }

      .cards-container {
        grid-template-columns: 1fr;
      }

      .form-grid {
        grid-template-columns: 1fr;
      }

      .popup {
        padding: 25px;
      }
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
          <?php
          // Get UserId for notification badge
          $username = $_SESSION['Username'];
          $queryUserId = "SELECT UserId FROM useraccounts WHERE Username = ?";
          $stmtUserId = mysqli_prepare($connection, $queryUserId);
          mysqli_stmt_bind_param($stmtUserId, "s", $username);
          mysqli_stmt_execute($stmtUserId);
          $resultUserId = mysqli_stmt_get_result($stmtUserId);
          if ($row = mysqli_fetch_assoc($resultUserId)) {
              $userId = $row['UserId'];
          }
          ?>
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
      <div class="side-btn-container home-js active">
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
      <div class="side-btn-container bookings-js">
        <div><img src="../assets/ribbon.png" class="img-btn"></div>
        <div class="side-btn">My Bookings</div>
        <?php
        // Check for unread notifications
        $queryNotifCount = "SELECT COUNT(*) as unread FROM notifications WHERE UserId = ? AND Is_Read = 0";
        $stmtNotifCount = mysqli_prepare($connection, $queryNotifCount);
        mysqli_stmt_bind_param($stmtNotifCount, "i", $userId);
        mysqli_stmt_execute($stmtNotifCount);
        $resultNotifCount = mysqli_stmt_get_result($stmtNotifCount);
        $notifCountData = mysqli_fetch_assoc($resultNotifCount);
        $unreadNotifCount = $notifCountData['unread'];
        if ($unreadNotifCount > 0) {
            echo '<span style="background: #dc3545; color: white; padding: 3px 8px; border-radius: 10px; font-size: 0.7rem; font-weight: 700; margin-left: 8px; position: absolute; right: 15px;">' . $unreadNotifCount . '</span>';
        }
        ?>
      </div>
      <div class="side-btn-container settings-js">
        <div><img src="../assets/settings.png" class="img-btn"></div>
        <div class="side-btn">Settings</div>
      </div>
    </div>
  </section>
  <div class="services-wrapper">
    <h1 class="services-title">Our Grooming Services</h1>
    
    <div class="cards-container">
      <!-- Full Grooming Card -->
      <div class="service-card">
        <div class="card-header">Full Grooming</div>
        <div class="card-body">
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Small Dog</div>
              <div class="item-weight">10kg Below</div>
            </div>
            <div class="item-price">&#8369; 500</div>
          </div>
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Medium Dog</div>
              <div class="item-weight">11kg - 20kg</div>
            </div>
            <div class="item-price">&#8369; 600</div>
          </div>
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Large Dog</div>
              <div class="item-weight">21kg - 30kg</div>
            </div>
            <div class="item-price">&#8369; 700</div>
          </div>
        </div>
      </div>

      <!-- Basic Grooming Card -->
      <div class="service-card">
        <div class="card-header">Basic Grooming</div>
        <div class="card-body">
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Small Dog</div>
              <div class="item-weight">10kg Below</div>
            </div>
            <div class="item-price">&#8369; 300</div>
          </div>
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Medium Dog</div>
              <div class="item-weight">11kg - 20kg</div>
            </div>
            <div class="item-price">&#8369; 400</div>
          </div>
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Large Dog</div>
              <div class="item-weight">21kg - 30kg</div>
            </div>
            <div class="item-price">&#8369; 500</div>
          </div>
        </div>
      </div>

      <!-- Individual Grooming Card -->
      <div class="service-card">
        <div class="card-header">Individual Grooming</div>
        <div class="card-body">
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Nail Trim and Filing</div>
            </div>
            <div class="item-price">&#8369; 100</div>
          </div>
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Teeth Brushing</div>
            </div>
            <div class="item-price">&#8369; 150</div>
          </div>
          <div class="price-item">
            <div class="item-details">
              <div class="item-name">Facial Trimming</div>
            </div>
            <div class="item-price">&#8369; 150</div>
          </div>
        </div>
      </div>
    </div>

    <div class="book-now-container">
      <button id="openFormButton" class="lalabas">Book Now</button>
    </div>
  </div>



  <!-- Overlay -->
  <div class="overlay" id="overlay"></div>

  <!-- Enhanced Pop-Up Form -->
  <div class="popup" id="popupForm">
    <button class="close-btn" id="closeBtn">×</button>
    <div class="popup-header">Book Appointment</div>
    <div class="popup-subtitle">Fill in the details below to schedule your pet's grooming</div>
    
    <form action="./appointment-info.php" method="post">
      <div class="form-grid">
        <!-- Contact Information -->
        <div class="form-group full-width">
          <label class="form-label">Email Address</label>
          <input type="email" name="emailaddress" placeholder="your.email@example.com" class="input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Phone Number</label>
          <input type="tel" name="phonenumber" placeholder="09XX XXX XXXX" class="input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Address</label>
          <input type="text" name="address" placeholder="Your address" class="input" required>
        </div>

        <!-- Pet Information -->
        <div class="form-group">
          <label class="form-label">Pet's Name</label>
          <input type="text" name="petname" placeholder="e.g., Buddy" class="input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Pet's Breed</label>
          <input type="text" name="petbreed" placeholder="e.g., Golden Retriever" class="input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Pet's Age</label>
          <input type="text" name="petage" placeholder="e.g., 2 years" class="input" required>
        </div>

        <!-- Appointment Details -->
        <div class="form-group">
          <label class="form-label">Appointment Date</label>
          <input type="date" name="date" class="input" required>
        </div>

        <div class="form-group">
          <label class="form-label">Preferred Time</label>
          <select name="aptmtime" id="time" required>
            <option value="" disabled selected>Select time slot</option>
            <option value="8:00 AM">8:00 AM</option>
            <option value="1:00 PM">1:00 PM</option>
            <option value="4:00 PM">4:00 PM</option>
            <option value="7:00 PM">7:00 PM</option>
          </select>
        </div>

        <div class="form-group full-width">
          <label class="form-label">Service Type</label>
          <select name="services" id="service" required>
            <option value="" disabled selected>Choose a service</option>
            <option value="Full Grooming">Full Grooming - Complete care package</option>
            <option value="Basic Grooming">Basic Grooming - Shampoo & Blow Dry</option>
            <option value="Individual Grooming">Individual Grooming - Custom services</option>
          </select>
        </div>

        <!-- Dog Size Selection (Hidden by default) -->
        <div class="form-group full-width" id="dogSizeContainer" style="display: none;">
          <label class="form-label">Select Dog Size</label>
          <div id="dogSizeOptions" style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
            <!-- Options will be populated by JavaScript -->
          </div>
        </div>

        <!-- Individual Services Selection (Hidden by default) -->
        <div class="form-group full-width" id="individualServicesContainer" style="display: none;">
          <label class="form-label">Select Services</label>
          <div id="individualServiceOptions" style="display: flex; flex-direction: column; gap: 12px; margin-top: 10px;">
            <!-- Options will be populated by JavaScript -->
          </div>
        </div>
      </div>

      <input type="submit" name="submit" value="Confirm Booking" class="book-btn">
    </form>
  </div>
  <script>
    // Enhanced JavaScript to handle pop-up form
    const openFormButton = document.getElementById("openFormButton");
    const popupForm = document.getElementById("popupForm");
    const overlay = document.getElementById("overlay");
    const closeBtn = document.getElementById("closeBtn");

    // Open popup
    openFormButton.addEventListener("click", () => {
      popupForm.classList.add("active");
      overlay.classList.add("active");
      document.body.style.overflow = "hidden"; // Prevent background scrolling
    });

    // Close popup when clicking overlay
    overlay.addEventListener("click", () => {
      popupForm.classList.remove("active");
      overlay.classList.remove("active");
      document.body.style.overflow = "auto";
    });

    // Close popup when clicking close button
    closeBtn.addEventListener("click", (e) => {
      e.preventDefault();
      popupForm.classList.remove("active");
      overlay.classList.remove("active");
      document.body.style.overflow = "auto";
    });

    // Set minimum date to today
    const dateInput = document.querySelector('input[name="date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);

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

    // Handle service selection change
    const serviceSelect = document.getElementById('service');
    const dogSizeContainer = document.getElementById('dogSizeContainer');
    const dogSizeOptions = document.getElementById('dogSizeOptions');
    const individualServicesContainer = document.getElementById('individualServicesContainer');
    const individualServiceOptions = document.getElementById('individualServiceOptions');

    serviceSelect.addEventListener('change', function() {
      const selectedService = this.value;
      
      // Reset containers
      dogSizeContainer.style.display = 'none';
      individualServicesContainer.style.display = 'none';
      dogSizeOptions.innerHTML = '';
      individualServiceOptions.innerHTML = '';

      if (selectedService && servicePricing[selectedService]) {
        const options = servicePricing[selectedService];
        
        if (selectedService === 'Individual Grooming') {
          // Show individual services checkboxes
          individualServicesContainer.style.display = 'block';
          options.forEach((option, index) => {
            const checkboxDiv = document.createElement('div');
            checkboxDiv.style.cssText = 'display: flex; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #4db6e9; transition: all 0.3s ease;';
            checkboxDiv.innerHTML = `
              <input type="checkbox" id="service_${index}" name="dog_size[]" value="${option.size}" 
                     style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #4db6e9;">
              <label for="service_${index}" style="flex: 1; cursor: pointer; font-weight: 500; color: #28496b;">
                ${option.size}
              </label>
              <span style="font-weight: 700; color: #4db6e9; font-size: 1.1rem;">₱ ${option.price}</span>
            `;
            individualServiceOptions.appendChild(checkboxDiv);
          });
        } else {
          // Show dog size radio buttons for Full/Basic Grooming
          dogSizeContainer.style.display = 'block';
          options.forEach((option, index) => {
            const radioDiv = document.createElement('div');
            radioDiv.style.cssText = 'display: flex; align-items: center; padding: 12px; background: #f8f9fa; border-radius: 10px; border-left: 4px solid #4db6e9; transition: all 0.3s ease;';
            radioDiv.innerHTML = `
              <input type="radio" id="size_${index}" name="dog_size" value="${option.size}" required
                     style="width: 20px; height: 20px; margin-right: 12px; cursor: pointer; accent-color: #4db6e9;">
              <label for="size_${index}" style="flex: 1; cursor: pointer; font-weight: 500; color: #28496b;">
                ${option.size}
              </label>
              <span style="font-weight: 700; color: #4db6e9; font-size: 1.1rem;">₱ ${option.price}</span>
            `;
            dogSizeOptions.appendChild(radioDiv);
          });
        }
      }
    });
  </script>
        <script src="../script/userRedirect.js"></script>
</body>

</html>