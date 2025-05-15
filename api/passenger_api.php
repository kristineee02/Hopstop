<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include "database.php";
include "../class/Freelancer.php";

session_start();
$database = new Database();
$db = $database->getConnection();

$passenger = new Passenger($db);

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case 'GET':
        if (isset($_GET["userId"])) {
            $passengerData = $passenger->getPassengerById($_GET["userId"]);
            echo json_encode(["status" => "success", "passengerData" => $passengerData]);
        } else {
            $passengers = $passenger->getPassenger();
            echo json_encode(["status" => "success", "passengers" => $passengers]);
        }
        break;

    case 'POST':
        if (!isset($_SESSION['userId'])) {
            echo json_encode(["status" => "error", "message" => "User not authenticated"]);
            exit;
        }

        if (isset($_POST['firstName'], $_POST['lastName'], $_POST['email'])) {
            $userId = $_SESSION['userId'];
            $firstName = $_POST['firstName'];
            $lastName = $_POST['lastName'];
            $address = $_POST['email'];
            $profilePic = null;

            if (isset($_FILES['picture']) && $_FILES['picture']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = '../Uploads/';
                $fileName = uniqid() . '-' . basename($_FILES['picture']['name']);
                $uploadPath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES['picture']['tmp_name'], $uploadPath)) {
                    $profilePic = $fileName;
                } else {
                    echo json_encode(["status" => "error", "message" => "Failed to upload profile picture"]);
                    exit;
                }
            }

            try {
                $passenger->updateProfile($userId, $firstName, $lastName, $picture);
                echo json_encode(["status" => "success", "message" => "Profile updated successfully"]);
            } catch (Exception $e) {
                echo json_encode(["status" => "error", "message" => "Update failed: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Missing required fields"]);
        }
        break;

    default:
        echo json_encode(["status" => "error", "message" => "Invalid request method"]);
        break;
}
?>