<?php
class Passenger{
    private $conn;
    private $table = "passenger";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addUser($firstName, $lastName, $email, $password) {
        // Check for existing email
        $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Email already exists");
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO " . $this->table . " (first_name, last_name, email, password) VALUES (:firstName, :lastName, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $params = [
            ":firstName" => $firstName,
            ":lastName" => $lastName,
            ":email" => $email,
            ":password" => $hashed_password
        ];

        if ($stmt->execute($params)) {
            return true;
        }
        return false;
    }

    
    public function getPassenger(){
        $query = "SELECT * FROM " . $this->table . "WHERE passenger_id = :id";
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

        if ($user && password_verify($password, $user["password"])) {
            return $user;
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
