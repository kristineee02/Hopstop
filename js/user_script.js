function scrollLeft() {
    document.getElementById('slider').scrollBy({ left: -250, behavior: 'smooth' });
}

function scrollRight() {
    document.getElementById('slider').scrollBy({ left: 250, behavior: 'smooth' });
}

function toggleDropdown1() {
    let dropdown = document.getElementById("dropdownMenu1");
    dropdown.classList.toggle("show");
}

function logOut() {
    fetch("../api/logout.php", {
        method: "POST"
    })
    .then(response => response.json())
    .then(data => {
        if(data.status === "success") {
            window.location.href = "../login/login.php";
        }
    })
    .catch(error => {
        console.error('Error logging out:', error);
        window.location.href = "../login/login.php"; 
    });
}

document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown toggle
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

    initModal();
    getPassenger();

    const profileForm = document.getElementById("profileUpdateForm");
    if (profileForm) {
        profileForm.addEventListener("submit", function(event) {
            event.preventDefault();
            updateProfile();
        });
    }
});

function initModal() {
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'EditProfile') {
            e.preventDefault();
            document.getElementById("editProfileModal").classList.add("show");
        }
        if (e.target && e.target.classList.contains('close')) {
            document.getElementById("editProfileModal").classList.remove("show");
        }
    });

    window.addEventListener('click', function(e) {
        const modal = document.getElementById("editProfileModal");
        if (e.target === modal) {
            modal.classList.remove("show");
        }
    });
}

function getPassenger() {
    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Session response:', data);
            if (data.status === "success" && data.passengerId) {
                return fetch(`../api/passenger_api.php?passengerId=${data.passengerId}`);
            } else {
                throw new Error(data.message || "User not authenticated");
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Passenger fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Passenger response:', data);
            if (data.status === "success" && data.passengerData) {
                const passenger = data.passengerData;
                
                const imgElement = document.getElementById("imageDisplay2");
                if (imgElement) {
                    imgElement.src = passenger.picture ? `../Uploads/${passenger.picture}` : '../images/profile.png';
                }
                
                const nameElement = document.getElementById("nameDisplay2");
                if (nameElement) {
                    nameElement.textContent = `${passenger.first_name} ${passenger.last_name}`;
                }
                
                const emailElement = document.getElementById("userEmail");
                if (emailElement) {
                    emailElement.textContent = passenger.email;
                }
                
                // Populate edit form fields
                const firstNameField = document.getElementById("editfirstName");
                if (firstNameField) {
                    firstNameField.value = passenger.first_name;
                }
                
                const lastNameField = document.getElementById("editlastName");
                if (lastNameField) {
                    lastNameField.value = passenger.last_name;
                }
            } else {
                throw new Error(data.message || "Failed to load passenger data");
            }
        })
        .catch(error => {
            console.error('Error fetching passenger:', error);
            alert("Error loading profile: " + error.message);
        });
}

function updateProfile() {
    const formData = new FormData();
    formData.append('firstName', document.getElementById("editfirstName").value);
    formData.append('lastName', document.getElementById("editlastName").value);
    
    const profilePicInput = document.getElementById("edit-prof");
    if (profilePicInput.files[0]) {
        const file = profilePicInput.files[0];
        if (file.size > 5 * 1024 * 1024 || !file.type.startsWith('image/')) {
            alert('Profile picture must be an image under 5MB.');
            return;
        }
        formData.append('picture', file);
    }

    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error('Session fetch failed');
            return response.json();
        })
        .then(data => {
            console.log('Session response for update:', data);
            if (data.status === "success" && data.passengerId) {
                return fetch("../api/passenger_api.php", {
                    method: "POST",
                    body: formData
                });
            } else {
                throw new Error(data.message || "User not authenticated");
            }
        })
        .then(response => {
            if (!response.ok) throw new Error('Update failed');
            return response.json();
        })
        .then(data => {
            console.log('Update response:', data);
            if (data.status === "success") {
                alert("Profile updated successfully!");
                document.getElementById("editProfileModal").classList.remove("show");
                getPassenger();
            } else {
                throw new Error(data.message || "Update failed");
            }
        })
        .catch(error => {
            console.error('Error updating profile:', error);
            alert("Error updating profile: " + error.message);
        });
}



document.addEventListener("DOMContentLoaded", function () {
    checkUserSession();
    loadActiveBookings();
    setupBookingButtons();
    loadBookingHistory();
    
    const urlParams = new URLSearchParams(window.location.search);
    const busId = urlParams.get('bus_id');
    const bookingId = urlParams.get('booking_id') || urlParams.get('id');
    
    // Handle both types of parameters
    if (busId) {
        getBusById(busId);
    } else if (bookingId) {
        getBookingById(bookingId);
    }
});

function checkUserSession() {
    fetch("../api/store_session.php")
        .then(response => response.json())
        .then(data => {
            if (!data.userId) {
                window.location.href = "login.php";
                return;
            }
            if (data.firstName && data.lastName) {
                document.getElementById("nameDisplay2").textContent = data.firstName + " " + data.lastName;
            }
            if (data.email) {
                document.getElementById("userEmail").textContent = data.email;
            }
            if (data.profilePicture) {
                document.getElementById("imageDisplay2").src = data.profilePicture;
            }
        })
        .catch(error => console.error("Session check error:", error));
}

function loadActiveBookings() {
    fetch("../api/store_session.php")
        .then(response => response.json())
        .then(data => {
            if (!data.userId) {
                console.error("User not authenticated");
                window.location.href = "login.php";
                return;
            }
            
            // Fetch active and pending bookings
            return fetch(`../api/booking_api.php?user_id=${data.userId}&status=active,pending`);
        })
        .then(response => response.json())
        .then(data => {
            console.log("Booking API response:", data); // Debug: Log the response
            if (data.status === "success") {
                displayActiveBookings(data.bookings);
            } else {
                console.error("Error fetching bookings:", data.message);
                const bookingTable = document.getElementById("content");
                if (bookingTable) {
                    bookingTable.innerHTML = `
                        <tr>
                            <td colspan="6" style="text-align: center;">No active or pending bookings found.</td>
                        </tr>
                    `;
                }
                // Disable action buttons
                const cancelBtn = document.querySelector(".cancel-btn");
                const viewBtn = document.querySelector(".view-btn");
                if (cancelBtn) cancelBtn.disabled = true;
                if (viewBtn) viewBtn.disabled = true;
            }
        })
        .catch(error => {
            console.error("Error fetching bookings:", error);
            const bookingTable = document.getElementById("content");
            if (bookingTable) {
                bookingTable.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center;">Error loading bookings.</td>
                    </tr>
                `;
            }
        });
}

function displayActiveBookings(bookings) {
    const bookingTable = document.getElementById("content");
    
    if (!bookingTable) {
        console.error("Booking table content element not found!");
        return;
    }
    
    // Clear existing content
    bookingTable.innerHTML = '';
    
    if (!bookings || bookings.length === 0) {
        bookingTable.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center;">No active or pending bookings found.</td>
            </tr>
        `;
        // Disable action buttons
        const cancelBtn = document.querySelector(".cancel-btn");
        const viewBtn = document.querySelector(".view-btn");
        if (cancelBtn) cancelBtn.disabled = true;
        if (viewBtn) viewBtn.disabled = true;
        return;
    }
    
    // Enable action buttons
    const cancelBtn = document.querySelector(".cancel-btn");
    const viewBtn = document.querySelector(".view-btn");
    if (cancelBtn) cancelBtn.disabled = false;
    if (viewBtn) viewBtn.disabled = false;
    
    // Populate table with bookings
    bookings.forEach(booking => {
        const bookingRow = document.createElement("tr");
        bookingRow.className = "booking-item";
        bookingRow.dataset.bookingId = booking.booking_id;
        bookingRow.dataset.busId = booking.bus_id;
        
        bookingRow.innerHTML = `
            <td>${booking.bus_number || 'N/A'}</td>
            <td>${booking.location || 'N/A'}</td>
            <td>${booking.destination || 'N/A'}</td>
            <td>${booking.departure_time || 'N/A'}</td>
            <td>${booking.arrival_time || 'N/A'}</td>
            <td>${booking.seat_number || 'N/A'}</td>
        `;
        
        bookingTable.appendChild(bookingRow);
        
        // Add click handler to select booking
        bookingRow.addEventListener("click", function() {
            document.querySelectorAll(".booking-item").forEach(row => {
                row.classList.remove("selected");
            });
            this.classList.add("selected");
        });
    });
    
    // Auto-select the first booking
    const firstBooking = document.querySelector(".booking-item");
    if (firstBooking) {
        firstBooking.classList.add("selected");
    }
}

function getBusById(id) {
    console.log(`Fetching bus with ID: ${id}`);
    fetch(`../api/bus_api.php?id=${id}`)
        .then(response => {
            console.log("API Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("API Response data:", data);
            if (data.status === "success" && data.bus) {
                console.log("Bus booked successfully:", data.bus);
                // Show success message
                alert(`Bus booking successful! You've booked a seat on bus ${data.bus.bus_number} from ${data.bus.location} to ${data.bus.destination}.`);
                // Refresh the active bookings to show the new booking
                loadActiveBookings();
            } else {
                console.error("Error fetching bus:", data.message);
                alert("Failed to confirm booking details. Please check your active bookings.");
            }
        })
        .catch(error => {
            console.error("Error fetching bus:", error);
            alert("Failed to load bus data. Please try again later.");
        });
}
function getBookingById(id) {
    console.log(`Fetching booking with ID: ${id}`);
    fetch(`../api/booking_api.php?booking_id=${id}`)
        .then(response => {
            console.log("API Response status:", response.status);
            return response.json();
        })
        .then(data => {
            console.log("API Response data:", data);
            if (data.status === "success" && data.booking) {
                console.log("Booking details:", data.booking);
                displayBookingDetails(data.booking);
            } 
        })
        .catch(error => {
            console.error("Error fetching booking:", error);
            alert("Failed to load booking data. Please check your active bookings.");
        });
}

function displayBookingDetails(booking) {
    const bookingDetailsContainer = document.getElementById("booking-details");
    if (!bookingDetailsContainer) {
        console.error("Booking details container not found!");
        return;
    }

    bookingDetailsContainer.innerHTML = `
        <h3>Booking Details</h3>
        <p><strong>Bus Number:</strong> ${booking.bus_number || 'N/A'}</p>
        <p><strong>From:</strong> ${booking.location || 'N/A'}</p>
        <p><strong>To:</strong> ${booking.destination || 'N/A'}</p>
        <p><strong>Departure:</strong> ${formatTime(booking.departure_time) || 'N/A'}</p>
        <p><strong>Arrival:</strong> ${formatTime(booking.arrival_time) || 'N/A'}</p>
        <p><strong>Seat Number:</strong> ${booking.seat_number || 'N/A'}</p>
        <p><strong>Reference:</strong> ${booking.reference || 'N/A'}</p>
    `;
}

function setupBookingButtons() {
    // Cancel booking button
    const cancelBtn = document.querySelector(".cancel-btn");
    if (cancelBtn) {
        cancelBtn.addEventListener("click", function() {
            const selectedBooking = document.querySelector(".booking-item.selected");
            
            if (!selectedBooking) {
                alert("Please select a booking to cancel.");
                return;
            }
            
            const bookingId = selectedBooking.dataset.bookingId;
            
            if (confirm("Are you sure you want to cancel this booking?")) {
                cancelBooking(bookingId);
            }
        });
    }
    
}

function cancelBooking(bookingId) {
    fetch("../api/booking_api.php", {
        method: "DELETE",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ booking_id: Number(bookingId) })
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === "success") {
            alert("Booking cancelled successfully!");
            loadActiveBookings(); 
        } else {
            alert("Failed to cancel booking: " + data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("An error occurred while cancelling the booking.");
    });
}

function loadBookingHistory() {
    fetch("../api/store_session.php")
        .then(response => {
            if (!response.ok) throw new Error("Session fetch failed");
            return response.json();
        })
        .then(data => {
            if (!data.userId) {
                console.error("User not authenticated");
                window.location.href = "login.php";
                return;
            }
            // Fetch confirmed bookings
            return fetch(`../api/booking_api.php?user_id=${data.userId}&status=confirmed`);
        })
        .then(response => response.json())
        .then(data => {
            console.log("Booking History API response:", data);
            displayBookingHistory(data.bookings);
        })
        .catch(error => {
            console.error("Error fetching booking history:", error);
            const historyTable = document.getElementById("bookingHistoryTableBody");
            if (historyTable) {
                historyTable.innerHTML = `
                    <tr>
                        <td colspan="6" style="text-align: center;">Error loading booking history.</td>
                    </tr>
                `;
            }
        });
}

function displayBookingHistory(bookings) {
    const historyTableBody = document.getElementById("bookingHistoryTableBody");
    if (!historyTableBody) {
        console.error("Booking history table body not found!");
        return;
    }

    historyTableBody.innerHTML = "";

    if (!bookings || bookings.length === 0) {
        historyTableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center;">No confirmed bookings found.</td>
            </tr>
        `;
        return;
    }

    bookings.forEach(booking => {
        const row = document.createElement("tr");
        row.innerHTML = `
            <td>${booking.bus_number || "N/A"}</td>
            <td>${booking.location || "N/A"}</td>
            <td>${booking.destination || "N/A"}</td>
            <td>${booking.departure_time || "N/A"}</td>
            <td>${booking.arrival_time || "N/A"}</td>
            <td>${booking.seat_number || "N/A"}</td>
        `;
        historyTableBody.appendChild(row);
    });
}