
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/Homepage.css">
    <script src="../js/Homepage.js"></script>
    <title>HopStop</title>
</head>
<body>
    <header>
        <div class="container nav-container">
            <div class="logo">
                <div class="logo-circle"></div>
                <span><b>HopStop</b></span>
            </div>
            <nav>
                <button class="mobile-menu-btn">☰</button>
                <ul class="nav-links">
                    <li><a href="Homepage.php">Home</a></li>
                    <li><a href="Aboutpage.php">About</a></li>
                    <li><a href="Contactpage.php">Contact</a></li>
                </ul>
            </nav>
            <button class="login-btn" onclick="login()">Log In</button>
        </div>
    </header>

    <section class="cover-section">
        <img src="../images/Homepage_image1.png" alt="Bus Interior" class="cover-image">
        <div class="cover-overlay">
            <div class="cover-content">
                <h1>HopStop</h1>
                <p>Your journey begins with us</p>
                <button class="scroll-btn" id="scroll-down">Explore Now</button>
            </div>
        </div>
    </section>

    <section class="hero">
        <div class="hero-content">
            <h1>HopStop</h1> 
            <p>Bus Travel Made Easy</p>
        </div>
    </section>

    <section class="search-form-container">
        <div class="search-form">
            <h2>Book your ticket now!</h2>
            <form>
                <div class="form-group">
                    <div class="form-control">
                        <label for="from">From</label>
                        <input type="text" id="from" placeholder="Enter city">
                    </div>
                    <div class="form-control">
                        <label for="to">To</label>
                        <input type="text" id="to" placeholder="Enter city">
                    </div>
                </div>
                <button type="submit" class="search-btn">Search</button>
            </form>
        </div>
    </section>

    <section class="destinations container">
        <h2>Famous Destinations</h2>
        <div class="destination-grid">
            <div class="destination-card">
                <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
                <div class="destination-info">
                    <h3>PLACE</h3>
                    <p>Discover amazing scenery and attractions</p>
                </div>
            </div>
            <div class="destination-card">
                <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
                <div class="destination-info">
                    <h3>PLACE</h3>
                    <p>Discover amazing scenery and attractions</p>
                </div>
            </div>
            <div class="destination-card">
                <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
                <div class="destination-info">
                    <h3>PLACE</h3>
                    <p>Discover amazing scenery and attractions</p>
                </div>
            </div>
            <div class="destination-card">
                <img class="destination-img" src="../images/Homepage_image2.png" alt="Destination">
                <div class="destination-info">
                    <h3>PLACE</h3>
                    <p>Discover amazing scenery and attractions</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="#">Terms of Use</a>
                <a href="#">Support</a>
            </div>
            <p>&copy; 2025 HopStop. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>