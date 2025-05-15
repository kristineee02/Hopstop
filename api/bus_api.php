<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include 'database.php';
include_once '../class/Bus.php';

$database = new Database();
$db = $database->getConnection();
$bus = new Bus($db);

if (!$db) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database connection failed"]);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

$inputData = null;
if ($method === 'POST' || $method === 'PUT') {
    $inputJSON = file_get_contents('php://input');
    $inputData = json_decode($inputJSON, true);
    if ($inputData === null && json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(["status" => "error", "message" => "Invalid JSON: " . json_last_error_msg()]);
        exit;
    }
}

switch ($method) {
    case 'GET':
        if (isset($_GET['action']) && $_GET['action'] === 'search') {
            if (!isset($_GET['location']) || !isset($_GET['destination'])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Location and destination are required"]);
                exit;
            }
            $location = $_GET['location'];
            $destination = $_GET['destination'];
            try {
                $query = "SELECT DISTINCT * FROM bus WHERE location = :location AND destination = :destination";
                $stmt = $db->prepare($query);
                $stmt->bindValue(':location', $location, PDO::PARAM_STR);
                $stmt->bindValue(':destination', $destination, PDO::PARAM_STR);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo json_encode(["status" => "success", "buses" => $results]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
            }
            exit;
        }
        $filters = [];
        if (isset($_GET['location'])) $filters['location'] = $_GET['location'];
        if (isset($_GET['destination'])) $filters['destination'] = $_GET['destination'];
        if (isset($_GET['bus_type'])) $filters['bus_type'] = $_GET['bus_type'];
        if (isset($_GET['id'])) {
            $busData = $bus->getBusById($_GET['id']);
            if ($busData) {
                echo json_encode(["status" => "success", "bus" => $busData]);
            } else {
                http_response_code(404);
                echo json_encode(["status" => "error", "message" => "Bus not found"]);
            }
            exit;
        } else {
            $result = empty($filters) ? $bus->getAllBusDetails() : $bus->getFilteredBuses($filters);
            echo json_encode(["status" => "success", "buses" => $result]);
            exit;
        }

    case 'POST':
        if (!isset($inputData['location'], $inputData['destination'], 
                  $inputData['departure_time'], $inputData['arrival_time'], 
                  $inputData['available_seats'], $inputData['bus_type'], 
                  $inputData['price'], $inputData['bus_number'], $inputData['status'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
            exit;
        }
        $validBusTypes = ['Air-Conditioned', 'Non Air-Conditioned'];
        if (!in_array($inputData['bus_type'], $validBusTypes)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid bus type. Must be 'Air-Conditioned' or 'Non Air-Conditioned'"]);
            exit;
        }
        try {
            $result = $bus->createNewBus(
                $inputData['location'],
                $inputData['destination'],
                $inputData['departure_time'],
                $inputData['arrival_time'],
                $inputData['available_seats'],
                $inputData['bus_type'],
                $inputData['price'],
                $inputData['bus_number'],
                $inputData['status']
            );
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Bus created successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["status" => "error", "message" => "Failed to create bus"]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to create bus: " . $e->getMessage()]);
        }
        exit;

    case 'PUT':
        if (!isset($inputData['id'], $inputData['location'], $inputData['destination'], 
                  $inputData['departure_time'], $inputData['arrival_time'], 
                  $inputData['available_seats'], $inputData['bus_type'], 
                  $inputData['price'], $inputData['bus_number'], $inputData['status'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Missing required fields for update"]);
            exit;
        }
        $validBusTypes = ['Air-Conditioned', 'Non Air-Conditioned'];
        if (!in_array($inputData['bus_type'], $validBusTypes)) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Invalid bus type. Must be 'Air-Conditioned' or 'Non Air-Conditioned'"]);
            exit;
        }
        try {
            $result = $bus->updateBus(
                $inputData['id'],
                $inputData['location'],
                $inputData['destination'],
                $inputData['departure_time'],
                $inputData['arrival_time'],
                $inputData['available_seats'],
                $inputData['bus_type'],
                $inputData['price'],
                $inputData['bus_number'],
                $inputData['status']
            );
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Bus updated successfully"]);
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Failed to update bus. Bus ID may not exist."]);
            }
        } catch (Exception $e) {
            error_log("Update bus error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to update bus: " . $e->getMessage()]);
        }
        exit;

    case 'DELETE':
        if (!isset($_GET['id']) || empty($_GET['id'])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Bus ID is required for deletion"]);
            exit;
        }
        $id = $_GET['id'];
        try {
            // Check for associated bookings
            $checkQuery = "SELECT COUNT(*) FROM bookings WHERE bus_id = ?";
            $checkStmt = $db->prepare($checkQuery);
            $checkStmt->bindParam(1, $id, PDO::PARAM_INT);
            $checkStmt->execute();
            $bookingCount = $checkStmt->fetchColumn();
            if ($bookingCount > 0) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Cannot delete bus because it has $bookingCount associated booking(s)."]);
                exit;
            }
            $result = $bus->deleteBus($id);
            if ($result) {
                echo json_encode(["status" => "success", "message" => "Bus deleted successfully"]);
            } else {
                $checkQuery = "SELECT COUNT(*) FROM bus WHERE bus_id = ?";
                $checkStmt = $db->prepare($checkQuery);
                $checkStmt->bindParam(1, $id);
                $checkStmt->execute();
                $count = $checkStmt->fetchColumn();
                $message = $count == 0 ? "Bus with ID $id not found" : "Failed to delete bus due to database error";
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => $message]);
            }
        } catch (Exception $e) {
            error_log("Delete bus error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Failed to delete bus: " . $e->getMessage()]);
        }
        exit;

    default:
        http_response_code(405);
        echo json_encode(["status" => "error", "message" => "Method not allowed"]);
        exit;
}
?>