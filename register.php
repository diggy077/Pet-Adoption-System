<?php
session_start();
include("connection.php");
$error_message = "";

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $full_name = $_POST["full_name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $password = $_POST["password"];
    $Cpassword = $_POST["Cpassword"];

    if ($password == $Cpassword) {
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $error_message = "User already exists!";
        } else {

            $stmt = $con->prepare("INSERT INTO users (full_name, email, password, phone_num) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $password, $phone);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit;
            } else {
                $error_message = "Error occurred while registering.";
            }
        }
        $stmt->close();
    } else {
        $error_message = "Passwords do not match!";
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
                        <input type="text" id="full_name" name="full_name"
                            placeholder="Enter your full name"
                            pattern="[A-Za-z\s]{3,50}"
                            title="Name should contain only letters and spaces"
                            required>
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email"
                            placeholder="Enter your email"
                            pattern="/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/"
                            title="Enter a valid email address"
                            required>
                    </div>

                    <div class="input-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" id="phone" name="phone"
                            placeholder="Enter your contact number"
                            pattern="^(98|97)[0-9]{8}$"
                            title="Phone number must be 10 digits and start with 98 or 97"
                            required>
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <small id="strengthMessage"></small>
                    </div>

                    <div class="input-group">
                        <label for="Cpassword">Confirm Password</label>
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
<script>
const password = document.getElementById("password");
const message = document.getElementById("strengthMessage");

password.addEventListener("input", function () {

    let value = password.value;

    let mediumRegex = /^(?=.*[A-Za-z])(?=.*\d).{6,}$/;
    let strongRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{8,}$/;

    if (value.length === 0) {
        message.textContent = "";
    }
    else if (strongRegex.test(value)) {
        message.textContent = "Strong Password";
        message.style.color = "green";
    }
    else if (mediumRegex.test(value)) {
        message.textContent = "Medium Password";
        message.style.color = "orange";
    }
    else {
        message.textContent = "Weak Password";
        message.style.color = "red";
    }

});
</script>

</html>