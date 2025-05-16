document.addEventListener("DOMContentLoaded", function () {
    loadBookings();

    // Add event listener for search functionality
    const searchInput = document.getElementById("search-reference");
    if (searchInput) {
        searchInput.addEventListener("keyup", function () {
            searchBookings();
        });
    }
});

function loadBookings() {
    const bookingTableBody = document.getElementById("BookingTableBody");
    if (bookingTableBody) {
        bookingTableBody.innerHTML = `<tr><td colspan="5" style="text-align: center;">Loading...</td></tr>`;
    }

    fetch("../api/booking_api.php")
        .then(response => {
            console.log("API Response Status:", response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log("API Response Data:", data); 
            if (!bookingTableBody) {
                console.error("Booking table body not found!");
                return;
            }

            bookingTableBody.innerHTML = "";

            if (data.status === "success" && data.bookings && data.bookings.length > 0) {
                data.bookings.forEach(booking => {
                    const statusClass = booking.status === 'confirmed' ? 'status-confirmed' : 'status-pending';
                    const price = booking.price ? parseFloat(booking.price).toFixed(2) : '0.00';

                    const row = document.createElement('tr');
                    row.className = 'clickable-row';
                    row.innerHTML = `
                        <td>${booking.reference || 'N/A'}</td>
                        <td>${booking.passenger_type || 'N/A'}</td>
                        <td>${booking.seat_number || 'N/A'}</td>
                        <td>₱${price}</td>
                        <td>
                            <button class="status-btn ${statusClass}" 
                                    onclick="updateStatus('${booking.reference}', '${booking.status === 'confirmed' ? 'pending' : 'confirmed'}')">
                                ${booking.status || 'pending'}
                            </button>
                        </td>
                    `;
                    row.addEventListener("click", function (event) {
                        if (event.target.tagName !== 'BUTTON') {
                            displayBookingDetails(booking);
                        }
                    });
                    bookingTableBody.appendChild(row);
                });
            } else {
                console.warn("No bookings found or API error:", data.message || "Empty response");
                bookingTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center;">No bookings found.</td>
                    </tr>
                `;
            }
        })
        .catch(error => {
            console.error("Error fetching bookings:", error);
            if (bookingTableBody) {
                bookingTableBody.innerHTML = `
                    <tr>
                        <td colspan="5" style="text-align: center;">Error loading bookings: ${error.message}</td>
                    </tr>
                `;
            }
        });
}

function displayBookingDetails(booking) {
    const bookingDetailsContainer = document.getElementById("booking-details");
    if (!bookingDetailsContainer) {
        console.error("Booking details container not found!");
        return;
    }

    const price = booking.price ? parseFloat(booking.price).toFixed(2) : '0.00';
    bookingDetailsContainer.innerHTML = `
        <h3>Booking Details</h3>
        <p><strong>Reference:</strong> ${booking.reference || 'N/A'}</p>
        <p><strong>Passenger Type:</strong> ${booking.passenger_type || 'N/A'}</p>
        <p><strong>Seat Number:</strong> ${booking.seat_number || 'N/A'}</p>
        <p><strong>Price:</strong> ₱${price}</p>
        <p><strong>Status:</strong> ${booking.status || 'pending'}</p>
    `;
}

function updateStatus(reference, newStatus) {
    event.stopPropagation();

    const formData = {
        reference: reference,
        status: newStatus
    };

    fetch("../api/booking_api.php", {
        method: "PUT",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify(formData)
    })
        .then(response => {
            console.log("Update Status Response Status:", response.status); // Debug
            return response.json();
        })
        .then(data => {
            console.log("Update Status Response Data:", data); // Debug
            if (data.status === "success") {
                alert(`Booking status updated to ${newStatus}`);
                loadBookings();
            } else {
                alert("Failed to update status: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error updating status:", error);
            alert("An error occurred while updating the status.");
        });
}

function filterPassengerType() {
    const filterValue = document.getElementById("filterPassType").value;

    if (filterValue === "") {
        loadBookings();
    } else {
        fetch(`../api/booking_api.php?passenger_type=${encodeURIComponent(filterValue)}`)
            .then(response => {
                console.log("Filter API Response Status:", response.status); // Debug
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("Filter API Response Data:", data); // Debug
                const bookingTableBody = document.getElementById("BookingTableBody");
                if (!bookingTableBody) {
                    console.error("Booking table body not found!");
                    return;
                }

                bookingTableBody.innerHTML = "";

                if (data.status === "success" && data.bookings && data.bookings.length > 0) {
                    data.bookings.forEach(booking => {
                        const statusClass = booking.status === 'confirmed' ? 'status-confirmed' : 'status-pending';
                        const price = booking.price ? parseFloat(booking.price).toFixed(2) : '0.00';

                        const row = document.createElement('tr');
                        row.className = 'clickable-row';
                        row.innerHTML = `
                            <td>${booking.reference || 'N/A'}</td>
                            <td>${booking.passenger_type || 'N/A'}</td>
                            <td>${booking.seat_number || 'N/A'}</td>
                            <td>₱${price}</td>
                            <td>
                                <button class="status-btn ${statusClass}" 
                                        onclick="updateStatus('${booking.reference}', '${booking.status === 'confirmed' ? 'pending' : 'confirmed'}')">
                                    ${booking.status || 'pending'}
                                </button>
                            </td>
                        `;
                        row.addEventListener("click", function (event) {
                            if (event.target.tagName !== 'BUTTON') {
                                displayBookingDetails(booking);
                            }
                        });
                        bookingTableBody.appendChild(row);
                    });
                } else {
                    bookingTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center;">No bookings found for ${filterValue}.</td>
                        </tr>
                    `;
                }
            })
            .catch(error => {
                console.error("Error filtering bookings:", error);
                if (bookingTableBody) {
                    bookingTableBody.innerHTML = `
                        <tr>
                            <td colspan="5" style="text-align: center;">Error loading bookings: ${error.message}</td>
                        </tr>
                    `;
                }
            });
    }
}

function searchBookings() {
    const searchValue = document.getElementById("search-reference").value.toLowerCase();
    const rows = document.getElementById("BookingTableBody").getElementsByTagName("tr");

    for (let i = 0; i < rows.length; i++) {
        const referenceCell = rows[i].getElementsByTagName("td")[0];
        if (referenceCell) {
            const reference = referenceCell.textContent || referenceCell.innerText;
            rows[i].style.display = reference.toLowerCase().includes(searchValue) ? "" : "none";
        }
    }
}