document.addEventListener('DOMContentLoaded', function() {
    const profileButton = document.getElementById('profileButton');
    const profileDropdown = document.getElementById('profileDropdown');
    if (profileButton && profileDropdown) {
        profileButton.addEventListener('click', function(event) {
            event.stopPropagation();
            profileDropdown.classList.toggle('show');
        });
        window.addEventListener('click', function(event) {
            if (!profileButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.remove('show');
            }
        });
    }

    const urlParams = new URLSearchParams(window.location.search);
    const busId = urlParams.get('bus_id') || urlParams.get('id');
    if (!busId) {
        alert('No bus selected. Please choose a bus.');
        window.location.href = 'User.php';
        return;
    }

    const idFileInput = document.getElementById('idFile');
    if (idFileInput) {
        idFileInput.addEventListener('change', updateFileName);
    }

    const passengerTypeRadios = document.querySelectorAll('input[name="passengerType"]');
    passengerTypeRadios.forEach(radio => {
        radio.addEventListener('change', calculatePrice);
    });

    getBusDetails(busId);
});

function getBusDetails(busId) {
    fetch(`../api/bus_api.php?id=${busId}`)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.bus) {
                displayBusDetails(data.bus);
            } else {
                throw new Error(data.message || 'Bus not found');
            }
        })
        .catch(error => {
            console.error('Error fetching bus details:', error);
            const tripInfoContainer = document.getElementById('trip-info');
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
    if (!bus) return;
    const tripInfoContainer = document.getElementById('trip-info');
    if (!tripInfoContainer) return;
    const spans = tripInfoContainer.querySelectorAll('.auto-generated');
    if (spans.length >= 5) {
        spans[0].textContent = bus.bus_number || 'N/A';
        spans[1].textContent = bus.location || 'N/A';
        spans[2].textContent = bus.destination || 'N/A';
        spans[3].textContent = formatTime(bus.departure_time) || 'N/A';
        spans[4].textContent = formatTime(bus.arrival_time) || 'N/A';
    }
    const price = parseFloat(bus.price) || 0;
    const basePriceElement = document.getElementById('basePrice');
    if (basePriceElement) {
        basePriceElement.textContent = 'PHP ' + price.toFixed(2);
        basePriceElement.setAttribute('data-price', price);
    }
    // Store bus_id for booking
    tripInfoContainer.dataset.busId = bus.bus_id;
    calculatePrice();
}

function formatTime(timeString) {
    if (!timeString) return 'N/A';
    try {
        const date = new Date(`2000-01-01T${timeString}`);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return timeString;
    }
}

function updateFileName() {
    const fileInput = document.getElementById('idFile');
    const fileNameDisplay = document.getElementById('fileNameDisplay');
    if (fileInput && fileNameDisplay) {
        fileNameDisplay.textContent = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file selected';
    }
}

function calculatePrice() {
    const basePriceElement = document.getElementById('basePrice');
    if (!basePriceElement) return;
    const basePrice = parseFloat(basePriceElement.getAttribute('data-price') || 0);
    let discount = 0;
    const passengerType = document.querySelector('input[name="passengerType"]:checked')?.value;
    if (passengerType === 'PWD/Senior Citizen' || passengerType === 'Student') {
        discount = basePrice * 0.2;
    }
    const totalPrice = basePrice - discount;
    const discountElement = document.getElementById('discount');
    const totalPriceElement = document.getElementById('totalPrice');
    if (discountElement) {
        discountElement.textContent = 'PHP ' + discount.toFixed(2);
    }
    if (totalPriceElement) {
        totalPriceElement.textContent = 'PHP ' + totalPrice.toFixed(2);
    }
}

function processBooking() {
    const name = document.getElementById('name')?.value;
    const selectedSeat = document.getElementById('seat-number')?.value;
    const passengerType = document.querySelector('input[name="passengerType"]:checked')?.value;
    const remarks = document.getElementById('remarks')?.value;
    const tripInfoContainer = document.getElementById('trip-info');
    const busId = tripInfoContainer?.dataset.busId;

    if (!name) return alert('Please enter passenger name');
    if (!passengerType) return alert('Please select passenger type');
    if (!selectedSeat) return alert('Please select a seat');
    if (!busId) return alert('Bus details not loaded');

    const idFile = document.getElementById('idFile')?.files[0];
    if ((passengerType === 'PWD/Senior Citizen' || passengerType === 'Student') && !idFile) {
        return alert('Please upload ID for discount verification');
    }

    fetch('../api/store_session.php')
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            if (data.status === 'success' && data.passengerId) {
                createNewBooking(data.passengerId, busId, name, passengerType, selectedSeat, remarks, idFile);
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

function createNewBooking(passengerId, busId, name, passengerType, selectedSeat, remarks, idFile) {
    const formData = new FormData();
    formData.append('passenger_id', passengerId);
    formData.append('bus_id', busId);
    formData.append('reserve_name', name);
    formData.append('passenger_type', passengerType);
    formData.append('seat_number', selectedSeat);
    formData.append('remarks', remarks || '');
    if (idFile) {
        formData.append('id_file', idFile);
    }

    fetch('../api/booking_api.php', {
        method: 'POST',
        body: formData
    })
        .then(response => {
            console.log('Booking API response status:', response.status);
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(`Booking request failed: ${response.status} - ${data.message || 'Unknown error'}`);
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Booking API response data:', data);
            if (data.status === 'success' && data.booking_id && data.reference) {
                alert('Booking successful! Reference: ' + data.reference);
                window.location.href = `user_profile.php?id=${data.booking_id}`;
            } else {
                throw new Error(data.message || 'Invalid response from server');
            }
        })
        .catch(error => {
            console.error('Error creating booking:', error);
            alert('Failed to create booking: ' + error.message);
        });
}

function cancelBooking() {
    if (confirm('Are you sure you want to cancel this booking?')) {
        window.location.href = 'User.php';
    }
}