<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

include "database.php";
include "../class/Passenger.php";

$database = new Database();
$db = $database->getConnection();

$passenger = new User($db);

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "POST") {
    $postData = file_get_contents("php://input");
    $data = json_decode($postData, true);

    // Validate input
    if (empty($data["firstName"]) || empty($data["lastName"]) || 
        empty($data["email"]) || empty($data["password"])) {
        echo json_encode(["status" => "error", "message" => "All fields are required"]);
        exit;
    }

    // Trim inputs to prevent whitespace issues
    $firstName = trim($data["firstName"]);
    $lastName = trim($data["lastName"]);
    $email = trim($data["email"]);
    $password = $data["password"];
    
    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Invalid email format"]);
        exit;
    }
    
    // Check if email already exists in either passenger or admin table
    // First check passenger table
    $checkEmailQuery = "SELECT * FROM passenger WHERE email = :email";
    $stmt = $db->prepare($checkEmailQuery);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already in use"]);
        exit;
    }
    
    // Then check admin table
    $checkAdminEmailQuery = "SELECT * FROM admin WHERE email = :email";
    $stmt = $db->prepare($checkAdminEmailQuery);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "error", "message" => "Email already in use"]);
        exit;
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    try {
        // Add the user
        $passenger->addUser($firstName, $lastName, $email, $hashedPassword);
        echo json_encode(["status" => "success", "message" => "User registered successfully"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Registration failed", "error" => $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>