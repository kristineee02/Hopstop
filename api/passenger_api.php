<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, PUT");
header("Access-Control-Allow-Headers: Content-Type");

// Check for preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

session_start();
require_once '../api/database.php';
require_once '../class/Passenger.php';

try {
    $db = $database->connect();
} catch (Exception $e) {
    echo json_encode([
        "status" => "error",
        "message" => "Database connection error: " . $e->getMessage()
    ]);
    exit;
}

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case 'GET':
        if (isset($_GET["userId"])) {
            try {
                $passenger = new User($db);
                $passengerData = $passenger->getPassengerById($_GET["userId"]);
                
                if ($passengerData) {
                    echo json_encode([
                        "status" => "success",
                        "passengerData" => $passengerData
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Passenger not found"
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Server error: " . $e->getMessage()
                ]);
            }
        } else if (isset($_SESSION["userId"])) {
            // Return current session user ID
            echo json_encode([
                "status" => "success",
                "message" => "Session data retrieved",
                "userId" => $_SESSION["userId"]
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No active session"
            ]);
        }
        break;
        
    case 'POST':
        try {
            $postData = file_get_contents("php://input");
            $data = json_decode($postData, true);
        
            if (!isset($data["email"]) || !isset($data["password"])) {
                echo json_encode(["status" => "error", "message" => "Missing email or password"]);
                exit;
            }
        
            $passenger = new User($db);
            $user = $passenger->login($data["email"], $data["password"]);
        
            if ($user) {
                $_SESSION["userEmail"] = $data["email"];
                $_SESSION["userId"] = $user["passenger_id"];
                echo json_encode([
                    "status" => "success",
                    "message" => "Login successful",
                    "userId" => $user["passenger_id"]
                ]);
            } else {
                echo json_encode([
                    "status" => "error",
                    "message" => "Invalid email or password"
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Server error: " . $e->getMessage()
            ]);
        }
        break;
        
    case 'PUT':
        if (!isset($_SESSION["userId"])) {
            echo json_encode([
                "status" => "error",
                "message" => "Not authenticated"
            ]);
            exit;
        }
        
        try {
            $passenger = new User($db);
            $Id = $_SESSION["userId"];
            
            // For PUT requests with form data
            $firstName = $_POST["firstName"] ?? "";
            $lastName = $_POST["lastName"] ?? "";
            $pictureFileName = null;
            
            if (isset($_FILES["picture"]) && $_FILES["picture"]["error"] == 0) {
                $uploadDir = "../Uploads/";
                if (!file_exists($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $pictureFileName = time() . "_" . basename($_FILES["picture"]["name"]);
                $targetFile = $uploadDir . $pictureFileName;
                
                if (!move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile)) {
                    throw new Exception("Failed to upload profile picture");
                }
            }
            
            $result = $passenger->updateProfile(
                $Id,
                $firstName,
                $lastName,
                $pictureFileName
            );
            
            if ($result) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Profile updated successfully"
                ]);
            } else {
                throw new Exception("Failed to update profile");
            }
        } catch (Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            echo json_encode([
                "status" => "error",
                "message" => "Server error: " . $e->getMessage()
            ]);
        }
        break;
        
    default:
        echo json_encode([
            "status" => "error",
            "message" => "Invalid request method"
        ]);
}
?>