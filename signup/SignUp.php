<?php
   include "../api/database.php";
   include '../class/Passenger.php';
   $database = new Database();
   $conn = $database->getConnection();

   $user = new User($conn);

   if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])){
       $firstName = $_POST["firstName"];
       $lastName = $_POST["lastName"];
       $email = $_POST["email"];
       $password = $_POST["password"];

       $user->addUser($firstName, $lastName, $email, $password);
       header("Location: ../Login/Login.php");
   }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up HopStop</title>
    <link rel="stylesheet" href="../style/style2.css">
</head>
<body>
    <div class="container">
        
        <h2>Sign Up</h2>
        <div class="profile-pic">
            <img src="../images/Jennie Kim.jpg" alt="Profile Picture">
            <input type="file" accept="image/*">
        </div>
        <form method = "POST">
        <div class="input-box">
            <label>First Name</label>
            <input type="text" name= "firstName"  required>
        </div>
        <div class="input-box">
            <label>Last Name</label>
            <input type="text" name="lastName" required>
        </div>
        <div class="input-box">
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div class="input-box">
            <label>Create Password</label>
            <input type="password" name="password" required>
    </form>

        <div class="btn">
            <input type="submit" name="submit" value="Sign In" class="button"> 
        </div>
        <div class="signin">
            Already have an account? <a href="../Login/LogIn.php">Sign in</a>
        </div>
    </div>
</body>
</html>