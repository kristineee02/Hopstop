<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';
include_once '../class/Booking.php';
include_once '../class/Bus.php';

$database = new Database();
$db = $database->getConnection();
$booking = new Booking($db);
$bus = new Bus($db);

if (!$db) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

$inputData = null;
if ($method === 'POST' || $method === 'PUT') {
    $inputJSON = file_get_contents('php://input');
    $inputData = json_decode($inputJSON, true);

    if ($inputData === null && json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(["status" => "error", "message" => "Invalid JSON: " . json_last_error_msg()]);
        exit;
    }
}

switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'occupied_seats') {
            // Get occupied seats for a specific bus
            if (!isset($_GET['bus_id'])) {
                echo json_encode(["status" => "error", "message" => "Bus ID is required"]);
                exit;
            }

            $bus_id = $_GET['bus_id'];
            try {
                $occupiedSeats = $booking->getOccupiedSeats($bus_id);
                echo json_encode(["status" => "success", "occupied_seats" => $occupiedSeats]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }

        if (isset($_GET['bus_id'])) {
            // Get bookings for a specific bus
            $bus_id = $_GET['bus_id'];
            try {
                $bookings = $booking->getBookingsByBusId($bus_id);
                echo json_encode(["status" => "success", "bookings" => $bookings]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }

        if (isset($_GET['passenger_id'])) {
            // Get bookings for a specific passenger
            $passenger_id = $_GET['passenger_id'];
            try {
                $bookings = $booking->getBookingsByPassengerId($passenger_id);
                echo json_encode(["status" => "success", "bookings" => $bookings]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }

        if (isset($_GET['id'])) {
            // Get booking by ID
            $id = $_GET['id'];
            try {
                $bookingData = $booking->getBookingById($id);
                if ($bookingData) {
                    echo json_encode(["status" => "success", "booking" => $bookingData]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Booking not found"]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
            exit;
        }

        // Get all bookings
        try {
            $bookings = $booking->getAllBookingDetails();
            echo json_encode(["status" => "success", "bookings" => $bookings]);
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    case 'POST':
        if (!isset($inputData['passenger_id'], $inputData['bus_id'], $inputData['reserve_name'], 
                  $inputData['passenger_type'], $inputData['seat_number'])) {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            exit;
        }

        // Check if the seat is already taken
        $occupiedSeats = $booking->getOccupiedSeats($inputData['bus_id']);
        if (in_array($inputData['seat_number'], $occupiedSeats)) {
            echo json_encode(["status" => "error", "message" => "Seat is already taken"]);
            exit;
        }

        // Check if the bus exists and has available seats
        $busData = $bus->getBusById($inputData['bus_id']);
        if (!$busData) {
            echo json_encode(["status" => "error", "message" => "Bus not found"]);
            exit;
        }

        if ($busData['available_seats'] <= 0) {
            echo json_encode(["status" => "error", "message" => "No available seats on this bus"]);
            exit;
        }

        // Generate a unique reference number
        $reference = $booking->generateReference();
        
        // Handle ID upload if provided
        $id_upload_path = null;
        if (isset($_FILES['id_file']) && $_FILES['id_file']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = "../uploads/ids/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $filename = uniqid() . "_" . basename($_FILES['id_file']['name']);
            $target_file = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['id_file']['tmp_name'], $target_file)) {
                $id_upload_path = $target_file;
            }
        }

        try {
            $result = $booking->createNewBooking(
                $inputData['passenger_id'],
                $inputData['bus_id'],
                $inputData['reserve_name'],
                $inputData['passenger_type'],
                $inputData['seat_number'],
                $id_upload_path,
                $reference,
                $inputData['remarks'] ?? '',
                $inputData['status'] ?? 'pending'
            );

            if ($result) {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Booking created successfully", 
                    "booking_id" => $result,
                    "reference" => $reference
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to create booking"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    case 'PUT':
        if (!isset($_GET['id'])) {
            echo json_encode(["status" => "error", "message" => "Booking ID is required"]);
            exit;
        }

        $id = $_GET['id'];
        
        // Update booking status
        if (isset($inputData['status'])) {
            try {
                $result = $booking->updateBookingStatus($id, $inputData['status']);
                if ($result) {
                    echo json_encode(["status" => "success", "message" => "Booking status updated successfully"]);
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to update booking status"]);
                }
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Status is required for update"]);
        }
        exit;

    case 'DELETE':
        if (!isset($_GET['id'])) {
            echo json_encode(["status" => "error", "message" => "Booking ID is required for deletion"]);
            exit;
        }

        $id = $_GET['id'];
        try {
            $result = $booking->deleteBooking($id);
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Booking deleted successfully"]);
            } else {
                echo json_encode(["status" => "error", "message" => "Failed to delete booking or booking not found"]);
            }
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
        exit;

    default:
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        exit;
}
?>