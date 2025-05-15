document.addEventListener("DOMContentLoaded", function() {
    loadBookings();
    document.getElementById("search-reference").addEventListener("keyup", searchBookings);
});

function loadBookings() {
    fetch("../api/booking_api.php")
        .then(response => response.json())
        .then(data => {
            const BookingTableBody = document.getElementById("BookingTableBody");
            BookingTableBody.innerHTML = "";
            if (data.status === "success" && data.bookings) {
                data.bookings.forEach(booking => {
                    const price = booking.passenger_type === 'Regular' ? booking.price : (booking.price * 0.8).toFixed(2);
                    const statusClass = booking.status === 'confirmed' ? 'status-confirmed' : 'status-pending';
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${booking.reference || ''}</td>
                        <td>${booking.passenger_type || ''}</td>
                        <td>${booking.seat_number || ''}</td>
                        <td>₱${price || '0'}</td>
                        <td>
                            <button class="status-btn ${statusClass}" 
                                    onclick="updateStatus('${booking.reference}', '${booking.status === 'confirmed' ? 'pending' : 'confirmed'}')">
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1) || 'Pending'}
                            </button>
                        </td>
                    `;
                    row.classList.add("clickable-row");
                    row.addEventListener("click", function(e) {
                        if (!e.target.classList.contains('status-btn')) {
                            openEditModal(
                                booking.reference,
                                booking.passenger_type,
                                booking.seat_number,
                                price,
                                booking.status
                            );
                        }
                    });
                    BookingTableBody.appendChild(row);
                });
            } else {
                console.error("Error loading bookings:", data.message || "Unknown error");
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
        });
}

function openEditModal(reference, passengerType, seatNumber, price, status) {
    document.getElementById("editReference").value = reference;
    document.getElementById("editPassengerType").value = passengerType;
    document.getElementById("editSeatNumber").value = seatNumber;
    document.getElementById("editPrice").value = price;
    document.getElementById("editStatus").value = status;
    document.getElementById("editBookingModal").style.display = "block";
}

function closeEditModal() {
    document.getElementById("editBookingModal").style.display = "none";
}

function updateBooking() {
    const formData = {
        reference: document.getElementById("editReference").value,
        passenger_type: document.getElementById("editPassengerType").value,
        seat_number: document.getElementById("editSeatNumber").value,
        price: document.getElementById("editPrice").value,
        status: document.getElementById("editStatus").value
    };
    for (const key in formData) {
        if (!formData[key]) {
            alert(`${key.replace(/_/g, ' ')} is required!`);
            return;
        }
    }
    fetch("../api/booking_api.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData)
    })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeEditModal();
                loadBookings();
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function updateStatus(reference, newStatus) {
    event.stopPropagation();
    fetch("../api/booking_api.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ reference, status: newStatus })
    })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                loadBookings();
            } else {
                alert("Failed to update status: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error:", error);
        });
}

function filterPassengerType() {
    const filterValue = document.getElementById("filterPassType").value;
    if (filterValue === "") {
        loadBookings();
    } else {
        fetch(`../api/booking_api.php?passenger_type=${encodeURIComponent(filterValue)}`)
            .then(response => response.json())
            .then(data => {
                const BookingTableBody = document.getElementById("BookingTableBody");
                BookingTableBody.innerHTML = "";
                if (data.status === "success" && data.bookings) {
                    data.bookings.forEach(booking => {
                        const price = booking.passenger_type === 'Regular' ? booking.price : (booking.price * 0.8).toFixed(2);
                        const statusClass = booking.status === 'confirmed' ? 'status-confirmed' : 'status-pending';
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${booking.reference || ''}</td>
                            <td>${booking.passenger_type || ''}</td>
                            <td>${booking.seat_number || ''}</td>
                            <td>₱${price || '0'}</td>
                            <td>
                                <button class="status-btn ${statusClass}" 
                                        onclick="updateStatus('${booking.reference}', '${booking.status === 'confirmed' ? 'pending' : 'confirmed'}')">
                                    ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1) || 'Pending'}
                                </button>
                            </td>
                        `;
                        row.classList.add("clickable-row");
                        row.addEventListener("click", function(e) {
                            if (!e.target.classList.contains('status-btn')) {
                                openEditModal(
                                    booking.reference,
                                    booking.passenger_type,
                                    booking.seat_number,
                                    price,
                                    booking.status
                                );
                            }
                        });
                        BookingTableBody.appendChild(row);
                    });
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
            rows[i].style.display = reference.toLowerCase().indexOf(searchValue) > -1 ? "" : "none";
        }
    }
}