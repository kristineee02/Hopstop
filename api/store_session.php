<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

session_start();

$postData = file_get_contents("php://input");
$data = json_decode($postData, true);

if (isset($data["email"]) && isset($data["user_type"])) {
    $_SESSION["userEmail"] = $data["email"];
    $_SESSION["user_type"] = $data["user_type"];
    if ($data["user_type"] === "passenger" && isset($data["passenger_id"])) {
        $_SESSION["userId"] = $data["passenger_id"];
    } elseif ($data["user_type"] === "admin" && isset($data["admin_id"])) {
        $_SESSION["userId"] = $data["admin_id"];
    }
    echo json_encode([
        "status" => "success",
        "message" => "Session stored successfully"
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Missing required session data"
    ]);
}
?>