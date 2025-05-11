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
        <form method="POST" id="formId">
            <div class="input-box">
                <label>Email</label>
                <input type="email" id="email" name="email" required />
            </div>
            <div class="input-box">
                <label>Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="forgot">
                <a href="#">Forgot Password?</a>
            </div>
            <div class="btn">
                <input type="submit" name="submit" value="Sign In" class="button"> 
            </div>
        </form>
        <div class="signup">
            Don't have an account? <a href="../signup/SignUp.php">Sign up</a>
        </div>
    </div>
    <script src="../js/login.js"></script>
</body>
</html>