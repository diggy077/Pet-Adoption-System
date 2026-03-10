<?php
session_start();
include("connection.php");
include("includes/functions.php");

$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = $_POST["email"];
    $password = $_POST["password"];

    if (empty($email) || empty($password)) {
        $error_message = "All fields are required.";
    }

    if (empty($error_message)) {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Enter a valid email address.";
        }
    }

    if (empty($error_message)) {
        $email = sanitize_email($email);

        $stmt = $con->prepare("SELECT id, full_name, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $res = $result->fetch_assoc();

            if (verify_password($password, $res['password'])) {
                $_SESSION['id']        = $res['id'];
                $_SESSION['full_name'] = $res['full_name'];
                $_SESSION['role']      = $res['role'];

                header("Location: user.php");
                exit;
            } else {
                $error_message = "Invalid Email or Password.";
            }
        } else {
            $error_message = "User does not exist.";
        }
        $stmt->close();
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
                        <input type="email" id="email" name="email" placeholder="Enter your email">
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password">
                    </div>

                    <?php
                    if (!empty($error_message)) {
                        echo '<div class="error-message">' . e($error_message) . '</div>';
                    }
                    ?><br>

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