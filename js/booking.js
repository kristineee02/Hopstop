document.addEventListener("DOMContentLoaded", function() {
    loadBookings();
    document.getElementById("search-reference").addEventListener("keyup", searchBookings);
    // Poll for new bookings every 30 seconds
    setInterval(loadBookings, 30000);
});

function loadBookings() {
    fetch("../api/booking_api.php")
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const BookingTableBody = document.getElementById("BookingTableBody");
            BookingTableBody.innerHTML = "";
            if (data.status === "success" && data.bookings) {
                data.bookings.forEach(booking => {
                    const price = booking.passenger_type === 'Regular' ? 
                        parseFloat(booking.price).toFixed(2) : 
                        (parseFloat(booking.price) * 0.8).toFixed(2);
                    const statusClass = booking.status === 'confirmed' ? 'status-confirmed' : 
                                       booking.status === 'cancelled' ? 'status-cancelled' : 'status-pending';
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${booking.booking_id || ''}</td>
                        <td>${booking.reference || ''}</td>
                        <td>${booking.passenger_name || ''}</td>
                        <td>${booking.reserve_name || ''}</td>
                        <td>${booking.passenger_type || ''}</td>
                        <td>${booking.bus_id || ''}</td>
                        <td>${booking.seat_number || ''}</td>
                        <td>${booking.location || ''}</td>
                        <td>${booking.destination || ''}</td>
                        <td>${formatTime(booking.departure_time) || ''}</td>
                        <td>${formatTime(booking.arrival_time) || ''}</td>
                        <td>₱${price || '0'}</td>
                        <td>${booking.remarks || ''}</td>
                        <td>${booking.id_upload_path ? `<a href="${booking.id_upload_path}" target="_blank">View</a>` : ''}</td>
                        <td>
                            <button class="status-btn ${statusClass}" 
                                    onclick="updateStatus('${booking.reference}', '${getNextStatus(booking.status)}')">
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1) || 'Pending'}
                            </button>
                        </td>
                    `;
                    row.classList.add("clickable-row");
                    row.addEventListener("click", function(e) {
                        if (!e.target.classList.contains('status-btn') && e.target.tagName !== 'A') {
                            openEditModal(booking);
                        }
                    });
                    BookingTableBody.appendChild(row);
                });
            } else {
                console.error("Error loading bookings:", data.message || "Unknown error");
                BookingTableBody.innerHTML = '<tr><td colspan="15">No bookings found</td></tr>';
            }
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            document.getElementById("BookingTableBody").innerHTML = '<tr><td colspan="15">Error loading bookings</td></tr>';
        });
}

function formatTime(timeString) {
    if (!timeString) return '';
    try {
        const date = new Date(`2000-01-01T${timeString}`);
        return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
    } catch (e) {
        return timeString;
    }
}

function getNextStatus(currentStatus) {
    if (currentStatus === 'confirmed') return 'pending';
    if (currentStatus === 'cancelled') return 'pending';
    return 'confirmed';
}

function openEditModal(booking) {
    document.getElementById("editBookingId").value = booking.booking_id || '';
    document.getElementById("editReference").value = booking.reference || '';
    document.getElementById("editPassengerName").value = booking.passenger_name || '';
    document.getElementById("editReserveName").value = booking.reserve_name || '';
    document.getElementById("editPassengerType").value = booking.passenger_type || '';
    document.getElementById("editBusId").value = booking.bus_id || '';
    document.getElementById("editSeatNumber").value = booking.seat_number || '';
    document.getElementById("editPrice").value = booking.passenger_type === 'Regular' ? 
        parseFloat(booking.price).toFixed(2) : (parseFloat(booking.price) * 0.8).toFixed(2);
    document.getElementById("editStatus").value = booking.status || 'pending';
    document.getElementById("editRemarks").value = booking.remarks || '';
    document.getElementById("editIdUpload").value = booking.id_upload_path || '';
    document.getElementById("editLocation").value = booking.location || '';
    document.getElementById("editDestination").value = booking.destination || '';
    document.getElementById("editDeparture").value = formatTime(booking.departure_time) || '';
    document.getElementById("editArrival").value = formatTime(booking.arrival_time) || '';
    document.getElementById("editBookingModal").style.display = "block";
}

function closeEditModal() {
    document.getElementById("editBookingModal").style.display = "none";
}

function updateBooking() {
    const formData = {
        reference: document.getElementById("editReference").value,
        reserve_name: document.getElementById("editReserveName").value,
        passenger_type: document.getElementById("editPassengerType").value,
        bus_id: document.getElementById("editBusId").value,
        seat_number: document.getElementById("editSeatNumber").value,
        price: document.getElementById("editPrice").value,
        status: document.getElementById("editStatus").value,
        remarks: document.getElementById("editRemarks").value
    };
    for (const key in formData) {
        if (!formData[key] && key !== 'remarks') {
            alert(`${key.replace(/_/g, ' ')} is required!`);
            return;
        }
    }
    fetch("../api/booking_api.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify(formData)
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            alert(data.message);
            if (data.status === "success") {
                closeEditModal();
                loadBookings();
            }
        })
        .catch(error => {
            console.error("Error updating booking:", error);
            alert("Failed to update booking: " + error.message);
        });
}

function updateStatus(reference, newStatus) {
    event.stopPropagation();
    fetch("../api/booking_api.php", {
        method: "PUT",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({ reference, status: newStatus })
    })
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            if (data.status === "success") {
                loadBookings();
            } else {
                alert("Failed to update status: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error updating status:", error);
            alert("Failed to update status: " + error.message);
        });
}

function filterPassengerType() {
    const filterValue = document.getElementById("filterPassType").value;
    const url = filterValue ? `../api/booking_api.php?passenger_type=${encodeURIComponent(filterValue)}` : "../api/booking_api.php";
    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            return response.json();
        })
        .then(data => {
            const BookingTableBody = document.getElementById("BookingTableBody");
            BookingTableBody.innerHTML = "";
            if (data.status === "success" && data.bookings) {
                data.bookings.forEach(booking => {
                    const price = booking.passenger_type === 'Regular' ? 
                        parseFloat(booking.price).toFixed(2) : 
                        (parseFloat(booking.price) * 0.8).toFixed(2);
                    const statusClass = booking.status === 'confirmed' ? 'status-confirmed' : 
                                       booking.status === 'cancelled' ? 'status-cancelled' : 'status-pending';
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${booking.booking_id || ''}</td>
                        <td>${booking.reference || ''}</td>
                        <td>${booking.passenger_name || ''}</td>
                        <td>${booking.reserve_name || ''}</td>
                        <td>${booking.passenger_type || ''}</td>
                        <td>${booking.bus_id || ''}</td>
                        <td>${booking.seat_number || ''}</td>
                        <td>${booking.location || ''}</td>
                        <td>${booking.destination || ''}</td>
                        <td>${formatTime(booking.departure_time) || ''}</td>
                        <td>${formatTime(booking.arrival_time) || ''}</td>
                        <td>₱${price || '0'}</td>
                        <td>${booking.remarks || ''}</td>
                        <td>${booking.id_upload_path ? `<a href="${booking.id_upload_path}" target="_blank">View</a>` : ''}</td>
                        <td>
                            <button class="status-btn ${statusClass}" 
                                    onclick="updateStatus('${booking.reference}', '${getNextStatus(booking.status)}')">
                                ${booking.status.charAt(0).toUpperCase() + booking.status.slice(1) || 'Pending'}
                            </button>
                        </td>
                    `;
                    row.classList.add("clickable-row");
                    row.addEventListener("click", function(e) {
                        if (!e.target.classList.contains('status-btn') && e.target.tagName !== 'A') {
                            openEditModal(booking);
                        }
                    });
                    BookingTableBody.appendChild(row);
                });
            } else {
                BookingTableBody.innerHTML = '<tr><td colspan="15">No bookings found</td></tr>';
            }
        })
        .catch(error => {
            console.error("Error filtering bookings:", error);
            document.getElementById("BookingTableBody").innerHTML = '<tr><td colspan="15">Error loading bookings</td></tr>';
        });
}

function searchBookings() {
    const searchValue = document.getElementById("search-reference").value.toLowerCase();
    const rows = document.getElementById("BookingTableBody").getElementsByTagName("tr");
    for (let i = 0; i < rows.length; i++) {
        const referenceCell = rows[i].getElementsByTagName("td")[1]; // Reference is second column
        if (referenceCell) {
            const reference = referenceCell.textContent || referenceCell.innerText;
            rows[i].style.display = reference.toLowerCase().includes(searchValue) ? "" : "none";
        }
    }
}