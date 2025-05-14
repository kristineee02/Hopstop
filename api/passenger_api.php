<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "database.php";
include "../class/Passenger.php";

$database = new Database();
$db = $database->getConnection();

$passenger = new Passenger($db);

$method = $_SERVER["REQUEST_METHOD"];

if ($method !== "POST") {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Method not allowed"]);
    exit;
}

$postData = file_get_contents("php://input");
$data = json_decode($postData, true);

if (empty($data["firstName"]) || empty($data["lastName"]) || empty($data["email"]) || empty($data["password"])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "All fields are required"]);
    exit;
}


try {
    $result = $passenger->addUser($data["firstName"], $data["lastName"], $data["email"], $data["password"]);
    if ($result) {
        http_response_code(201);
        echo json_encode(["status" => "success", "message" => "User created successfully"]);
    } else {
        http_response_code(500);
        echo json_encode(["status" => "error", "message" => "Failed to create user"]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>