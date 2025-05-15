document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown toggle
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
            console.log('Profile dropdown toggled');
        });
        window.addEventListener('click', function(event) {
            if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('show');
                console.log('Profile dropdown closed');
            }
        });
    } else {
        console.error('Profile button or dropdown not found');
    }

    // Seat selection modal
    const seatModal = document.getElementById('seat-modal');
    const closeSeatModal = document.getElementById('close-seat-modal');
    const selectSeatBtn = document.getElementById('select-seat-btn');
    const seatNumberInput = document.getElementById('seat-number');
    const ticketForm = document.getElementById('ticket-form');

    // Log element presence for debugging
    console.log('Seat modal:', !!seatModal);
    console.log('Close seat modal:', !!closeSeatModal);
    console.log('Select seat button:', !!selectSeatBtn);
    console.log('Seat number input:', !!seatNumberInput);
    console.log('Ticket form:', !!ticketForm);

    // Get URL parameters
    const urlParams = new URLSearchParams(window.location.search);
    console.log('URL Parameters:', Object.fromEntries(urlParams));
    let busId = urlParams.get('bus_id') || urlParams.get('id');

    // Require bus_id; redirect if missing
    if (!busId) {
        console.error('No bus_id or id parameter found in URL:', window.location.href);
        alert('No bus selected. Please choose a bus.');
        window.location.href = 'User.php';
        return;
    }

    // If seat number is provided, populate the field
    const seatNumber = urlParams.get('seat');
    if (seatNumber && seatNumberInput) {
        seatNumberInput.value = seatNumber;
    } else if (seatNumber) {
        console.warn('Seat number input not found');
    }

    // Set up event listeners
    const idFileInput = document.getElementById('idFile');
    if (idFileInput) {
        idFileInput.addEventListener('change', updateFileName);
    } else {
        console.warn('ID file input not found');
    }

    // Passenger type radio buttons for discount
    const passengerTypeRadios = document.querySelectorAll('input[name="passengerType"]');
    passengerTypeRadios.forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    // Seat selection button
    if (selectSeatBtn) {
        selectSeatBtn.addEventListener('click', () => {
            console.log('Select seat button clicked');
            openSeatModal(busId);
        });
    } else {
        console.error('Select seat button not found; cannot attach event listener');
    }

    // Close seat modal
    if (closeSeatModal && seatModal) {
        closeSeatModal.addEventListener('click', () => {
            console.log('Closing seat modal');
            seatModal.classList.remove('show');
        });
    } else {
        console.warn('Close seat modal or modal not found');
    }

    // Hide seat modal initially
    if (seatModal) {
        seatModal.classList.remove('show');
        console.log('Seat modal hidden initially');
    } else {
        console.error('Seat modal not found');
    }

    // Fetch bus details
    getBusDetails(busId);
});

function openSeatModal(busId) {
    console.log('Opening seat modal for bus ID:', busId);
    const seatModal = document.getElementById('seat-modal');
    const seatMap = document.getElementById('seat-map');
    if (!seatModal || !seatMap) {
        console.error('Seat modal or seat map not found');
        alert('Error: Seat selection interface not found.');
        return;
    }

    // Show modal and display loading message
    seatModal.classList.add('show');
    console.log('Set seat modal to show');
    seatMap.innerHTML = '<p>Loading seats...</p>';

    // Fetch available seats
    fetch(`../api/booking_api.php?bus_id=${busId}`)
        .then(response => {
            console.log('Seat API Response status:', response.status);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log('Seat API Response:', data);
            if (data.success) {
                createSeatGrid(seatMap, data.available_seats || []);
            } else {
                seatMap.innerHTML = '<p>Error fetching seats.</p>';
                alert(data.message || 'Error fetching seats');
            }
        })
        .catch(error => {
            console.error('Error fetching seats:', error);
            seatMap.innerHTML = '<p>Failed to load seat data.</p>';
            alert('Failed to fetch seat data. Please try again.');
        });
}

function createSeatGrid(seatMap, availableSeats) {
    console.log('Creating seat grid with available seats:', availableSeats);
    seatMap.innerHTML = '';

    // Define seat layout: 3 columns (A, B, C) x 10 rows = 30 seats
    const totalRows = 10;
    const columns = ['A', 'B', 'C'];
    const allSeats = [];
    for (let row = 1; row <= totalRows; row++) {
        for (let col of columns) {
            allSeats.push(`${row}${col}`);
        }
    }

    // Create grid container
    const grid = document.createElement('div');
    grid.style.display = 'grid';
    grid.style.gridTemplateColumns = 'repeat(4, 50px)';
    grid.style.gap = '5px';
    grid.style.padding = '10px';
    grid.style.justifyContent = 'center';

    // Add column labels
    grid.innerHTML = `
        <span style="text-align: center; font-weight: bold;"></span>
        <span style="text-align: center; font-weight: bold;">A</span>
        <span style="text-align: center; font-weight: bold;">B</span>
        <span style="text-align: center; font-weight: bold;">C</span>
    `;

    // Generate seat buttons
    let seatIndex = 0;
    for (let row = 1; row <= totalRows; row++) {
        // Add row number
        const rowLabel = document.createElement('span');
        rowLabel.textContent = row;
        rowLabel.style.textAlign = 'center';
        rowLabel.style.fontWeight = 'bold';
        grid.appendChild(rowLabel);

        // Add seats for each column
        for (let col of columns) {
            const seatId = allSeats[seatIndex];
            const isAvailable = availableSeats.includes(seatId);
            const seatBtn = document.createElement('button');
            seatBtn.type = 'button';
            seatBtn.textContent = seatId;
            seatBtn.classList.add('seat-button');
            if (isAvailable) {
                seatBtn.style.backgroundColor = '#4caf50';
                seatBtn.style.cursor = 'pointer';
                seatBtn.addEventListener('click', () => {
                    const seatInput = document.getElementById('seat-number');
                    if (seatInput) {
                        seatInput.value = seatId;
                        console.log(`Selected seat: ${seatId}`);
                    } else {
                        console.warn('Seat number input not found');
                    }
                    const seatModal = document.getElementById('seat-modal');
                    if (seatModal) {
                        seatModal.classList.remove('show');
                        console.log('Closed seat modal after selection');
                    }
                });
            } else {
                seatBtn.style.backgroundColor = '#ccc';
                seatBtn.style.cursor = 'not-allowed';
                seatBtn.disabled = true;
            }
            grid.appendChild(seatBtn);
            seatIndex++;
        }
    }

    seatMap.appendChild(grid);

    if (availableSeats.length === 0) {
        seatMap.innerHTML = '<p>No seats available for this bus.</p>';
    }
}

function getBusDetails(busId) {
    console.log("Fetching bus details for ID:", busId);
    fetch(`../api/bus_api.php?id=${busId}`)
        .then(response => {
            console.log("API Response status:", response.status);
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            console.log("API Response data:", data);
            if (data.status === "success" && data.bus) {
                console.log("Bus details received:", data.bus);
                displayBusDetails(data.bus);
            } else {
                throw new Error(data.message || "Bus not found");
            }
        })
        .catch(error => {
            console.error("Error fetching bus details:", error);
            const tripInfoContainer = document.getElementById("trip-info");
            if (tripInfoContainer) {
                tripInfoContainer.innerHTML = `<p class="error-message">Failed to load bus data: ${error.message}</p>`;
            }
            setTimeout(() => {
                alert('Invalid bus selection. Redirecting to home.');
                window.location.href = 'User.php';
            }, 2000);
        });
}

function displayBusDetails(bus) {
    if (!bus) {
        console.error("No bus data received");
        return;
    }

    const tripInfoContainer = document.getElementById("trip-info");
    if (!tripInfoContainer) {
        console.error("Trip info container not found");
        return;
    }

    const spans = tripInfoContainer.querySelectorAll('.auto-generated');
    if (spans.length >= 5) {
        spans[0].textContent = bus.bus_number || 'N/A';
        spans[1].textContent = bus.location || 'N/A';
        spans[2].textContent = bus.destination || 'N/A';
        spans[3].textContent = formatTime(bus.departure_time) || 'N/A';
        spans[4].textContent = formatTime(bus.arrival_time) || 'N/A';
    } else {
        console.error("Not enough auto-generated spans found");
    }

    const price = parseFloat(bus.price) || 0;
    const basePriceElement = document.getElementById('basePrice');
    if (basePriceElement) {
        basePriceElement.textContent = 'PHP ' + price.toFixed(2);
        basePriceElement.setAttribute('data-price', price);
    } else {
        console.warn('Base price element not found');
    }

    calculatePrice();
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    try {
        const date = new Date(`2000-01-01T${timeString}`);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        console.error('Error formatting time:', e);
        return timeString;
    }
}

function updateFileName() {
    const fileInput = document.getElementById('idFile');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    if (fileInput && fileNameDisplay) {
        fileNameDisplay.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file selected';
    } else {
        console.warn('File input or display not found');
    }
}

function calculatePrice() {
    const basePriceElement = document.getElementById('basePrice');
    if (!basePriceElement) {
        console.warn('Base price element not found');
        return;
    }

    const basePrice = parseFloat(basePriceElement.getAttribute('data-price') || 0);
    let discount = 0;

    const passengerType = document.querySelector('input[name="passengerType"]:checked')?.value;
    if (passengerType === 'pwd' || passengerType === 'student') {
        discount = basePrice * 0.2;
    }

    const totalPrice = basePrice - discount;
    const discountElement = document.getElementById('discount');
    const totalPriceElement = document.getElementById('totalPrice');
    if (discountElement) {
        discountElement.textContent = 'PHP ' + discount.toFixed(2);
    } else {
        console.warn('Discount element not found');
    }
    if (totalPriceElement) {
        totalPriceElement.textContent = 'PHP ' + totalPrice.toFixed(2);
    } else {
        console.warn('Total price element not found');
    }
}

function processBooking() {
    const name = document.getElementById('name')?.value;
    const selectedSeats = document.getElementById('seat-number')?.value;
    const passengerType = document.querySelector('input[name="passengerType"]:checked')?.value;
    const remarks = document.getElementById('remarks')?.value;
    const busId = document.querySelector('#trip-info .auto-generated')?.textContent;

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
    if (!busId) {
        alert('Bus details not loaded');
        return;
    }

    const idFile = document.getElementById('idFile')?.files[0];
    if ((passengerType === 'pwd' || passengerType === 'student') && !idFile) {
        alert('Please upload ID for discount verification');
        return;
    }

    fetch('../api/store_session.php')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success' && data.passengerId) {
                const passengerId = data.passengerId;
                submitBooking(passengerId, busId, name, passengerType, selectedSeats, remarks, idFile);
            } else {
                throw new Error('User not authenticated');
            }
        })
        .catch(error => {
            console.error('Error fetching session:', error);
            alert('Please log in to create a booking.');
            window.location.href = 'User.php';
        });
}

function submitBooking(passengerId, busId, name, passengerType, selectedSeats, remarks, idFile) {
    const formData = new FormData();
    formData.append('passenger_id', passengerId);
    formData.append('bus_id', busId);
    formData.append('reserve_name', name);
    formData.append('passenger_type', passengerType);
    formData.append('seat_number', selectedSeats);
    formData.append('remarks', remarks || '');
    if (idFile) {
        formData.append('id_file', idFile);
    }

    fetch('../api/booking_api.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Booking successful! Reference: ' + data.reference);
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