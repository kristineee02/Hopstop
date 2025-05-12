<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

$postData = file_get_contents("php://input");
$data = json_decode($postData, true);

if (!empty($data)) {
    $_SESSION['email'] = $data['email'];
    $_SESSION['user_type'] = $data['user_type'];
    
    // Store ID based on user type
    if ($data['user_type'] === 'passenger' && isset($data['passenger_id'])) {
        $_SESSION['passenger_id'] = $data['passenger_id'];
    } else if ($data['user_type'] === 'admin' && isset($data['admin_id'])) {
        $_SESSION['admin_id'] = $data['admin_id'];
    }
    
    echo json_encode(["status" => "success", "message" => "Session stored successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "No data received"]);
}
?>