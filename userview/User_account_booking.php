<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="../style/booking.css" />
  <title>HopStop - Booking Results</title>
</head>
<body>
  <header>
    <div class="container nav-container">
      <div class="logo">
        <div class="logo-circle"></div>
        <span><b>HopStop</b></span>
      </div>
      <nav>
        <ul class="nav-links">
          <li><a href="User.php">Home</a></li>
          <li><a href="About.php">About</a></li>
          <li><a href="contact.php">Contact</a></li>
        </ul>
      </nav>

      <div class="user-profile" id="profileButton">
        <i class="fas fa-user-circle"></i>
      </div>

      <div class="profile-dropdown" id="profileDropdown">
        <div class="dropdown-item">Profile</div>
        <div class="dropdown-item">Logout</div>
      </div>
    </div>
    <script src="../js/Userlogout.js"></script>
  </header>

  <div class="header-container">
    <img src="../images/Homepage_image1.png" alt="Bus Interior" class="header-image" />
  </div>
  <div class="search-box">
    <h2>Choose your travel location</h2>
  </div>

  <div class="user-container">
          <h2 class="trip">Available Tickets:</h2>
          <div id="trips-container"></div>
                <!-- <div class="trip-card">
            
        </div> -->
    </div>
  </div>

  <script src="../js/user_account_booking.js"></script>
</body>
</html>
