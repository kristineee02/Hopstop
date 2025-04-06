
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/About.css">
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

    <section class="banner" id="home">
        <h1>HopStop</h1>
    </section>

    <section class="about-section" id="about">
        <div class="container">
            <div class="about-container">
                <div class="about-image">
                    <img src="../images/Aboutpage_image.png" alt="People on public transportation">
                </div>
                <div class="about-text">
                    <h2>About Us</h2>
                    <p>HopStop is the Philippines’ first real-time bus ticket booking service. Trusted by established bus operators and hundreds of thousands of bus passengers, HopStop is proud to be the pioneer in bringing convenience and operational efficiency to bus travel in the Philippines.</p>
                    <p>Secure your seats for your next trip and get fast confirmation</p>
                </div>
            </div>
        </div>
    </section>

    <section class="goals-section">
        <div class="container">
            <div class="goals-container">
                <div class="goal-card">
                    <h3>Mission</h3>
                    <p>Our mission is to provide safe, reliable, eco-friendly, and affordable bus transportation that connects communities and empowers individuals. </p>
                </div>
                <div class="goal-card">
                    <h3>Vision</h3>
                    <p>Our vision is to lead in innovative, sustainable, and customer-focused bus transportation, creating seamless travel experiences that connect people and places.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="faq-landscape-section">
        <div class="container">
            <h2>FAQ</h2>
            <div class="faq-landscape-container">
                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question">How does HopStop work?</div>
                        <div class="faq-answer">
                            <p>HopStop works by aggregating real-time data from various public transportation providers. Simply enter your starting point and destination, and our algorithm will find the most efficient route for you, considering factors like traffic, schedules, and your preferences.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">Which cities do you support?</div>
                        <div class="faq-answer">
                            <p>We currently support areas across Zamboanga Peninsula. We're continuously expanding our coverage to include more cities and transportation networks.</p>
                        </div>
                    </div>
                    <div class="faq-item">
                        <div class="faq-question">When should I pay my bus ticket?</div>
                        <div class="faq-answer">
                            <p>Upon boarding the bus.</p>
                        </div>
                    </div>
                </div>
                <div class="landscape-container">
                    <img src="../images/Aboutpage_image-2.png" alt="Beautiful landscape" class="landscape-image">
                </div>
            </div>
        </div>
    </section>

    <footer id="contact">
        <div class="container">
            <p>© 2025 HopStop | All Rights Reserved</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Contact Us</a>
            </div>
        </div>
    </footer>
    <script src="../js/Aboutpage.js"></script>
    <script src="../js/User.js"></script>

   
</body>
</html>