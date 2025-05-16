<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up Page</title>

    <style>
              body {
    font-family: 'Montserrat', sans-serif;
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    background-color: #f8f9fa;
    color: #333;
}

.splitscreen {
    display: flex;
    height: 100vh;
    width: 100%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
    box-sizing: border-box;
}

.welcome {
    color: white;
    width: 60%;
    background-image: url('../images/Homepage_image1.png'); 
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    text-align: center;
    overflow: hidden;
}

.overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.4);
    z-index: 1;
}

.welcome h1 {
    font-family: 'Montserrat', sans-serif;
    font-style: italic;
    margin-bottom: 10px;
    font-weight: 300;
    z-index: 2;
    letter-spacing: 1px;
    text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
}

.welcome span {
    font-weight: 600;
    font-size: 44px;
}

.TF {
    color: #222;
    font-weight: 700;
    font-size: 54px;
    background-color:rgb(255, 149, 255);
    padding: 8px 20px;
    margin-top: 5px;
    z-index: 2;
    border-radius: 4px;
    box-shadow: 0 4px 10px rgba(243, 28, 28, 0.15);
    letter-spacing: 1px;
    transition: transform 0.3s ease;
}

.TF:hover {
    transform: scale(1.05);
}

.signup {
    width: 40%;
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    background-color: white;
}

.signup h2 {
    font-size: 32px;
    font-weight: 600;
    text-align: center;
    width: 80%;
    padding-bottom: 20px;
    color: #333;
    position: relative;
}

.signup h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 50px;
    height: 3px;
    background-color: #FFE295;
}

.form {
    width: 80%;
    display: flex;
    flex-direction: column;
    align-items: center;
}

.name {
    display: flex;
    gap: 10px;
    width: 100%;
}

.name input {
    width: 50%;
}

.form input {
    width: 100%;
    padding: 12px 15px;
    margin: 12px 0;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    font-size: 14px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}



.form input:focus {
    outline: none;
    border-color: #FFE295;
    box-shadow: 0 0 0 2px rgba(255, 226, 149, 0.3);
}

.form input::placeholder {
    color: #aaa;
}

.form button {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    margin: 15px 0;
    background-color:rgb(255, 149, 255);
    color: #333;
    transition: all 0.3s ease;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.form button:hover {
    background-color:rgb(191, 44, 211);
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
}

.form button:active {
    transform: translateY(0);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.login {
    text-align: center;
    font-size: 14px;
    margin-top: 15px;
    color: #666;
}

.login a {
    color: #333;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
    padding-bottom: 2px;
    border-bottom: 1px solid transparent;
}

.login a:hover {
    color: #000;
    border-bottom: 1px solid #FFE295;
}

.name{
    width: 107%;
}

@media (max-width: 768px) {
    .splitscreen {
        flex-direction: column;
    }
    
    .welcome, .signup {
        width: 100%;
        min-height: 60px;
    }
    
    .form {
        width: 90%;
    }
    
}


        </style>

</head>

<body>
    <div class="splitscreen">
        <div class="welcome">
            <h1><span>WELCOME</span> to</h1>
            <div class="TF">HOPSTOP</div>
            <div class="overlay"></div>
        </div>

        <div class="signup">
            <h2>Admin - Sign up</h2>
            <form class="form" id="formId">
                <div class="name">
                    <input type="text" id="firstName" placeholder="First Name" required>
                    <input type="text" id="lastName" placeholder="Last Name" required>
                </div>
                <input type="email" id="email" placeholder="Email Address" required>
                <input type="password" id="password" placeholder="Password" required>
                <input type="text" id="address" placeholder="Address" required>
                <button type="submit" name="submit" id="submit">Create Account</button>
            </form>
            <div class="login">Already have an account? <a href="../Login/UserLogin.php">Log In</a></div>
        </div>
    </div>
    <script src="../js/adminsignup.js"></script>
</body>
</html>