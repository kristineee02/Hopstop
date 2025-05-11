<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bus Seat Admin Dashboard</title>
    <style>
        /* Reset and Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
        }
        
        /* Container */
        .container {
            width: 100%;
            max-width: 1140px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        .mt-4 {
            margin-top: 1.5rem;
        }
        
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        
        .mb-2 {
            margin-bottom: 0.5rem;
        }
        
        /* Text Utilities */
        .text-center {
            text-align: center;
        }
        
        /* Alert */
        .alert {
            position: relative;
            padding: 0.75rem 1.25rem;
            margin-bottom: 1rem;
            border: 1px solid transparent;
            border-radius: 0.25rem;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .alert-warning {
            color: #856404;
            background-color: #fff3cd;
            border-color: #ffeeba;
        }
        
        /* Grid System */
        .row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -15px;
            margin-left: -15px;
        }
        
        .col-12 {
            flex: 0 0 100%;
            max-width: 100%;
            padding: 0 15px;
        }
        
        .col-md-4 {
            flex: 0 0 33.333333%;
            max-width: 33.333333%;
            padding: 0 15px;
        }
        
        @media (max-width: 768px) {
            .col-md-4 {
                flex: 0 0 100%;
                max-width: 100%;
                margin-bottom: 1rem;
            }
        }
        
        /* Cards */
        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0, 0, 0, 0.125);
            border-radius: 0.25rem;
        }
        
        .card-header {
            padding: 0.75rem 1.25rem;
            margin-bottom: 0;
            background-color: rgba(0, 0, 0, 0.03);
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .card-body {
            flex: 1 1 auto;
            padding: 1.25rem;
        }
        
        .card-title {
            margin-bottom: 0.75rem;
        }
        
        .summary-card {
            transition: all 0.3s;
        }
        
        .summary-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Bus Layout */
        .bus-layout {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        
        .seat {
            width: 50px;
            height: 50px;
            margin: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 5px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .seat:hover {
            transform: scale(1.05);
        }
        
        .seat-available {
            background-color: #28a745;
            color: white;
        }
        
        .seat-unavailable {
            background-color: #dc3545;
            color: white;
        }
        
        .seat-cell {
            text-align: center;
            font-weight: bold;
        }
        
        .aisle {
            width: 30px;
            display: inline-block;
        }
        
        .driver {
            width: 100px;
            height: 50px;
            background-color: #6c757d;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            margin: 0 auto 20px auto;
        }
        
        /* Table */
        .table-responsive {
            display: block;
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
            border-collapse: collapse;
        }
        
        .table-bordered {
            border: 1px solid #dee2e6;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0, 0, 0, 0.075);
        }
        
        .table th, .table td {
            padding: 0.75rem;
            vertical-align: top;
            border-top: 1px solid #dee2e6;
        }
        
        .table-bordered th, .table-bordered td {
            border: 1px solid #dee2e6;
        }
        
        .table thead th {
            vertical-align: bottom;
            border-bottom: 2px solid #dee2e6;
            background-color: #f8f9fa;
        }
        
        /* Buttons */
        .btn {
            display: inline-block;
            font-weight: 400;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            user-select: none;
            border: 1px solid transparent;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            line-height: 1.5;
            border-radius: 0.25rem;
            transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            cursor: pointer;
        }
        
        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
            line-height: 1.5;
            border-radius: 0.2rem;
        }
        
        .btn-primary {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        
        .btn-primary:hover {
            color: #fff;
            background-color: #0069d9;
            border-color: #0062cc;
        }
        
        .btn-secondary {
            color: #fff;
            background-color: #6c757d;
            border-color: #6c757d;
        }
        
        .btn-secondary:hover {
            color: #fff;
            background-color: #5a6268;
            border-color: #545b62;
        }
        
        .btn-outline-primary {
            color: #007bff;
            border-color: #007bff;
            background-color: transparent;
        }
        
        .btn-outline-primary:hover {
            color: #fff;
            background-color: #007bff;
            border-color: #007bff;
        }
        
        /* Forms */
        .form-label {
            margin-bottom: 0.5rem;
            display: inline-block;
        }
        
        .form-select {
            display: block;
            width: 100%;
            padding: 0.375rem 0.75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: #212529;
            background-color: #fff;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 16px 12px;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            appearance: none;
        }
        
        .mb-3 {
            margin-bottom: 1rem;
        }
        
        /* Modal */
        .modal {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1050;
            display: none;
            width: 100%;
            height: 100%;
            overflow: hidden;
            outline: 0;
            background-color: rgba(0, 0, 0, 0.5);
        }
        
        .modal.show {
            display: block;
        }
        
        .modal-dialog {
            position: relative;
            width: auto;
            margin: 1.75rem auto;
            max-width: 500px;
        }
        
        .modal-content {
            position: relative;
            display: flex;
            flex-direction: column;
            width: 100%;
            pointer-events: auto;
            background-color: #fff;
            background-clip: padding-box;
            border: 1px solid rgba(0, 0, 0, 0.2);
            border-radius: 0.3rem;
            outline: 0;
        }
        
        .modal-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            padding: 1rem 1rem;
            border-bottom: 1px solid #dee2e6;
            border-top-left-radius: calc(0.3rem - 1px);
            border-top-right-radius: calc(0.3rem - 1px);
        }
        
        .modal-title {
            margin-bottom: 0;
            line-height: 1.5;
        }
        
        .btn-close {
            padding: 0.5rem 0.5rem;
            margin: -0.5rem -0.5rem -0.5rem auto;
            background-color: transparent;
            border: 0;
            font-size: 1.5rem;
            line-height: 1;
            color: #000;
            opacity: .5;
            cursor: pointer;
        }
        
        .modal-body {
            position: relative;
            flex: 1 1 auto;
            padding: 1rem;
        }
        
        .modal-footer {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            padding: 0.75rem;
            border-top: 1px solid #dee2e6;
            border-bottom-right-radius: calc(0.3rem - 1px);
            border-bottom-left-radius: calc(0.3rem - 1px);
        }
        
        .modal-footer > * {
            margin: 0.25rem;
        }
        
        /* Tooltip */
        .tooltip {
            position: absolute;
            z-index: 1070;
            display: block;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            font-style: normal;
            font-weight: 400;
            line-height: 1.5;
            text-align: left;
            text-decoration: none;
            text-shadow: none;
            text-transform: none;
            letter-spacing: normal;
            word-break: normal;
            word-spacing: normal;
            white-space: normal;
            line-break: auto;
            font-size: 0.875rem;
            word-wrap: break-word;
            opacity: 0;
            pointer-events: none;
        }
        
        .tooltip.show {
            opacity: 0.9;
        }
        
        .tooltip-inner {
            max-width: 200px;
            padding: 0.25rem 0.5rem;
            color: #fff;
            text-align: center;
            background-color: #000;
            border-radius: 0.25rem;
        }
        
        .tooltip-arrow {
            position: absolute;
            display: block;
            width: 0.8rem;
            height: 0.4rem;
        }
        
        .bs-tooltip-top .tooltip-arrow {
            bottom: 0;
        }
        
        .bs-tooltip-top .tooltip-arrow::before {
            top: 0;
            border-width: 0.4rem 0.4rem 0;
            border-top-color: #000;
        }
        
        .d-flex {
            display: flex;
        }
        
        .justify-content-between {
            justify-content: space-between;
        }
        
        .align-items-center {
            align-items: center;
        }
        
        h1, h2, h3, h4, h5, h6 {
            margin-top: 0;
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Bus Seat Admin Dashboard</h1>
        
        <div class="alert alert-success" id="statusAlert" role="alert">
            Loading seat information...
        </div>
        
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Total Seats</h5>
                        <h2 id="totalSeats">--</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Available Seats</h5>
                        <h2 id="availableSeats">--</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card summary-card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Unavailable Seats</h5>
                        <h2 id="unavailableSeats">--</h2>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Bus Layout</h4>
                    </div>
                    <div class="card-body bus-layout">
                        <div class="driver">Driver</div>
                        <div id="busLayout" class="text-center">
                            <!-- Bus layout will be generated here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4>Seat Details</h4>
                <div>
                    <button class="btn btn-primary btn-sm" id="refreshBtn">
                        🔄 Refresh Data
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="seatsTable">
                        <thead>
                            <tr>
                                <th>Seat ID</th>
                                <th>Seat Number</th>
                                <th>User ID</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Seat data will be loaded here -->
                            <tr>
                                <td colspan="5" class="text-center">Loading seat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Vanilla JS Modal for Seat Actions -->
    <div class="modal" id="seatActionModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Seat Status</h5>
                    <button type="button" class="btn-close" id="modalClose">×</button>
                </div>
                <div class="modal-body">
                    <form id="seatUpdateForm">
                        <input type="hidden" id="modalSeatNumber" name="seat_number">
                        <div class="mb-3">
                            <label for="modalSeatStatus" class="form-label">Status</label>
                            <select class="form-select" id="modalSeatStatus" name="status" required>
                                <option value="available">Available</option>
                                <option value="unavailable">Unavailable</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" id="closeModalBtn">Close</button>
                    <button type="button" class="btn btn-primary" id="updateSeatBtn">Update Seat</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // API endpoint
        const API_URL = '../api/seat_api.php'; // Update this with your actual API endpoint

        // DOM Elements
        const seatsTable = document.getElementById('seatsTable');
        const totalSeatsEl = document.getElementById('totalSeats');
        const availableSeatsEl = document.getElementById('availableSeats');
        const unavailableSeatsEl = document.getElementById('unavailableSeats');
        const statusAlert = document.getElementById('statusAlert');
        const busLayout = document.getElementById('busLayout');
        const refreshBtn = document.getElementById('refreshBtn');
        const modal = document.getElementById('seatActionModal');
        const modalSeatNumber = document.getElementById('modalSeatNumber');
        const modalSeatStatus = document.getElementById('modalSeatStatus');
        const updateSeatBtn = document.getElementById('updateSeatBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');
        const modalClose = document.getElementById('modalClose');

        // Vanilla JS Modal Functions
        function showModal() {
            modal.classList.add('show');
        }
        
        function hideModal() {
            modal.classList.remove('show');
        }
        
        // Close modal when clicking close buttons
        modalClose.addEventListener('click', hideModal);
        closeModalBtn.addEventListener('click', hideModal);
        
        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                hideModal();
            }
        });

        // Tooltip implementation
        class Tooltip {
            constructor(element) {
                this.element = element;
                this.title = element.getAttribute('data-tooltip');
                this.position = element.getAttribute('data-tooltip-position') || 'top';
                this.tooltipEl = null;
            }
            
            show() {
                // Create tooltip element
                this.tooltipEl = document.createElement('div');
                this.tooltipEl.className = `tooltip bs-tooltip-${this.position} show`;
                
                const tooltipArrow = document.createElement('div');
                tooltipArrow.className = 'tooltip-arrow';
                
                const tooltipInner = document.createElement('div');
                tooltipInner.className = 'tooltip-inner';
                tooltipInner.textContent = this.title;
                
                this.tooltipEl.appendChild(tooltipArrow);
                this.tooltipEl.appendChild(tooltipInner);
                document.body.appendChild(this.tooltipEl);
                
                // Position the tooltip
                const rect = this.element.getBoundingClientRect();
                const tooltipRect = this.tooltipEl.getBoundingClientRect();
                
                if (this.position === 'top') {
                    this.tooltipEl.style.top = `${rect.top - tooltipRect.height - 5}px`;
                    this.tooltipEl.style.left = `${rect.left + (rect.width / 2) - (tooltipRect.width / 2)}px`;
                }
                
                // Position the arrow
                tooltipArrow.style.left = `${tooltipRect.width / 2 - 8}px`;
            }
            
            hide() {
                if (this.tooltipEl) {
                    document.body.removeChild(this.tooltipEl);
                    this.tooltipEl = null;
                }
            }
        }
        
        function initTooltips() {
            const tooltipElements = document.querySelectorAll('[data-tooltip]');
            
            tooltipElements.forEach(el => {
                const tooltip = new Tooltip(el);
                
                el.addEventListener('mouseenter', () => tooltip.show());
                el.addEventListener('mouseleave', () => tooltip.hide());
            });
        }

        // Fetch all seats data
        async function fetchSeats() {
            try {
                const response = await fetch(API_URL);
                const result = await response.json();
                
                if (result.status === 'success') {
                    displaySeats(result.data);
                    updateSummary(result.data);
                    generateBusLayout(result.data);
                    checkIfFullyBooked(result.data);
                } else {
                    showAlert('Error: ' + result.message, 'danger');
                }
            } catch (error) {
                showAlert('Failed to fetch seat data. Please try again.', 'danger');
                console.error('Error fetching seats:', error);
            }
        }

        // Display seats in the table
        function displaySeats(seats) {
            const tbody = seatsTable.querySelector('tbody');
            tbody.innerHTML = '';
            
            seats.forEach(seat => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td>${seat.id}</td>
                    <td>${seat.seat_number}</td>
                    <td>${seat.user_id || 'None'}</td>
                    <td class="seat-cell ${seat.seat_status === 'available' ? 'seat-available' : 'seat-unavailable'}">
                        ${seat.seat_status.toUpperCase()}
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-seat="${seat.seat_number}" data-status="${seat.seat_status}">
                            Edit
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Add event listeners to edit buttons
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const seatNumber = this.getAttribute('data-seat');
                    const status = this.getAttribute('data-status');
                    
                    modalSeatNumber.value = seatNumber;
                    modalSeatStatus.value = status;
                    
                    showModal();
                });
            });
        }

        // Update seat summary
        function updateSummary(seats) {
            const total = seats.length;
            const available = seats.filter(seat => seat.seat_status === 'available').length;
            const unavailable = total - available;
            
            totalSeatsEl.textContent = total;
            availableSeatsEl.textContent = available;
            unavailableSeatsEl.textContent = unavailable;
        }

        // Generate visual bus layout
        function generateBusLayout(seats) {
            // Define the layout based on your seat class
            const layout = [
                [1, 2, 0, 3, 4],
                [5, 6, 0, 7, 8],
                [9, 10, 0, 11, 12],
                [13, 14, 0, 15, 16],
                [17, 18, 0, 19, 20],
                [21, 22, 0, 23, 24],
                [25, 26, 0, 27, 28],
                [29, 30, 0, 31, 32],
                [33, 34, 0, 35, 36],
                [37, 38, 0, 39, 40],
                [41, 42, 0, 43, 44],
                [45, 46, 0, 47, 48]
            ];
            
            busLayout.innerHTML = '';
            
            layout.forEach(row => {
                const rowDiv = document.createElement('div');
                rowDiv.className = 'mb-2';
                
                row.forEach(seatNum => {
                    if (seatNum === 0) {
                        // This is an aisle
                        const aisle = document.createElement('div');
                        aisle.className = 'aisle';
                        rowDiv.appendChild(aisle);
                    } else {
                        // This is a seat
                        const seatData = seats.find(s => parseInt(s.seat_number) === seatNum);
                        
                        if (seatData) {
                            const status = seatData.seat_status;
                            const seatDiv = document.createElement('div');
                            seatDiv.className = `seat ${status === 'available' ? 'seat-available' : 'seat-unavailable'}`;
                            seatDiv.textContent = seatNum;
                            
                            // Add tooltip with more info
                            seatDiv.setAttribute('data-tooltip', `Seat ${seatNum}: ${status.toUpperCase()}${seatData.user_id ? ' (User: ' + seatData.user_id + ')' : ''}`);
                            seatDiv.setAttribute('data-tooltip-position', 'top');
                            
                            rowDiv.appendChild(seatDiv);
                        } else {
                            // Seat not found in data
                            const emptySeat = document.createElement('div');
                            emptySeat.className = 'seat bg-secondary';
                            emptySeat.textContent = seatNum;
                            rowDiv.appendChild(emptySeat);
                        }
                    }
                });
                
                busLayout.appendChild(rowDiv);
            });
            
            // Initialize tooltips
            initTooltips();
        }

        // Check if all seats are fully booked
        function checkIfFullyBooked(seats) {
            const availableSeats = seats.filter(seat => seat.seat_status === 'available');
            
            if (availableSeats.length === 0) {
                showAlert('All seats are fully booked!', 'danger');
            } else if (availableSeats.length <= 5) {
                showAlert(`Only ${availableSeats.length} seats left! Bus is almost fully booked.`, 'warning');
            } else {
                showAlert(`${availableSeats.length} seats available for booking.`, 'success');
            }
        }

        // Show alert
        function showAlert(message, type) {
            statusAlert.className = `alert alert-${type}`;
            statusAlert.textContent = message;
        }

        // Update seat status
        async function updateSeatStatus(seatNumber, status) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        seat_number: seatNumber,
                        status: status
                    })
                });
                
                const result = await response.json();
                
                if (result.status === 'success') {
                    showAlert('Seat updated successfully', 'success');
                    fetchSeats(); // Refresh data
                } else {
                    showAlert('Error: ' + result.message, 'danger');
                }
            } catch (error) {
                showAlert('Failed to update seat. Please try again.', 'danger');
                console.error('Error updating seat:', error);
            }
        }

        // Event Listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Initial fetch
            fetchSeats();
            
            // Refresh button
            refreshBtn.addEventListener('click', fetchSeats);
            
            // Update seat button
            updateSeatBtn.addEventListener('click', function() {
                const seatNumber = modalSeatNumber.value;
                const status = modalSeatStatus.value;
                
                updateSeatStatus(seatNumber, status);
                hideModal();
            });
        });
    </script>
</body>
</html>