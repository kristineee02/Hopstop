<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/User_account_ticket_form.css">
    <link rel="stylesheet" href="../style/User_account_seatSelection.css">
    <script src="../js/Userlogout.js"></script>
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    <title>HopStop - Ticket Form</title>
    <style>
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
        }
        .modal.show {
            display: flex;
        }
        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            width: 90%;
            max-width: 350px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            position: relative;
        }
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #333;
        }
        #seat-map {
            display: flex;
            justify-content: center;
            max-width: 300px;
            margin: 20px auto;
        }
        #seat-map > div {
            display: grid;
            grid-template-columns: repeat(5, 50px);
            gap: 10px;
        }
        .seat-button {
            background-color: #4caf50;
            border: none;
            color: white;
            padding: 8px;
            font-size: 14px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            width: 50px;
            height: 40px;
            text-align: center;
        }
        .seat-button:hover:not(:disabled) {
            background-color: #45a049;
        }
        .seat-button.selected {
            background-color: #007bff;
        }
        .seat-button:disabled,
        .seat-button.unavailable {
            background-color: #ccc;
            cursor: not-allowed;
        }
        .action-button {
            background-color: #a682c1;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        .action-button:hover {
            background-color: #8e69a5;
        }
    </style>
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
                <i class="fas fa-user-circle"></i>
            </div>
            <div class="profile-dropdown" id="profileDropdown">
                <div class="dropdown-item">Profile</div>
                <div class="dropdown-item" onclick="logOut()">Logout</div>
            </div>
        </div>
    </header>
    <div class="container3">
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
        <div class="ticket-form">
            <div class="passenger-section">
                <h3>Passenger Details</h3>
                <div class="form-group">
                    <label for="name">Full Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter full name" required>
                </div>
                <div class="form-group">
                    <label>Passenger Type:</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="regular" name="passengerType" value="Regular">
                            <label for="regular">Regular</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="pwd" name="passengerType" value="PWD/Senior Citizen">
                            <label for="pwd">PWD/Senior</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="student" name="passengerType" value="Student">
                            <label for="student">Student</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="seat-number">Seat Number:</label>
                <input type="text" id="seat-number" name="seatNumber" readonly>
                <button class="action-button" id="find-seat-btn">Find Seat</button>
            </div>
            <div id="seat-modal" class="modal">
                <div class="modal-content">
                    <span class="close" id="close-seat-modal">×</span>
                    <h3>Select a Seat</h3>
                    <div id="seat-map"></div>
                    <button class="action-button" id="confirm-seat-btn" style="width: 100%; margin-top: 20px;">Confirm</button>
                </div>
            </div>
            <div class="trip-info-container" id="trip-info">
                <h3>Trip Information</h3>
                <div class="form-group">
                    <label>Bus ID: <span class="auto-generated"></span></label>
                </div>
                <div class="form-group">
                    <label>From: <span class="auto-generated"></span></label>
                </div>
                <div class="form-group">
                    <label>To: <span class="auto-generated"></span></label>
                </div>
                <div class="form-group">
                    <label>Departure: <span class="auto-generated"></span></label>
                </div>
                <div class="form-group">
                    <label>Arrival: <span class="auto-generated"></span></label>
                </div>
            </div>
            <div class="form-group">
                <div class="id-verification">
                    <label>ID Verification:</label>
                    <div class="file-upload">
                        <input type="file" id="idFile" name="idFile" accept="image/*" style="display: none;">
                        <button type="button" class="action-button" onclick="document.getElementById('idFile').click()">Upload ID</button>
                        <span id="fileNameDisplay">No file selected</span>
                    </div>
                    <div class="verification-note">
                        <small>*Required for PWD/Senior/Student discounts</small>
                    </div>
                </div>
            </div>
            <div class="pricing-container">
                <h3>Pricing Details</h3>
                <div class="form-group">
                    <label>Base Price: <span id="basePrice" class="auto-generated">PHP 0.00</span></label>
                </div>
                <div class="form-group">
                    <label>Discount: <span id="discount" class="auto-generated">PHP 0.00</span></label>
                </div>
                <div class="form-group">
                    <label>Total Price: <span id="totalPrice" class="auto-generated">PHP 0.00</span></label>
                </div>
            </div>
            <div class="form-group">
                <label for="remarks">Remarks:</label>
                <input type="text" id="remarks" placeholder="Remarks">
            </div>
            <div class="action-buttons">
                <button type="button" class="print-button" onclick="processBooking()">Confirm & Print Ticket</button>
                <button type="button" class="cancel-button" onclick="cancelBooking()">Cancel</button>
            </div>
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
            slides.forEach((slide, index) => slide.classList.toggle('active', index === currentSlide));
            indicators.forEach((indicator, index) => indicator.classList.toggle('active', index === currentSlide));
        }
        function startAutoSlide() {
            slideInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }, 5000);
        }
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
        sliderContainer.addEventListener('mouseleave', startAutoSlide);
        startAutoSlide();
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => changeSlide(index));
        });
    </script>
    <script src="../js/ticket_form.js"></script>
    <script src="../js/seats.js"></script>
</body>
</html>