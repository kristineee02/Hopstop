<?php
session_start();
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Sanitize and get POST data
$name = trim($_POST['name'] ?? '');
$passenger_type = $_POST['passenger_type'] ?? '';
$seat_number = $_POST['seat_number'] ?? '';
$remarks = $_POST['remarks'] ?? '';
$bus_id = intval($_POST['bus_id'] ?? 0);

if (!$name || !$passenger_type || !$seat_number || !$bus_id) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

try {
    // Check bus exists
    $stmtBus = $pdo->prepare("SELECT * FROM buses WHERE bus_id = :bus_id");
    $stmtBus->execute([':bus_id' => $bus_id]);
    $bus = $stmtBus->fetch();

    if (!$bus) {
        echo json_encode(['success' => false, 'message' => 'Bus not found']);
        exit;
    }

    // Check if seat is still available
    $stmtSeat = $pdo->prepare("SELECT COUNT(*) FROM bookings WHERE bus_id = :bus_id AND seat_number = :seat_number AND status IN ('confirmed', 'pending')");
    $stmtSeat->execute([':bus_id' => $bus_id, ':seat_number' => $seat_number]);
    $seatTaken = $stmtSeat->fetchColumn();

    if ($seatTaken) {
        echo json_encode(['success' => false, 'message' => 'Seat already booked, please choose another seat']);
        exit;
    }

    // Validate file upload if passenger type requires it
    if (($passenger_type === 'PWD/Senior Citizen' || $passenger_type === 'Student')) {
        if (!isset($_FILES['id_upload']) || $_FILES['id_upload']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'ID upload is required for PWD/Senior Citizen or Student']);
            exit;
        }

        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($_FILES['id_upload']['type'], $allowed_types)) {
            echo json_encode(['success' => false, 'message' => 'Invalid ID file type']);
            exit;
        }

        // Save uploaded file
        $uploadDir = __DIR__ . '/uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

        $fileExt = pathinfo($_FILES['id_upload']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('id_') . '.' . $fileExt;
        $uploadPath = $uploadDir . $fileName;

        if (!move_uploaded_file($_FILES['id_upload']['tmp_name'], $uploadPath)) {
            echo json_encode(['success' => false, 'message' => 'Failed to save uploaded ID']);
            exit;
        }

        $id_upload_path = 'uploads/' . $fileName;
    } else {
        $id_upload_path = null;
    }

    // Calculate price with discount
    $price = floatval($bus['price']);
    if ($passenger_type === 'PWD/Senior Citizen' || $passenger_type === 'Student') {
        // Example: 20% discount
        $price *= 0.8;
    }

    // Generate a unique booking reference
    $reference = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

    // Insert booking record
    $stmtInsert = $pdo->prepare("INSERT INTO bookings (passenger_id, bus_id, name, passenger_type, seat_number, id_upload_path, remarks, reference, status, price, created_at) VALUES (:passenger_id, :bus_id, :name, :passenger_type, :seat_number, :id_upload_path, :remarks, :reference, 'pending', :price, NOW())");

    // Assuming you track logged-in users via session (optional)
    $passenger_id = $_SESSION['passenger_id'] ?? null;

    $stmtInsert->execute([
        ':passenger_id' => $passenger_id,
        ':bus_id' => $bus_id,
        ':name' => $name,
        ':passenger_type' => $passenger_type,
        ':seat_number' => $seat_number,
        ':id_upload_path' => $id_upload_path,
        ':remarks' => $remarks,
        ':reference' => $reference,
        ':price' => $price
    ]);

    echo json_encode(['success' => true, 'message' => 'Booking successful', 'reference' => $reference]);

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}