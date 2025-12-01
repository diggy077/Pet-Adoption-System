<?php
session_start();
include("includes/connection.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $password = $_POST["password"];
    $user_type = $_POST["user_type"]; 
    
    if ($user_type == "user") {
        $sql = "SELECT * FROM users WHERE email='$email' AND status='active'";
    } else {
        $sql = "SELECT * FROM admins WHERE email='$email' AND status='active'";
    }
    
    $result = mysqli_query($con, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        $res = mysqli_fetch_assoc($result);

        if ($password == $res['password']) {
            
            if ($user_type == "user") {
                $_SESSION['user_id'] = $res['user_id'];
                $_SESSION['full_name'] = $res['full_name'];
                $_SESSION['email'] = $res['email'];
                $_SESSION['user_type'] = 'user';
                
                header("Location: user/user.php");
            } else {
                $_SESSION['admin_id'] = $res['admin_id'];
                $_SESSION['admin_name'] = $res['name'];
                $_SESSION['email'] = $res['email'];
                $_SESSION['role'] = $res['role'];
                $_SESSION['shelter_id'] = $res['shelter_id'];
                $_SESSION['user_type'] = 'admin';
                
                header("Location: admin/dashboard.php");
            }
            exit;
        } else {
            $error_message = "Invalid Email or Password";
        }
    } else {
        $error_message = "Invalid Email or Password or Account is blocked";
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

                <p class="login-select">Login as:</p>

                <form method="POST" action="">
                    
                    <div class="login-buttons">
                        <button type="button" class="login-type-btn active" data-type="user">User</button>
                        <button type="button" class="login-type-btn" data-type="shelter">Shelter Admin</button>
                    </div>

                    <input type="hidden" name="user_type" id="user_type" value="user">
                    
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
                    <?php } ?>

                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot Password?</a>
                    </div>
                    
                    <button type="submit" class="login-submit">Login</button>
                </form>

                <div class="signup-link">
                    <a href="register.php">Don't have an account? Sign Up</a>
                </div>

            </div>

        </div>

    </div>

    <script>
        const buttons = document.querySelectorAll('.login-type-btn');
        const userTypeInput = document.getElementById('user_type');

        buttons.forEach(button => {
            button.addEventListener('click', function() {
                buttons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                userTypeInput.value = this.getAttribute('data-type');
            });
        });
    </script>
</body>
</html>