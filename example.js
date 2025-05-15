
document.addEventListener('DOMContentLoaded', function() {
    const seatModal = document.getElementById('seat-modal');
    const closeSeatModal = document.getElementById('close-seat-modal');
    const selectSeatBtn = document.getElementById('select-seat-btn');
    const seatNumberInput = document.getElementById('seat-number');
    const ticketForm = document.getElementById('ticket-form');
    const busId = ticketForm.querySelector('input[name="bus_id"]').value;

    // Fetch available seats and display in modal
    async function fetchBusDetails(busId) {
      try {
        const response = await fetch(`get_bus_details.php?bus_id=${busId}`);
        const data = await response.json();

        if (data.success && data.available_seats) {
          const seatMap = document.getElementById('seat-map');
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
                seatNumberInput.value = seat;
                seatModal.style.display = 'none';
              });
              seatMap.appendChild(seatBtn);
            });
          }

          seatModal.style.display = 'flex';
        } else {
          alert(data.message || 'Error fetching seats');
        }
      } catch (err) {
        console.error(err);
        alert('Failed to fetch seat data.');
      }
    }

    selectSeatBtn.addEventListener('click', () => fetchBusDetails(busId));
    closeSeatModal.addEventListener('click', () => seatModal.style.display = 'none');

    // Validate and submit form via AJAX
    ticketForm.addEventListener('submit', async (e) => {
      e.preventDefault();

      const formData = new FormData(ticketForm);
      const passengerType = formData.get('passenger_type');
      const idUpload = formData.get('id_upload');

      // Validate file upload requirement
      if ((passengerType === 'PWD/Senior Citizen' || passengerType === 'Student') && (!idUpload || !idUpload.name)) {
        alert('Please upload your ID for PWD/Senior Citizen or Student.');
        return;
      }

      try {
        const response = await fetch('book_ticket.php', {
          method: 'POST',
          body: formData,
        });
        const result = await response.json();

        if (result.success) {
          alert(`Booking successful! Your reference: ${result.reference}`);
          ticketForm.reset();
          seatNumberInput.value = '';
        } else {
          alert('Booking failed: ' + (result.message || 'Unknown error'));
        }
      } catch (err) {
        console.error(err);
        alert('Error submitting booking.');
      }
    });
  });
