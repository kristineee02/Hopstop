<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';
include_once '../class/Bookings.php';
include_once '../class/Bus.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);
$bus = new Bus($db);

if (!$db) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'occupied_seats') {
            if (!isset($_GET['bus_id'])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Bus ID is required"]);
                exit;
            }
            $bus_id = $_GET['bus_id'];
            try {
                $occupiedSeats = $booking->getOccupiedSeats($bus_id);
                echo json_encode(["status" => "success", "occupied_seats" => $occupiedSeats]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }
        if (isset($_GET['bus_id'])) {
            $bus_id = $_GET['bus_id'];
            try {
                $bookings = $booking->getBookingsByBusId($bus_id);
                echo json_encode(["status" => "success", "bookings" => $bookings]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }
        if (isset($_GET['passenger_id'])) {
            $passenger_id = $_GET['passenger_id'];
            try {
                $bookings = $booking->getBookingsByPassengerId($passenger_id);
                echo json_encode(["status" => "success", "bookings" => $bookings]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }
        if (isset($_GET['passenger_type'])) {
            $passenger_type = $_GET['passenger_type'];
            try {
                $bookings = $booking->getBookingsByPassengerType($passenger_type);
                echo json_encode(["status" => "success", "bookings" => $bookings]);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            try {
                $bookingData = $booking->getBookingById($id);
                if ($bookingData) {
                    echo json_encode(["status" => "success", "booking" => $bookingData]);
                } else {
                    http_response_code(404);
                    echo json_encode(["status" => "error", "message" => "Booking not found"]);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }
        try {
            $bookings = $booking->getAllBookingDetails();
            echo json_encode(["status" => "success", "bookings" => $bookings]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    case 'POST':
        $passenger_id = $_POST['passenger_id'] ?? null;
        $bus_id = $_POST['bus_id'] ?? null;
        $reserve_name = $_POST['reserve_name'] ?? null;
        $passenger_type = $_POST['passenger_type'] ?? null;
        $seat_number = $_POST['seat_number'] ?? null;
        $remarks = $_POST['remarks'] ?? '';

        if (!$passenger_id || !$bus_id || !$reserve_name || !$passenger_type || !$seat_number) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            exit;
        }

        try {
            // Check if seat is already taken
            $occupiedSeats = $booking->getOccupiedSeats($bus_id);
            if (in_array($seat_number, $occupiedSeats)) {
                http_response_code(409);
                echo json_encode(["status" => "error", "message" => "Seat is already taken"]);
                exit;
            }

            // Verify bus exists and has available seats
            $busData = $bus->getBusById($bus_id);
            if (!$busData) {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Bus not found"]);
                exit;
            }

            if ($busData['available_seats'] <= 0) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "No available seats on this bus"]);
                exit;
            }

            $reference = $booking->generateReference();
            $id_upload_path = null;
            if (isset($_FILES['id_file']) && $_FILES['id_file']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = "../Uploads/ids/";
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                $filename = uniqid() . "_" . basename($_FILES['id_file']['name']);
                $target_file = $upload_dir . $filename;
                if (!move_uploaded_file($_FILES['id_file']['tmp_name'], $target_file)) {
                    http_response_code(500);
                    echo json_encode(["status" => "error", "message" => "Failed to upload ID file"]);
                    exit;
                }
                $id_upload_path = $target_file;
            }

            $booking_id = $booking->createNewBooking(
                $passenger_id,
                $bus_id,
                $reserve_name,
                $passenger_type,
                $seat_number,
                $id_upload_path,
                $reference,
                $remarks,
                'pending'
            );

            if ($booking_id) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Booking created successfully",
                    "booking_id" => $booking_id,
                    "reference" => $reference
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create booking"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    case 'PUT':
        $inputJSON = file_get_contents('php://input');
        $inputData = json_decode($inputJSON, true);
        if (!$inputData || !isset($inputData['reference'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Booking reference is required"]);
            exit;
        }
        try {
            $result = $booking->updateBookingStatusByReference($inputData['reference'], $inputData['status']);
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Booking status updated successfully"]);
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Failed to update booking status"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Booking ID is required for deletion"]);
            exit;
        }
        $id = $_GET['id'];
        try {
            $result = $booking->deleteBooking($id);
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Booking deleted successfully"]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Failed to delete booking or booking not found"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        exit;
}
?>