<?php
include '../api/database.php';
include '../class/Passenger.php';

$database = new Database();
$conn = $database->getConnection();

$user = new User($conn);

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['submit'])) {
    $Email = $_POST['email'];
    $Password = $_POST['password'];

    $user->login($email, $password);
    header("Location: ../userview/User.php");
    exit();
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HopStop LogIn</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
<body>
    <div class="container">
        <div class="logo">HopStop</div>
        <br>
        <div class="welcome">Welcome Back!</div>
        <p>Sign in to continue</p>
        <form method="POST">
        <div class="input-box">
            <label>Email</label>
            <input type="email" name="email" required />
        </div>
        <div class="input-box">
            <label>Password</label>
            <input type="password" name="password" required>
        </div>

        <div class="forgot">
            <a href="#">Forgot Password?</a>
        </div>
        
        <div class="btn">
            <input type="submit" name="submit" value="Sign Up" class="button"> 
        </div>
        </form>
       

        <div class="signup">
            Don't have an account? <a href="../signup/SignUp.php">Sign up</a>
        </div>
    </div>
</body>
</html>