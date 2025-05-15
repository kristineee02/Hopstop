<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/User_account_ticket_form.css">
    <script src="../js/Userlogout.js"></script>
    <script src="https://kit.fontawesome.com/yourcode.js" crossorigin="anonymous"></script>
    <title>HopStop - Ticket Form</title>
    <style>
        /* Modal container */
.modal {
  position: fixed;
  z-index: 1000;
  left: 0;
  top: 0;
  width: 100%;
  height: 100%;
  overflow: auto;
  background-color: rgba(0,0,0,0.5); /* semi-transparent black background */
  display: flex;
  justify-content: center;
  align-items: center;
}

/* Modal content box */
.modal-content {
  background-color: #fff;
  padding: 20px 30px;
  border-radius: 8px;
  width: 90%;
  max-width: 400px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  position: relative;
}

/* Close button (x) */
.close {
  position: absolute;
  top: 12px;
  right: 15px;
  font-size: 24px;
  font-weight: bold;
  color: #555;
  cursor: pointer;
}

.close:hover {
  color: #000;
}

/* Seat map container */
#seat-map {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-top: 15px;
  max-height: 300px;
  overflow-y: auto;
  justify-content: center;
}

/* Individual seat button */
.seat-button {
  background-color: #4caf50;
  border: none;
  color: white;
  padding: 12px 16px;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.2s ease;
  min-width: 45px;
  text-align: center;
  user-select: none;
}

.seat-button:hover {
  background-color: #45a049;
}

/* Seat button disabled style (optional) */
/* .seat-button.booked {
  background-color: #ccc;
  cursor: not-allowed;
} */

/* Ticket form styling */
#ticket-form-section {
  max-width: 500px;
  margin: 20px auto;
  padding: 15px;
  border: 1px solid #ddd;
  border-radius: 8px;
  background-color: #fafafa;
}

#ticket-form div {
  margin-bottom: 15px;
}

#ticket-form label {
  display: block;
  margin-bottom: 6px;
  font-weight: 600;
  color: #333;
}

#ticket-form input[type="text"],
#ticket-form select,
#ticket-form textarea,
#ticket-form input[type="file"] {
  width: 100%;
  padding: 8px 10px;
  border: 1px solid #ccc;
  border-radius: 5px;
  font-size: 14px;
  box-sizing: border-box;
}

#ticket-form button[type="submit"],
#select-seat-btn {
  background-color: #007bff;
  color: white;
  border: none;
  padding: 10px 16px;
  font-size: 16px;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.25s ease;
}

#ticket-form button[type="submit"]:hover,
#select-seat-btn:hover {
  background-color: #0056b3;
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
        <!-- Image Slider -->
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

        <!-- Ticket Form -->
<div class="ticket-form">
    <!-- Primary Passenger Details -->
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
                    <input type="radio" id="regular" name="passengerType" value="regular">
                    <label for="regular">Regular</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="pwd" name="passengerType" value="pwd">
                    <label for="pwd">PWD/Senior</label>
                </div>
                <div class="radio-option">
                    <input type="radio" id="student" name="passengerType" value="student">
                    <label for="student">Student</label>
                </div>
            </div>
        </div>
    </div>
    
       <!-- Seat selection modal -->
       <div id="seat-modal">
      <div class="modal-content">
        <span class="close" id="close-seat-modal">&times;</span>
        <h3>Select a Seat</h3>
        <div id="seat-map"></div>
      </div>
    </div>

    <!-- Trip Information -->
    <div class="trip-info-container" id="trip-info">
        <h3>Trip Information</h3>
        <div class="form-group">
            <label>Bus ID: <span class="auto-generated"></span></label>
        </div>

        <div class="form-group">
            <label>From: <span  class="auto-generated"></span></label>
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

    <!-- ID Verification -->
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

    <!-- Pricing Information -->
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

    <!-- Action Buttons -->
    <div class="action-buttons">
        <button type="button" class="print-button" onclick="processBooking()">Confirm & Print Ticket</button>
        <button type="button" class="cancel-button" onclick="cancelBooking()">Cancel</button>
    </div>
</div>

    <!-- Footer -->
    <div class="footer"></div>

    <script>
        // Image slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('.slider-img');
        const indicators = document.querySelectorAll('.indicator');
        const totalSlides = slides.length;
        let slideInterval;

        // Function to change slide
        function changeSlide(index) {
            currentSlide = index;
            updateSlider();
        }

        // Update the slider to show the current slide
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

        // Auto-slide functionality
        function startAutoSlide() {
            slideInterval = setInterval(() => {
                currentSlide = (currentSlide + 1) % totalSlides;
                updateSlider();
            }, 5000); // Change slide every 5 seconds
        }

        // Pause auto-slide on hover
        const sliderContainer = document.querySelector('.slider-container');
        sliderContainer.addEventListener('mouseenter', () => {
            clearInterval(slideInterval);
        });

        // Resume auto-slide when mouse leaves
        sliderContainer.addEventListener('mouseleave', () => {
            startAutoSlide();
        });

        // Start the auto-slider
        startAutoSlide();

        // Manual slide change with indicators
        indicators.forEach((indicator, index) => {
            indicator.addEventListener('click', () => {
                changeSlide(index);
            });
        });
    </script>

<script src="../js/ticket_form.js"></script>


</body>
</html>