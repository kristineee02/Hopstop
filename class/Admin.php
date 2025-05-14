<?php
class Admin{
    private $conn;
    private $table = "admin";


    public function __construct($db)
    {
        $this->conn = $db;
    }


    public function addAdmin($firstName, $lastName, $email, $password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
       
        $query = "INSERT INTO " . $this->table . " (first_name, last_name, email, password) VALUES (:firstName, :lastName, :email, :password)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":firstName" => $firstName, ":lastName" => $lastName, ":email" => $email, ":password" => $hashed_password]);
    }


    public function getAdmin(){
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getAdminById($id){
        $query = "SELECT * FROM " . $this->table . " WHERE admin_id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":id" => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function admin_login($email, $password){
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user["password"])){
            return $user;
        }else{
            return false;

        }
    }


}
?>

