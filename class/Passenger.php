<?php
class User{
    private $conn;
    private $table = "passenger";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addUser($firstName, $lastName, $email, $password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $query = "INSERT INTO " . $this->table . " (first_name, last_name, email, password) VALUES (:firstName, :lastName, :email, :password)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([":firstName" => $firstName, ":lastName" => $lastName, ":email" => $email, ":password" => $hashed_password]);
    }

    
    public function getPassenger(){
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPassengerById($id) {
        $query = "SELECT first_name, last_name, email, picture FROM passenger WHERE passenger_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getProfilePicture($id){
        $query = "SELECT picture FROM " . $this->table . " WHERE passenger_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } 

    public function user_login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if (password_verify($password, $user["password"])) {
                return $user;
            } else {
                error_log("Password verification failed for email: $email");
            }
        } else {
            error_log("No user found with email: $email");
        }

        return false;
    }


    public function updateProfile($passengerId, $firstName, $lastName, $profilePic) {
        $query = "UPDATE " . $this->table . " SET first_name = :firstName, last_name = :lastName" . 
                 ($profilePic !== null ? ", picture = :profilePic" : "") . 
                 " WHERE passenger_id = :passenger_id";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':firstName', $firstName);
        $stmt->bindParam(':lastName', $lastName);
        if ($profilePic !== null) {
            $stmt->bindParam(':profilePic', $profilePic);
        }
        $stmt->bindParam(':passengerId', $passengerId, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            throw new Exception("SQL Error: " . implode(", ", $stmt->errorInfo()));
        }
    }
}
