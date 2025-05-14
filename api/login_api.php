<?php
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    include "database.php";
    include "../class/Admin.php";
    include "../class/Passenger.php";
    $database = new Database();
    $db = $database->getConnection();

    $admin = new Admin($db);
    $passenger = new Passenger($db);

    $method = $_SERVER["REQUEST_METHOD"];

    switch ($method){
        case "GET":
            $admins = $admin->getAdmin();
            echo json_encode(["status" => "success", "admins" => $admins]);
            break;
        case "POST":
            $postData = file_get_contents("php://input");
            $data = json_decode($postData, true);
        
            $email = $data["email"] ?? null;
            $password = $data["password"] ?? null;
        
            if ($email && $password) {
                if($admins = $admin->admin_login($email, $password)){
                    echo json_encode(["status" => "success", "accountType" => "admin", "admins" => $admins]);
                }else if($passengers = $passenger->user_login($email, $password)){
                    echo json_encode(["status" => "success", "accountType" => "passenger", "passengers" => $passengers]);
                }else{
                    echo json_encode(["status" => "error", "message" => "Invalid credentials"]);
                }
            } else {
                echo json_encode(["status" => "error", "message" => "Missing email or password"]);
            }
            break;
            
            
            
    }
?>