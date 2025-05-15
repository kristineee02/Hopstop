document.addEventListener('DOMContentLoaded', () => {
  const findSeatBtn = document.getElementById('find-seat-btn');
  const seatModal = document.getElementById('seat-modal');
  const closeSeatModal = document.getElementById('close-seat-modal');
  const seatMapContainer = document.getElementById('seat-map');
  const confirmSeatBtn = document.getElementById('confirm-seat-btn');
  const seatNumberInput = document.getElementById('seat-number');
  const urlParams = new URLSearchParams(window.location.search);
  const busId = urlParams.get('bus_id') || urlParams.get('id');

  if (!busId) {
      alert('No bus selected. Please choose a bus.');
      window.location.href = 'User.php';
      return;
  }

  const seatMap = [
      [1, 2, 0, 3, 4], [5, 6, 0, 7, 8], [9, 10, 0, 11, 12], [13, 14, 0, 15, 16],
      [17, 18, 0, 19, 20], [21, 22, 0, 23, 24], [25, 26, 0, 27, 28], [29, 30, 0, 31, 32],
      [33, 34, 0, 35, 36], [37, 38, 0, 39, 40]
  ];

  let selectedSeat = null;

  async function fetchUnavailableSeats() {
      try {
          const response = await fetch(`../api/booking_api.php?action=occupied_seats&bus_id=${busId}`);
          if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
          const data = await response.json();
          if (data.status === 'success') {
              return data.occupied_seats.map(String);
          }
          throw new Error(data.message || 'Failed to fetch occupied seats');
      } catch (error) {
          console.error('Error fetching unavailable seats:', error);
          alert('Failed to load seat data. Please try again later.');
          return [];
      }
  }

  async function initializeSeatMap() {
      seatMapContainer.innerHTML = '<p>Loading seats...</p>';
      const unavailableSeats = await fetchUnavailableSeats();
      seatMapContainer.innerHTML = '';
      const gridContainer = document.createElement('div');
      seatMap.forEach(row => {
          const rowDiv = document.createElement('div');
          row.forEach(seatNumber => {
              const seatElement = document.createElement('button');
              seatElement.className = 'seat-button';
              seatElement.textContent = seatNumber || '';
              if (seatNumber === 0) {
                  seatElement.style.visibility = 'hidden';
              } else if (unavailableSeats.includes(String(seatNumber))) {
                  seatElement.disabled = true;
                  seatElement.classList.add('unavailable');
              } else {
                  seatElement.dataset.seatNumber = seatNumber;
                  seatElement.classList.toggle('selected', seatNumber === selectedSeat);
                  seatElement.addEventListener('click', () => toggleSeatSelection(seatElement, seatNumber));
              }
              rowDiv.appendChild(seatElement);
          });
          gridContainer.appendChild(rowDiv);
      });
      seatMapContainer.appendChild(gridContainer);
  }

  function toggleSeatSelection(seatElement, seatNumber) {
      const currentSelected = seatMapContainer.querySelector('.seat-button.selected');
      if (currentSelected) {
          currentSelected.classList.remove('selected');
      }
      if (seatElement.classList.contains('selected')) {
          seatElement.classList.remove('selected');
          selectedSeat = null;
      } else {
          seatElement.classList.add('selected');
          selectedSeat = seatNumber;
      }
  }

  function confirmSeatSelection() {
      if (selectedSeat) {
          seatNumberInput.value = selectedSeat;
          seatModal.classList.remove('show');
      } else {
          alert('Please select a seat.');
      }
  }

  findSeatBtn.addEventListener('click', () => {
      initializeSeatMap();
      seatModal.classList.add('show');
  });

  closeSeatModal.addEventListener('click', () => {
      seatModal.classList.remove('show');
  });

  seatModal.addEventListener('click', (e) => {
      if (e.target === seatModal) {
          seatModal.classList.remove('show');
      }
  });

  confirmSeatBtn.addEventListener('click', confirmSeatSelection);
});