<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../style/login.css">
    <title>Sign Up Page</title>
</head>

<body>
    <div class="splitscreen">
        <div class="welcome">
            <h1><span>WELCOME</span> to</h1>
            <div class="TF">HOPSTOP</div>
            <div class="overlay"></div>
        </div>

        <div class="signup">
            <h2>Log In</h2>
            <form class="form" method="POST" id="formId">
                <input type="text" id="email" placeholder="Username or Email" required>
                <input type="password" id="password" placeholder="Password" required>
                <div class="forgot-password">Forgot password?</div>
                <button type="submit" name="submit">Log in</button>
            </form>
            <div class="login">New here? <a href="../signup/SignupAS.php">Sign Up</a></div>
        </div>
    </div>
    <script src="../js/login.js"></script>
</body>
</html>