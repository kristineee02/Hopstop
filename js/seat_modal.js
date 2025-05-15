// Seat selection modal functionality
document.addEventListener('DOMContentLoaded', function() {
    const seatModal = document.getElementById('seat-modal');
    const closeSeatModal = document.getElementById('close-seat-modal');
    const selectSeatBtn = document.getElementById('select-seat-btn');
    const seatNumberInput = document.getElementById('seat-number');
    const ticketForm = document.getElementById('ticket-form');
    
    // Only proceed if we have the necessary elements
    if (ticketForm && selectSeatBtn) {
        const busIdInput = ticketForm.querySelector('input[name="bus_id"]');
        if (busIdInput) {
            const busId = busIdInput.value;
            
            // Open modal when select seat button is clicked
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
        }
    }
    
    // Function to fetch and display available seats
    async function fetchBusDetails(busId) {
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
});