<?php
session_start();
include '../api/database.php';
include '../class/bus.php';

// Get bus details from URL parameter
$bus_id = isset($_GET['id']) ? $_GET['id'] : null;
$bus_details = null;

if ($bus_id) {
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        $query = "SELECT DISTINCT * FROM bus WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':id', $bus_id, PDO::PARAM_INT);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $bus_details = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/User_account_bookk_details.css">
    <title>HopStop - Book Details</title>
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

    <div class="container2">
        <div class="slider-container">
            <div class="slider" id="imageSlider">
                <img src="../images/bus1.png" alt="Bus Image 1" class="slider-img active" id="img1">
                <img src="../images/ceres1.png" alt="Bus Image 2" class="slider-img" id="img2">
                <div class="slider-indicators">
                    <div class="indicator active" onclick="changeSlide(0)"></div>
                    <div class="indicator" onclick="changeSlide(1)"></div>
                </div>
            </div>
        </div>

        <div class="bus-details" id="bus">
            <?php if ($bus_details): ?>
                <div class="detail-row">
                    <div class="detail-label">BUS ID:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bus_details['id']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">From:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bus_details['location']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">To:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bus_details['destination']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Departure:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bus_details['departure_time']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Arrival:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bus_details['arrival_time']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Bus Type:</div>
                    <div class="detail-value"><?php echo htmlspecialchars($bus_details['bus_type']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Price:</div>
                    <div class="detail-value">$<?php echo htmlspecialchars($bus_details['price']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-label">Available Seats:</div>
                    <div class="detail-value"><?php echo isset($bus_details['available_seats']) ? htmlspecialchars($bus_details['available_seats']) : 'N/A'; ?></div>
                </div>

                <button class="book-button" onclick="redirectToTicketForm(<?php echo $bus_details['id']; ?>)">Book now</button>
            <?php else: ?>
                <div class="no-details">No bus details available. Please select a bus from the booking page.</div>
                <a href="User_account_booking.php" class="back-button">Go back to search</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="footer"></div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider-img');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;
        let slideInterval;

        function changeSlide(index) {
            currentSlide = index;
            updateSlider();
        }

        function updateSlider() {
            slides.forEach((slide, index) => {
                if (index === currentSlide) {
                    slide.classList.add('active');
                } else {
                    slide.classList.remove('active');
                }
            });

            indicators.forEach((indicator, index) => {
                if (index === currentSlide) {
                    indicator.classList.add('active');
                } else {
                    indicator.classList.remove('active');
                }
            });
        }

        function startAutoSlide() {
            slideInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }, 5000);
        }

        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        sliderContainer.addEventListener('mouseleave', () => {
            startAutoSlide();
        });

        startAutoSlide();

        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                changeSlide(index);
            });
        });

        function redirectToTicketForm(bus_id) {
            window.location.href = "User_account_ticket_form.php?bus_id=" + bus_id;
        }
    </script>
</body>
</html>
