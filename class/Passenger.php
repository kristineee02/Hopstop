<?php
    class User{
        private $conn;
        private $table = "passenger";

        public function __construct($db)
        {
            $this->conn = $db;
        }

        public function addUser($firstName, $lastName, $email, $password){
            $query = "INSERT INTO " . $this->table . " (first_name, last_name, email, password) VALUES (:firstName, :lastName, :email, :password)";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([":firstName" => $firstName, ":lastName" => $lastName, ":email" => $email, ":password" => $password]);
        }
     
        public function login($email, $password){
            $query = "SELECT * FROM " . $this->table . " WHERE email = :email LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([":email" => $email]);
            if ($stmt->rowCount() == 1){
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if($password == $user["password"]){
                    return $user;
                }
            }
            return false;
        }
    }


?>