<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style/seat.css">
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
                <i class="fas fa-user-circle"></i>
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
            const API_URL = '../api/seat_api.php';

            // Selected seats array
            let selectedSeats = [];

            // Initialize seats
            initializeSeats();

            // Function to initialize seats via API if needed
            async function initializeSeats() {
                try {
                    // First try to initialize seats if not already done
                    const initResponse = await fetch(`${API_URL}?action=initialize`);
                    await initResponse.json(); // We don't need to use the response

                    // Then load the seat map
                    loadSeatMap();
                } catch (error) {
                    console.error('Error initializing seats:', error);
                    // Fallback to static data if API fails
                    loadStaticSeatMap();
                }
            }

            // Function to load seat map from API

            async function loadSeatMap() {
                try {
                    const res = await fetch(`${API_URL}?action=seatmap`);
                    const data = await res.json();

                    const map = data.data.seatMap;
                    const unavailable = data.data.unavailableSeats.map(String); // Ensure strings for comparison

                    seatGrid.innerHTML = ''; // Clear previous grid if any

                    map.forEach(row => {
                        const rowDiv = document.createElement('div');
                        rowDiv.classList.add('seat-row');

                        row.forEach(seatNum => {
                            const seatDiv = document.createElement('div');

                            if (seatNum === 0) {
                                seatDiv.classList.add('empty-space');
                            } else {
                                seatDiv.classList.add('seat');
                                seatDiv.textContent = seatNum;

                                if (unavailable.includes(String(seatNum))) {
                                    seatDiv.classList.add('unavailable');
                                } else {
                                    seatDiv.classList.add('available');

                                    seatDiv.addEventListener('click', () => {
                                        const index = selectedSeats.indexOf(seatNum);
                                        if (index > -1) {
                                            selectedSeats.splice(index, 1);
                                            seatDiv.classList.remove('selected');
                                        } else {
                                            selectedSeats.push(seatNum);
                                            seatDiv.classList.add('selected');
                                        }
                                    });
                                }
                            }

                            rowDiv.appendChild(seatDiv);
                        });

                        seatGrid.appendChild(rowDiv);
                    });
                } catch (err) {
                    console.error('Error loading seat map:', err);
                }
            }


            // Function to load static seat map if API fails
            function loadStaticSeatMap() {
                // Seat data structure (1-based index as shown in UI)
                // 0 = empty, 1 = available, 2 = unavailable
                const seatMap = [
                    [1, 2, 0, 3, 4], // Row 1
                    [5, 6, 0, 7, 8], // Row 2
                    [9, 10, 0, 11, 12], // Row 3
                    [13, 14, 0, 15, 16], // Row 4
                    [17, 18, 0, 19, 20], // Row 5
                    [21, 22, 0, 23, 24], // Row 6
                    [25, 26, 0, 27, 28], // Row 7
                    [29, 30, 0, 31, 32], // Row 8
                    [33, 34, 0, 35, 36], // Row 9
                    [37, 38, 0, 39, 40], // Row 10
                    [41, 42, 0, 43, 44], // Row 11
                    [45, 46, 0, 47, 48] // Row 12
                ];

                // Unavailable seats (numbers as shown in the UI)
                const unavailableSeats = [];

                renderSeatMap(seatMap, unavailableSeats);
            }

            // Function to render the seat map
            function renderSeatMap(seatMap, unavailableSeats) {
                // Clear existing seats
                seatGrid.innerHTML = '';

                // Create seats in the grid
                seatMap.forEach(row => {
                    // Create row container
                    const rowDiv = document.createElement('div');
                    rowDiv.className = 'seat-row';

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

                        rowDiv.appendChild(seatElement);
                    });

                    seatGrid.appendChild(rowDiv);
                });
            }

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
                    confirmButton.classList.add('active');
                } else {
                    confirmButton.classList.remove('active');
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
            async function completeBooking() {
                try {
                    // Get user ID (in a real app, this would come from user session)
                    const userId = getUserId() || 1;

                    // Call API to book seats
                    const response = await fetch(API_URL, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            action: 'book',
                            seats: selectedSeats.map(String), // Convert numbers to strings
                            user_id: userId
                        })
                    });

                    const result = await response.json();

                    if (result.status === 'success') {
                        alert(`Booking confirmed for seats: ${selectedSeats.join(', ')}!`);

                        // Reset selection and reload seat map
                        selectedSeats = [];
                        hidePopup();
                        loadSeatMap();
                    } else {
                        alert(`Booking failed: ${result.message}`);
                        hidePopup();
                    }
                } catch (error) {
                    console.error('Error booking seats:', error);
                    alert('Error booking seats. Please try again.');
                    hidePopup();
                }
            }

            // Get user ID from session or localStorage
            function getUserId() {
                // In a real app, this would get the user ID from session/localStorage
                // For demo, just return null and let the API use default
                return null;
            }

            // Add click event for the confirm button
            confirmButton.addEventListener('click', () => {
                if (selectedSeats.length > 0) {
                    selectedSeatsList.innerHTML = selectedSeats.join(', ');
                    confirmationPopup.style.display = 'flex';
                }
            });

            cancelButton.addEventListener('click', () => {
                confirmationPopup.style.display = 'none';
            });

            proceedButton.addEventListener('click', async() => {
                const user_id = 1; // Replace this with the logged-in user's ID

                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        action: 'book',
                        seats: selectedSeats,
                        user_id: user_id
                    })
                });

                const result = await response.json();
                if (result.status === 'success') {
                    alert(result.message);
                    selectedSeats = [];
                    confirmationPopup.style.display = 'none';
                    loadSeatMap(); // Refresh seats
                } else {
                    alert(result.message);
                }
            });
        });
    </script>
</body>

</html>