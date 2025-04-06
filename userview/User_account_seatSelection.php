
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/User_account_seatSelection.css">
    <title>HopStop - Seat Selection</title>
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
                 <i class ="fas fa-user-circle"></i>
              </div>
       
              <div class="profile-dropdown" id="profileDropdown">
                  <div class="dropdown-item">Profile</div>
                  <div class="dropdown-item">Logout</div>
              </div>
          </div>
          <script src="../js/Userlogout.js"></script>
    </header>

    <div class="container4">
        <div class="seat-selection-card">
            <div class="legend">
                <div class="legend-item">
                    <div class="color-box available"></div>
                    <span>Available</span>
                </div>
                <div class="legend-item">
                    <div class="color-box unavailable"></div>
                    <span>Unavailable</span>
                </div>
                <div class="legend-item">
                    <div class="color-box selected"></div>
                    <span>Selected</span>
                </div>
            </div>

            <div class="seat-grid" id="seatGrid">
                <!-- Seats will be populated by JavaScript -->
            </div>

            <div class="action-button" id="confirmButton">
                Confirm
            </div>
        </div>
    </div>

    <!-- Popup markup -->
    <div class="popup-overlay" id="confirmationPopup">
        <div class="popup-content">
            <div class="popup-title">Confirm Your Selection</div>
            <div class="popup-message">You're about to reserve the following seats:</div>
            <div class="popup-seats" id="selectedSeatsList">
                <!-- Selected seats will be displayed here -->
            </div>
            <div class="popup-buttons">
                <button class="popup-button cancel" id="cancelButton">Cancel</button>
                <button class="popup-button confirm" id="proceedButton">Proceed</button>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const seatGrid = document.getElementById('seatGrid');
            const confirmButton = document.getElementById('confirmButton');
            const confirmationPopup = document.getElementById('confirmationPopup');
            const selectedSeatsList = document.getElementById('selectedSeatsList');
            const cancelButton = document.getElementById('cancelButton');
            const proceedButton = document.getElementById('proceedButton');
            
            // Seat data structure (1-based index as shown in UI)
            // 0 = empty, 1 = available, 2 = unavailable
            const seatMap = [
                [1, 2, 0, 3, 4],  // Row 1
                [5, 6, 0, 7, 8],  // Row 2
                [9, 10, 0, 11, 12],  // Row 3
                [13, 14, 0, 15, 16],  // Row 4
                [17, 18, 0, 19, 20],  // Row 5
                [21, 22, 0, 23, 24],  // Row 6
                [25, 26, 0, 27, 28],  // Row 7
                [29, 30, 0, 31, 32],  // Row 8
                [33, 34, 0, 35, 36],  // Row 9
                [37, 38, 0, 39, 40],  // Row 10
                [41, 42, 0, 43, 44],  // Row 11
                [45, 46, 0, 47, 48, 49]  // Row 12
            ];

            // Unavailable seats (numbers as shown in the UI)
            const unavailableSeats = [3, 15, 23, 24, 48, 5, 7, 13, 35, 45];
            
            // Selected seats (initially empty, will be updated as user selects)
            let selectedSeats = [];

            // Create seats in the grid
            seatMap.forEach(row => {
                row.forEach(seatNumber => {
                    const seatElement = document.createElement('div');
                    
                    if (seatNumber === 0) {
                        // Empty space
                        seatElement.className = 'seat empty';
                    } else if (unavailableSeats.includes(seatNumber)) {
                        // Unavailable seat
                        seatElement.className = 'seat unavailable';
                        seatElement.textContent = seatNumber;
                    } else {
                        // Available seat
                        seatElement.className = 'seat available';
                        seatElement.textContent = seatNumber;
                        seatElement.dataset.seatNumber = seatNumber;
                        
                        // Add click event for seat selection
                        seatElement.addEventListener('click', () => {
                            toggleSeatSelection(seatElement, seatNumber);
                        });
                    }
                    
                    seatGrid.appendChild(seatElement);
                });
            });

            // Function to toggle seat selection
            function toggleSeatSelection(seatElement, seatNumber) {
                if (seatElement.classList.contains('selected')) {
                    // Deselect the seat
                    seatElement.classList.remove('selected');
                    selectedSeats = selectedSeats.filter(seat => seat !== seatNumber);
                } else {
                    // Select the seat
                    seatElement.classList.add('selected');
                    selectedSeats.push(seatNumber);
                }
                
                // Update the confirm button text based on selection
                updateConfirmButton();
            }

            // Update the confirm button based on seat selection
            function updateConfirmButton() {
                if (selectedSeats.length > 0) {
                    confirmButton.textContent = 'Confirm';
                    confirmButton.style.cursor = 'pointer';
                } else {
                    confirmButton.innerHTML = 'Confirm';
                }
            }

            // Show popup with selected seats
            function showPopup() {
                if (selectedSeats.length === 0) return;
                
                // Clear previous seat displays
                selectedSeatsList.innerHTML = '';
                
                // Sort seats numerically for better display
                selectedSeats.sort((a, b) => a - b);
                
                // Add each selected seat
                selectedSeats.forEach(seatNumber => {
                    const seatElement = document.createElement('div');
                    seatElement.className = 'popup-seat';
                    seatElement.textContent = seatNumber;
                    selectedSeatsList.appendChild(seatElement);
                });
                
                // Show the popup
                confirmationPopup.classList.add('active');
            }

            // Hide popup
            function hidePopup() {
                confirmationPopup.classList.remove('active');
            }

            // Complete the booking
            function completeBooking() {
                // In a real app, this would send the selection to a server
                alert(`Booking confirmed for seats: ${selectedSeats.join(', ')}!`);
                
                // Reset selection
                document.querySelectorAll('.seat.selected').forEach(seat => {
                    seat.classList.remove('selected');
                });
                selectedSeats = [];
                updateConfirmButton();
                
                // Hide popup
                hidePopup();
            }

            // Add click event for the confirm button
            confirmButton.addEventListener('click', () => {
                if (selectedSeats.length > 0) {
                    showPopup();
                }
            });

            // Cancel button event
            cancelButton.addEventListener('click', hidePopup);

            // Proceed button event
            proceedButton.addEventListener('click', completeBooking);
        });
    </script>
</body>
</html>