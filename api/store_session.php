<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Store session data from login
    if (isset($data["passenger_id"]) && isset($data["email"])) {
        $_SESSION["userId"] = $data["passenger_id"];
        $_SESSION["userEmail"] = $data["email"];
        $_SESSION["user_type"] = "passenger";
        
        echo json_encode([
            "status" => "success",
            "message" => "Session stored successfully",
            "passengerId" => $data["passenger_id"]
        ]);
    } 
    // Handle admin sessions if needed
    elseif (isset($data["admin_id"]) && isset($data["email"])) {
        $_SESSION["userId"] = $data["admin_id"];
        $_SESSION["userEmail"] = $data["email"];
        $_SESSION["user_type"] = "admin";
        
        echo json_encode([
            "status" => "success",
            "message" => "Session stored successfully",
            "adminId" => $data["admin_id"]
        ]);
    } 
    else {
        echo json_encode([
            "status" => "error",
            "message" => "Missing required session data"
        ]);
    }
} 
// Handle GET request to retrieve session data
else if ($_SERVER["REQUEST_METHOD"] === "GET") {
    if (isset($_SESSION["userId"]) && isset($_SESSION["user_type"])) {
        $response = [
            "status" => "success",
            "message" => "Session retrieved successfully",
            "userId" => $_SESSION["userId"],
            "userType" => $_SESSION["user_type"]
        ];
        
        // Add type-specific ID for convenience
        if ($_SESSION["user_type"] === "passenger") {
            $response["passengerId"] = $_SESSION["userId"];
        } elseif ($_SESSION["user_type"] === "admin") {
            $response["adminId"] = $_SESSION["userId"];
        }
        
        echo json_encode($response);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "No active session"
        ]);
    }
}
?>