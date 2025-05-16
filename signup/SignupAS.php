
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/Homepage.css">
    <script src="../js/Homepage.js"></script>

    <title>HopStop</title>
    <style>
        * SIGN UP AS PAGE */

.signupas {
    font-style: italic;
    text-shadow: #646464 0px 0px 1px;
    font-size: 20px;
    margin-top: 80px;
    margin-bottom: 20px;
    position: relative;
    width: 100%;
}

.signupbox {
    display: flex;
    gap: 20px;
    top: 50px;
    position: relative;
}

.signupoption {
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    align-items: center;
    text-decoration: none;
    color: black;
    width: 125px;
    height: 125px;
    background-color: white;
    border: 1px solid black;
    font-weight: 500;
    font-family: 'Inter', sans-serif;
    padding: 10px;
    border-radius: 2.5px;
}

.signupoption:hover {
    box-shadow: 0px 1px 0px 2px rgba(0, 0, 0, 0.3); 
}

.signupoption img {
      width: 55px;
      height: 55px;
      object-fit: contain;
      margin-top: 10px;
      margin-bottom: 15px;
}

.signupoption span {
    margin-top: auto;
    margin-bottom: 5px;
    font-size: 16px;
    font-weight: 500;
}
</style>
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
                    <li><a href="../homepage/Homepage.php">Home</a></li>
                    <li><a href="../homepage/Aboutpage.php">About</a></li>
                    <li><a href="../homepage/Contactpage.php">Contact</a></li>
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
            </div>

            <p class="signupas">Sign up as:</p>
        <div class="signupbox">
            <a href="usersignup.php" class="signupoption">
                <img src="../images/client-profile.png"/>
                <span>PASSENGER</span>
            </a>
            <a href="adminsignup.php" class="signupoption">
                <img src="../images/freelancer-icon.png"/>
                <span>ADMIN</span>
            </a>
        </div>
</div>
        
    </section>

    <footer>
    </footer>

</body>
</html>
