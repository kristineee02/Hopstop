<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include "database.php";
include "../class/Passenger.php";

session_start();
$database = new Database();
$db = $database->getConnection();

$passenger = new Passenger($db);

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case 'GET':
        if (!isset($_SESSION["userId"])) {
            echo json_encode(["status" => "error", "message" => "User not authenticated"]);
            exit;
        }
        
        // Get passenger by ID
        if (isset($_GET["passengerId"])) {
            $passengerId = $_GET["passengerId"];
            $passengerData = $passenger->getPassengerById($passengerId);
            
            if ($passengerData) {
                echo json_encode([
                    "status" => "success",
                    "passengerData" => $passengerData
                ]);
            } else {
                echo json_encode(["status" => "error", "message" => "Passenger not found"]);
            }
        } else {
            // Get all passengers (admin function)
            $passengers = $passenger->getPassenger();
            echo json_encode(["status" => "success", "passengers" => $passengers]);
        }
        break;

    case 'POST':
        if (!isset($_SESSION["userId"])) {
            echo json_encode(["status" => "error", "message" => "User not authenticated"]);
            exit;
        }

        $passengerId = $_SESSION["userId"];
        
        // Handle profile update
        if (isset($_POST['firstName'], $_POST['lastName'])) {
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $picture = null;

            // Handle file upload if present
            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../Uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $fileName = uniqid() . '-' . basename($_FILES['picture']['name']);
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadPath)) {
                    $picture = $fileName;
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to upload profile picture"]);
                    exit;
                }
            }

            try {
                if ($passenger->updateProfile($passengerId, $firstName, $lastName, $picture)) {
                    echo json_encode([
                        "status" => "success", 
                        "message" => "Profile updated successfully"
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error", 
                        "message" => "Failed to update profile"
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error", 
                    "message" => "Update failed: " . $e->getMessage()
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
            "message" => "Invalid request method"
        ]);
        break;
}
?>