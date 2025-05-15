document.addEventListener('DOMContentLoaded', function() {
    // Set up profile dropdown toggle
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');

    //SEAT SELECTIO MODAL 
    const seatModal = document.getElementById('seat-modal');
    const closeSeatModal = document.getElementById('close-seat-modal');
    const selectSeatBtn = document.getElementById('select-seat-btn');
    const seatNumberInput = document.getElementById('seat-number');
    const ticketForm = document.getElementById('ticket-form');
    
    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function() {
            profileDropdown.classList.toggle('active');
        });
    }
    
    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    const busId = urlParams.get('bus_id');
    const seatNumber = urlParams.get('seat');
    
    // If seat number is provided in URL, populate the field
    if (seatNumber) {
        const seatNoInput = document.getElementById('seatNo');
        if (seatNoInput) {
            seatNoInput.value = seatNumber;
        }
    }
    
    // Set up event listeners
    const idFileInput = document.getElementById('idFile');
    if (idFileInput) {
        idFileInput.addEventListener('change', updateFileName);
    }
    
    // Set up passenger type radio buttons to calculate discount
    const passengerTypeRadios = document.querySelectorAll('input[name="passengerType"]');
    passengerTypeRadios.forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    // If bus ID is provided, get specific bus details, otherwise get all buses
    if (busId) {
        getBusDetails(busId);
    } else {
        getAllBusDetails();
    }
});

function getBusDetails(busId) {
    console.log("Fetching bus details for ID:", busId);
    fetch(`../api/bus_api.php?bus_id=${busId}`)
        .then(response => {
            console.log("API Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("API Response data:", data);
            if (data.status === "success") {
                console.log("Bus details received:", data.bus);
                displayBusDetails([data.bus]); // Pass as array for consistency with getAllBusDetails
            } else {
                console.error("Error fetching bus details:", data.message);
                const busContainer = document.getElementById("trip-info");
                if (busContainer) {
                    busContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${data.message || "Unknown error"}</p>`;
                }
            }
        })
        .catch(error => {
            console.error("Error fetching bus details:", error);
            const busContainer = document.getElementById("trip-info");
            if (busContainer) {
                busContainer.innerHTML = `<p class="error-message">Failed to load bus data. Please try again later.</p>`;
            }
        });
}

function getAllBusDetails() {
    console.log("Fetching all buses...");
    fetch("../api/bus_api.php")
        .then(response => {
            console.log("API Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("API Response data:", data);
            if (data.status === "success") {
                console.log("Buses received:", data.buses);
                displayBusDetails(data.buses);
            } else {
                console.error("Error fetching buses:", data.message);
                const busContainer = document.getElementById("trip-info");
                if (busContainer) {
                    busContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${data.message || "Unknown error"}</p>`;
                }
            }
        })
        .catch(error => {
            console.error("Error fetching buses:", error);
            const busContainer = document.getElementById("trip-info");
            if (busContainer) {
                busContainer.innerHTML = `<p class="error-message">Failed to load bus data. Please try again later.</p>`;
            }
        });
}

function displayBusDetails(buses) {
    if (!buses || buses.length === 0) {
        console.error("No bus data received");
        return;
    }
    
    // Use the first bus in the list (or the specific bus requested)
    const bus = buses[0];
    
    // Get bus details container
    const tripInfoContainer = document.getElementById("trip-info");
    if (!tripInfoContainer) {
        console.error("Trip info container not found");
        return;
    }
    
    // Create the bus details container
    const busDetails = document.createElement("div");
    busDetails.innerHTML = `
        <h3>Trip Information</h3>
        <div class="form-group">
            <label>Bus Number: <span class="auto-generated" id="busId">${bus.bus_number}</span></label>
        </div>
        <div class="form-group">
            <label>From: <span class="auto-generated">${bus.location || 'N/A'}</span></label>
        </div>
        <div class="form-group">
            <label>To: <span class="auto-generated">${bus.destination || 'N/A'}</span></label>
        </div>
        <div class="form-group">
            <label>Departure: <span class="auto-generated">${formatTime(bus.departure_time) || 'N/A'}</span></label>
        </div>
        <div class="form-group">
            <label>Arrival: <span class="auto-generated">${formatTime(bus.arrival_time) || 'N/A'}</span></label>
        </div>
    `;
    
    // Replace the existing content with the new bus details
    tripInfoContainer.innerHTML = '';
    tripInfoContainer.appendChild(busDetails);
    
    // Set base price
    const price = parseFloat(bus.price) || 0;
    document.getElementById('basePrice').textContent = 'PHP ' + price.toFixed(2);
    
    // Store price as data attribute for calculations
    document.getElementById('basePrice').setAttribute('data-price', price);
    
    // Initialize price calculations
    calculatePrice();
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    try {
        const date = new Date(`2000-01-01T${timeString}`);
        return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    } catch (e) {
        console.error('Error formatting time:', e);
        return timeString; // Return original string if formatting fails
    }
}

function updateFileName() {
    const fileInput = document.getElementById('idFile');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    
    if (fileInput.files.length > 0) {
        fileNameDisplay.textContent = fileInput.files[0].name;
    } else {
        fileNameDisplay.textContent = 'No file selected';
    }
}

function calculatePrice() {
    const basePriceElement = document.getElementById('basePrice');
    if (!basePriceElement) return;
    
    const basePrice = parseFloat(basePriceElement.getAttribute('data-price') || 0);
    let discount = 0;
    
    // Check passenger type for discount
    const passengerType = document.querySelector('input[name="passengerType"]:checked')?.value;
    
    if (passengerType === 'pwd' || passengerType === 'student') {
        // 20% discount for PWD/Senior/Student
        discount = basePrice * 0.2;
    }
    
    const totalPrice = basePrice - discount;
    
    // Update price displays
    document.getElementById('discount').textContent = 'PHP ' + discount.toFixed(2);
    document.getElementById('totalPrice').textContent = 'PHP ' + totalPrice.toFixed(2);
}

function redirectToSeatSelection() {
    const busIdElement = document.getElementById('busId');
    if (!busIdElement || !busIdElement.textContent) {
        alert('Please wait for bus details to load');
        return;
    }
    window.location.href = `User_account_seatSelection.php?bus_id=${busIdElement.textContent}`;
}

function processBooking() {
    // Validate form
    const name = document.getElementById('name').value;
    const selectedSeats = document.getElementById('seatNo').value;
    const passengerType = document.querySelector('input[name="passengerType"]:checked')?.value;
    const remarks = document.getElementById('remarks').value;
    
    if (!name) {
        alert('Please enter passenger name');
        return;
    }
    
    if (!passengerType) {
        alert('Please select passenger type');
        return;
    }
    
    if (!selectedSeats) {
        alert('Please select a seat');
        return;
    }
    
    // Check if ID upload is required for discounted passengers
    const idFile = document.getElementById('idFile').files[0];
    if ((passengerType === 'pwd' || passengerType === 'student') && !idFile) {
        alert('Please upload ID for discount verification');
        return;
    }

    // Get passenger ID from session (this would be set during login)
    // For this example, we'll use a placeholder
    const passengerId = 1; // Replace with actual passenger ID from session
    
    // Create FormData object for file upload
    const formData = new FormData();
    formData.append('passenger_id', passengerId);
    formData.append('bus_id', document.getElementById('busId').textContent);
    formData.append('reserve_name', name);
    formData.append('passenger_type', passengerType);
    formData.append('seat_number', selectedSeats);
    formData.append('remarks', remarks || '');
    
    if (idFile) {
        formData.append('id_file', idFile);
    }
    
    // Submit booking via API
    fetch('../api/booking_api.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            alert('Booking successful! Reference: ' + data.reference);
            // Redirect to ticket view/print page
            window.location.href = `ticket_view.php?id=${data.booking_id}`;
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to create booking. Please try again.');
    });
}

function cancelBooking() {
    if (confirm('Are you sure you want to cancel this booking?')) {
        window.location.href = 'User.php';
    }
}

// Function to fetch and display available seats
async function getAllSBusDetails(busId) {
    if (selectSeatBtn) {
        selectSeatBtn.addEventListener('click', function() {
            fetchBusDetails(busId);
        });
    }
    
    // Close modal when close button is clicked
    if (closeSeatModal) {
        closeSeatModal.addEventListener('click', function() {
            seatModal.style.display = 'none';
        });
    }
    try {
        const response = await fetch(`../api/booking_api.php?bus_id=${busId}`);
        const data = await response.json();

        if (data.success && data.available_seats) {
            const seatMap = document.getElementById('seat-map');
            if (seatMap) {
                seatMap.innerHTML = '';

                if (data.available_seats.length === 0) {
                    seatMap.textContent = "No seats available.";
                } else {
                    data.available_seats.forEach(seat => {
                        const seatBtn = document.createElement('button');
                        seatBtn.type = 'button';
                        seatBtn.textContent = seat;
                        seatBtn.classList.add('seat-button');
                        seatBtn.addEventListener('click', () => {
                            if (seatNumberInput) {
                                seatNumberInput.value = seat;
                            }
                            if (seatModal) {
                                seatModal.style.display = 'none';
                            }
                        });
                        seatMap.appendChild(seatBtn);
                    });
                }
            }
            
            if (seatModal) {
                seatModal.style.display = 'flex';
            }
        } else {
            alert(data.message || 'Error fetching seats');
        }
    } catch (err) {
        console.error(err);
        alert('Failed to fetch seat data.');
    }
}

