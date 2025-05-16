<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="../style/User.css">
  <title>HopStop</title>
 
</head>
<body>
  <div id="main-container">
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
              <i class ="fas fa-user-circle"></i>
          </div>
         
          <div class="profile-dropdown" id="profileDropdown">
              <div class="dropdown-item" onclick="profile()">Profile</div>
              <div class="dropdown-item" onclick="logOut()">Logout</div>
          </div>
      </div>
    </header>
    
    <section class="hero">
      <div class="container">
          <p>Welcome to HopStop where you can book seats in advance.</p>
      </div>
    </section>

    <section class="banner">
      <h1>HopStop</h1>
    </section>
    
    <section class="search-container">
  <div class="search-title">Book your ticket now!</div>
  <form id="search-form">
    <div class="search-fields">
      <div class="search-field">
        <label for="from-location">From</label>
        <input type="text" id="from-location" name="from" placeholder="Enter your Location" required>
      </div>
      <div class="search-field">
        <label for="to-location">To</label>
        <input type="text" id="to-location" name="to" placeholder="Enter your Destination" required>
      </div>
    </div>
    <button type="submit" class="search-btn">Search</button>
  </form>
  </section>
    
    <section class="results-container" id="search-results">
    </section>
    
    <section class="destinations container">
      <h2>Famous Destinations</h2>
      <div class="destination-grid">
          <div class="destination-card">
              <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
              <div class="destination-info">
                  <h3>PLACE</h3>
                  <p>Description</p>
              </div>
          </div>
          <div class="destination-card">
              <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
              <div class="destination-info">
                  <h3>PLACE</h3>
                  <p>Description</p>
              </div>
          </div>
          <div class="destination-card">
              <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
              <div class="destination-info">
                  <h3>PLACE</h3>
                  <p>Description</p>
              </div>
          </div>
          <div class="destination-card">
              <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
              <div class="destination-info">
                  <h3>PLACE</h3>
                  <p>Description</p>
              </div>
          </div>
      </div>
    </section>
    <footer>
      <div class="container"></div>
    </footer>
   
  </div>
  
  <!-- Results Page Container -->
  <div id="results-container">
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
                <li><a href="Contact.php">Contact</a></li>
                </ul>
          </nav>
          <div class="user-profile" id="profileButton">
            <i class ="fas fa-user-circle"></i>
        </div>
       
        <div class="profile-dropdown" id="profileDropdown">
            <div class="dropdown-item">Profile</div>
            <div class="dropdown-item">Logout</div>
        </div>
        </div>
      <script src="../js/user_script.js"></script>
    </header>
    
    <section class="banner">
      <h1>HopStop</h1>
    </section>
    
    <section class="form-container">
      <input type="text" id="location-display" class="location-input" readonly>
    </section>

  </div>

<script src="../js/User.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    console.log('profileButton:', profileButton);
    console.log('profileDropdown:', profileDropdown);
    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        window.addEventListener('click', function(event) {
            if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    } else {
        console.error('Profile button or dropdown not found');
    }
});

</script>

</body>
</html>