<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';
include '../class/seat.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

if (!$db) {
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

// Initialize Seat class
$seat = new Seat($db);

// Get HTTP method
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Handle GET requests
        if (isset($_GET['action']) && $_GET['action'] == 'initialize') {
            // Initialize seats if needed
            $result = $seat->initializeSeats();
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Seats initialized successfully"]);
            } else {
                echo json_encode(["status" => "info", "message" => "Seats already exist or initialization failed"]);
            }
        } else if (isset($_GET['action']) && $_GET['action'] == 'seatmap') {
            // Get seat map for the UI
            $seatMap = $seat->getSeatMap();
            echo json_encode(["status" => "success", "data" => $seatMap]);
        } else if (isset($_GET['id'])) {
            // Get seat by ID
            $seatData = $seat->getSeatById($_GET['id']);
            if ($seatData) {
                echo json_encode(["status" => "success", "data" => $seatData]);
            } else {
                echo json_encode(["status" => "error", "message" => "Seat not found"]);
            }
        } else if (isset($_GET['seat_number'])) {
            // Get seat by number
            $seatData = $seat->getSeatByNumber($_GET['seat_number']);
            if ($seatData) {
                echo json_encode(["status" => "success", "data" => $seatData]);
            } else {
                echo json_encode(["status" => "error", "message" => "Seat not found"]);
            }
        } else if (isset($_GET['status'])) {
            // Get seats by status
            $seatsData = $seat->getSeatsByStatus($_GET['status']);
            echo json_encode(["status" => "success", "data" => $seatsData]);
        } else {
            // Get all seats
            $seats = $seat->getAllSeats();
            echo json_encode(["status" => "success", "data" => $seats]);
        }
        break;
    
    case 'POST':
        // Handle POST requests
        $data = json_decode(file_get_contents("php://input"), true);
        
        if (!$data) {
            echo json_encode(["status" => "error", "message" => "Invalid JSON input"]);
            exit;
        }
        
        if (isset($data['action']) && $data['action'] == 'book' && isset($data['seats']) && isset($data['user_id'])) {
            // Book multiple seats
            $result = $seat->bookSeats($data['seats'], $data['user_id']);
            
            if ($result) {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Seats booked successfully", 
                    "booked_seats" => $data['seats']
                ]);
            } else {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Failed to book seats. Some seats may already be unavailable."
                ]);
            }
        } else if (isset($data['seat_number']) && isset($data['status'])) {
            // Update seat status
            $result = $seat->updateSeatStatus($data['seat_number'], $data['status']);
            
            if ($result) {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Seat status updated successfully"
                ]);
            } else {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Failed to update seat status"
                ]);
            }
        } else if (isset($data['seat_number']) && isset($data['seat_status'])) {
            // Add new seat
            $user_id = isset($data['user_id']) ? $data['user_id'] : null;
            $result = $seat->addSeat($data['seat_number'], $user_id, $data['seat_status']);
            
            if ($result) {
                echo json_encode([
                    "status" => "success", 
                    "message" => "Seat added successfully"
                ]);
            } else {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Failed to add seat"
                ]);
            }
        } else {
            echo json_encode([
                "status" => "error", 
                "message" => "Missing required fields"
            ]);
        }
        break;
        
    default:
        echo json_encode([
            "status" => "error", 
            "message" => "Method not supported"
        ]);
}
?>