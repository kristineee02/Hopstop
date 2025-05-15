<?php
include 'db.php'; // Your DB connection
$bus_id = isset($_GET['bus_id']) ? intval($_GET['bus_id']) : 0;
$bus_details = null;

if ($bus_id) {
    $stmt = $pdo->prepare("SELECT * FROM buses WHERE bus_id = :bus_id");
    $stmt->execute([':bus_id' => $bus_id]);
    $bus_details = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="ticketStyle.css">
  <script src="booking.js"></script>
  <title>Ticket Booking</title>
 
</head>
<body>
  <header>
    <h1>Book Your Ticket</h1>
  </header>

  <main>
    <section id="ticket-form-section">
      <form id="ticket-form" method="POST" action="book_ticket.php" enctype="multipart/form-data">
        <div>
          <label for="name">Name:</label><br />
          <input type="text" id="name" name="name" required />
        </div>

        <div>
          <label for="passenger-type">Passenger Type:</label><br />
          <select id="passenger-type" name="passenger_type" required>
            <option value="Regular">Regular</option>
            <option value="PWD/Senior Citizen">PWD/Senior Citizen</option>
            <option value="Student">Student</option>
          </select>
        </div>

        <div>
          <label for="seat-number">Seat Number:</label><br />
          <input type="text" id="seat-number" name="seat_number" readonly required />
          <button type="button" id="select-seat-btn">Select Seat</button>
        </div>

        <div>
          <label for="id-upload">Upload ID (required for Student/PWD/Senior):</label><br />
          <input type="file" id="id-upload" name="id_upload" accept=".jpg,.jpeg,.png,.pdf" />
        </div>

        <div>
          <label for="remarks">Remarks (optional):</label><br />
          <textarea id="remarks" name="remarks"></textarea>
        </div>

        <!-- Hidden input to pass bus_id -->
        <input type="hidden" name="bus_id" value="<?php echo htmlspecialchars($bus_details['bus_id'] ?? ''); ?>" />

        <br />
        <button type="submit">Submit</button>
      </form>
    </section>

    <!-- Seat selection modal -->
    <div id="seat-modal">
      <div class="modal-content">
        <span class="close" id="close-seat-modal">&times;</span>
        <h3>Select a Seat</h3>
        <div id="seat-map"></div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2024 Bus Reservation System</p>
  </footer>

  
 
</body>
</html>