<?php
session_start();
include("connection.php");
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email='$email'";
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $res = mysqli_fetch_assoc($result);

        if ($password === $res['password']) {
            $_SESSION['id'] = $res['id'];
            $_SESSION['full_name'] = $res['full_name'];
            $_SESSION['role'] = $res['role'];
            header("Location: user.php");
            exit;
        } else {
            
            $error_message = "Invalid Email or Password";
        }
    } else {
        $error_message = "User Does Not Exist!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Pet Adoption System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            background-color: #F5EEC8;
        }
    </style>
</head>

<body>

    <div class="login-wrapper">

        <div class="login-container">

            <div class="login-image">
                <img src="assets/images/login1.jpg" alt="Pet Image">
            </div>

            <div class="login-form">

                <div class="login-logo">
                    <img src="assets/images/logo.png" alt="Logo">
                </div>

                <h2 class="login-title">Pet Adoption System</h2>

                <form method="POST" action="login.php">
                    
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    
                    <?php if (!empty($error_message)) { ?>
                        <div class="error-message">
                            <?php echo $error_message; ?>
                        </div>
                    <?php } ?><br>

                    <!-- <div class="forgot-password">
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div> -->
                    
                    <button type="submit" class="login-submit">Login</button>
                </form>

                <div class="signup-link">
                    <a href="register.php">Don't have an account? Sign Up</a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>