<?php
session_start();
include("connection.php");

$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $full_name = mysqli_real_escape_string($con, $_POST["full_name"]);
    $email = mysqli_real_escape_string($con, $_POST["email"]);
    $phone = mysqli_real_escape_string($con, $_POST["phone"]);
    $password = mysqli_real_escape_string($con, $_POST["password"]);
    $Cpassword = mysqli_real_escape_string($con, $_POST["Cpassword"]);
    
    $select = "SELECT * FROM users WHERE email = '$email'";
        $select_user = mysqli_query($con, $select);

        if (mysqli_num_rows($select_user) > 0) {
            $error_message = "User already exists!";
        } 
        else {
            if($password==$Cpassword){

                $sql = "INSERT INTO users(full_name,email, password, phone_num)
                    VALUES ('$full_name', '$email', '$password', '$phone')";
            
            if (mysqli_query($con, $sql)) {
                header("Location: login.php");
                exit;
            } else {
                $error_message = "Error occurred while registering.";
            }
            }
            else{
                $error_message= "Passwords do not match!";
            }
        }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Pet Adoption System</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href='https://fonts.googleapis.com/css?family=Agdasima' rel='stylesheet'>
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

                <?php if (!empty($error_message)): ?>
                    <div class="error-message">
                        <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <p class="login-select">Sign Up</p>

                <form method="POST" action="">
                                        
                    <div class="input-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your contact number" required>
                    </div>
                    
                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="Cpassword" name="Cpassword" placeholder="Re-enter your password" required>
                    </div>
                                        
                    <button type="submit" class="login-submit">Register</button>
                </form>

                <div class="signup-link">
                    <a href="login.php">Already have an account? Log in</a>
                </div>

            </div>

        </div>

    </div>

</body>
</html>