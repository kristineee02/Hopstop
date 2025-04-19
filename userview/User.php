<?php
session_start();

try {
  $pdo = new PDO('mysql:host=localhost;dbname=signup', 'root', '');
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit'])) {
    $from = $_POST['from'];
    $to = $_POST['to'];

    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit'])) {
      $from = $_POST['from'];
      $to = $_POST['to'];
  
      if (!empty($from) && !empty($to)) {
          try {
              $query = "SELECT DISTINCT * FROM bus WHERE location = :from AND destination = :to";
              $stmt = $pdo->prepare($query);
  
              $stmt->bindValue(':from', $from, PDO::PARAM_STR);
              $stmt->bindValue(':to', $to, PDO::PARAM_STR);
  
              $stmt->execute();
              
              $_SESSION['search_results'] = [];
              if ($stmt->rowCount() > 0) {
                  $_SESSION['search_results'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
              } else {
                  $_SESSION['search_results'] = "No tickets available for this route.";
              }
  
              header("Location: User_account_booking.php");
              exit();
          } catch (PDOException $e) {
              die("Database error: " . $e->getMessage());
          }
      } else {
          echo "<script>alert('Please fill in both fields'); window.history.back();</script>";
          exit();
      }
  }
}
?>



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
              <div class="dropdown-item">Profile</div>
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
      <div class="search-fields">
        <div class="search-field">

          <form method="POST" action="User.php">
          <label>From</label>
          <input type="text" name="from" placeholder="Enter your Location" required>
        </div>
        <div class="search-field">
          <label>To</label>
            <input type="text" name="to" placeholder="Enter your Destination" required>
      </div>
      </div>
      <input type="submit" name="submit" value="Search" class="search-btn">
      </form>
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
                <li><a href="contact.php">Contact</a></li>
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
      <script src="User_account.js"></script>
    </header>
    
    <section class="banner">
      <h1>HopStop</h1>
    </section>
    
    <section class="form-container">
      <input type="text" id="location-display" class="location-input" readonly>
    </section>
    
    <section class="results-container" id="search-results">
    </section>
  </div>

<script src="../js/User.js"></script>



</body>
</html>