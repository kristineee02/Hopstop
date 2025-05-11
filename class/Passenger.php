<?php
class User{
    private $conn;
    private $table = "passenger";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addUser($firstName, $lastName, $email, $password){
        // Hash the password before storing
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . " (first_name, last_name, email, password) VALUES (:firstName, :lastName, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":firstName" => $firstName, ":lastName" => $lastName, ":email" => $email, ":password" => $hashed_password]);
    }

    public function getPassenger(){
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPassengerById($id){
        $query = "SELECT * FROM " . $this->table . " WHERE passenger_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProfilePicture($id){
        $query = "SELECT picture FROM " . $this->table . " WHERE passenger_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } 
 
    public function login($email, $password) {
        // Trim input to prevent whitespace issues
        $email = trim($email);
        
        // Debug information (remove in production)
        error_log("Attempting passenger login for email: " . $email);
        
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $email]);
        
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug information (remove in production)
            error_log("Found user with email: " . $email);
            
            // Check if password is already hashed in the database
            if ($user && password_verify($password, $user["password"])) {
                error_log("Password verified successfully");
                return $user;
            } else {
                // As a fallback, try direct comparison (for unhashed passwords)
                // Note: This is for backward compatibility only
                if ($user && $password === $user["password"]) {
                    error_log("Password matched directly (unhashed)");
                    return $user;
                }
                error_log("Password verification failed");
            }
        } else {
            error_log("No user found with email: " . $email);
        }
        return false;
    }
    
    public function updateProfile($Id, $firstName, $lastName, $profilePic) {
        $query = "UPDATE " . $this->table . " SET first_name = :firstName, last_name = :lastName" . 
                 ($profilePic !== null ? ", picture = :profilePic" : "") . 
                 " WHERE passenger_id = :passenger_id";
        
        $stmt = $this->conn->prepare($query);
    
        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        if ($profilePic !== null) {
            $stmt->bindParam(':profilePic', $profilePic);
        }
        $stmt->bindParam(':passenger_id', $Id, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("SQL Error: " . implode(", ", $stmt->errorInfo()));
        }
    }
}
?>