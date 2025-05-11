<?php
class Admin{
    private $conn;
    private $table = "admin";


    public function __construct($db)
    {
        $this->conn = $db;
        $this->create_default_admin();
    }


    public function addAdmin($firstName, $lastName, $email, $password){
        // Hash the password before storing
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


    public function create_default_admin() {
        $query = "SELECT * FROM " . $this->table . " LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
       
        if ($stmt->rowCount() == 0) {
            // Default admin credentials
            $firstName = "Hop";
            $lastName = "Stop";
            $email = "admin@gmail.com";
            $password = "Admin123";
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
           
            // Insert default admin
            $query = "INSERT INTO " . $this->table . "
                     (first_name, last_name, email, password)
                     VALUES (:firstName, :lastName, :email, :password)";
           
            $stmt = $this->conn->prepare($query);
            $params = [
                ":firstName" => $firstName,
                ":lastName" => $lastName,
                ":email" => $email,
                ":password" => $hashed_password
            ];
           
            if ($stmt->execute($params)) {
                return [
                    "message" => "Default admin created successfully",
                    "email" => $email,
                    "password" => $password // Only return this during initial setup
                ];
            }
        }
       
        return ["message" => "Admin already exists"];
    }
   
    public function admin_login($email, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([":email" => $email]);
   
        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if($user && password_verify($password, $user["password"])){
                return $user;
            }
        }
        return false;
    }
}
?>

