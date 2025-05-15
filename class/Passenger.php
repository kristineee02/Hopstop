<?php
class Passenger{
    private $conn;
    private $table = "passenger";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function addUser($firstName, $lastName, $email, $password){
        $passengerQuery = "INSERT INTO " . $this->table . " (first_name, last_name, email, password, picture) VALUES (:firstName, :lastName, :email, :password, :profilePic)";
        $stmt = $this->conn->prepare($passengerQuery);

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $profilePic = null;
        $stmt->execute([":firstName" => $firstName, ":lastName" => $lastName, ":email" => $email, ":password" => $hashed_password, ":profilePic" => $profilePic]);
        
    }
    
    public function getPassenger(){
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPassengerById($id) {
        $query = "SELECT passenger_id, first_name, last_name, email, picture FROM " . $this->table . " WHERE passenger_id = :id";
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
            // Return user information for session creation
            return [
                "passenger_id" => $user["passenger_id"],
                "email" => $user["email"],
                "first_name" => $user["first_name"],
                "last_name" => $user["last_name"],
                "user_type" => "passenger"
            ];
        }
        return false;
    }

    public function updateProfile($passengerId, $firstName, $lastName, $picture = null) {
        // Create SQL query with optional picture update
        $query = "UPDATE " . $this->table . " SET first_name = :firstName, last_name = :lastName";
        if ($picture !== null) {
            $query .= ", picture = :picture";
        }
        $query .= " WHERE passenger_id = :passengerId";
        
        $stmt = $this->conn->prepare($query);

        // Bind parameters
        $params = [
            ':firstName' => $firstName,
            ':lastName' => $lastName,
            ':passengerId' => $passengerId
        ];
        
        if ($picture !== null) {
            $params[':picture'] = $picture;
        }
        
        if ($stmt->execute($params)) {
            return true;
        } else {
            throw new Exception("SQL Error: " . implode(", ", $stmt->errorInfo()));
        }
    }

    public function deletePassenger($passengerId) {
        $query = "DELETE FROM passenger WHERE passenger_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $passengerId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return $stmt->rowCount() > 0;
        } else {
            return false;
        }
    }
}