<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

include "database.php";
include "../class/Passenger.php";
include "../class/Admin.php";

$database = new Database();
$db = $database->getConnection();

$passenger = new User($db);
$admin = new Admin($db);

$method = $_SERVER["REQUEST_METHOD"];

switch ($method) {
    case "GET":
        $passengers = $passenger->getPassenger();
        echo json_encode(["status" => "success", "passengers" => $passengers]);
        break;
    case "POST":
        $postData = file_get_contents("php://input");
        $data = json_decode($postData, true);
    
        $email = $data["email"] ?? null;
        $password = $data["password"] ?? null;
    
        if ($email && $password) {
            $adminData = $admin->admin_login($email, $password);
            if ($adminData) {
                echo json_encode(["status" => "success", "accountType" => "admin", "admins" => $adminData]);
            } 
            else if ($passengerData = $passenger->user_login($email, $password)) {
                echo json_encode(["status" => "success", "accountType" => "passenger", "passengers" => $passengerData]);
            } 
            // Neither admin nor passenger - login failed
            else {
                echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
            }
        } else {
            echo json_encode(["status" => "error", "message" => "Missing email or password"]);
        }
        break;
}
?>