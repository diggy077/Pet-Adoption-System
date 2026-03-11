<?php
session_start();
include("connection.php");
include("includes/functions.php");

if ($_SERVER['REQUEST_METHOD'] === "POST") {

    $full_name = sanitize_string($_POST["full_name"]);
    $email     = $_POST["email"];
    $phone     = $_POST["phone"];
    $password  = $_POST["password"];
    $Cpassword = $_POST["Cpassword"];

    // Store form data in session so fields repopulate on error
    $_SESSION['form_data'] = [
        'full_name' => $full_name,
        'email'     => $email,
        'phone'     => $phone,
    ];

    if (empty($full_name) || empty($email) || empty($phone) || empty($password) || empty($Cpassword)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: register.php");
        exit;
    }

    if (!preg_match('/^(98|97)\d{8}$/', $phone)) {
        $_SESSION['error_message'] = "Phone number must be a valid 10-digit starting with 98 or 97.";
        header("Location: register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error_message'] = "Enter a valid email address.";
        header("Location: register.php");
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['error_message'] = "Password must be at least 8 characters.";
        header("Location: register.php");
        exit;
    }

    if ($password !== $Cpassword) {
        $_SESSION['error_message'] = "Passwords do not match.";
        header("Location: register.php");
        exit;
    }

    $email           = sanitize_email($email);
    $phone           = sanitize_phone($phone);
    $hashed_password = hash_password($password);

    $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['error_message'] = "User already exists!";
        header("Location: register.php");
        exit;
    }

    $stmt = $con->prepare("INSERT INTO users (full_name, email, password, phone_num) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $phone);

    if ($stmt->execute()) {
        $stmt->close();
        unset($_SESSION['form_data']);
        header("Location: login.php");
        exit;
    } else {
        $_SESSION['error_message'] = "Error occurred while registering.";
        $stmt->close();
        header("Location: register.php");
        exit;
    }
}

$error_message = "";
if (!empty($_SESSION['error_message'])) {
    $error_message = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

$form_data = [];
if (!empty($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
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

                <?php
                if (!empty($error_message)) {
                    echo '<div class="error-message">' . e($error_message) . '</div>';
                }
                ?>

                <p class="login-select">Sign Up</p>

                <form method="POST" action="register.php">

                    <div class="input-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name"
                            value="<?php echo !empty($form_data['full_name']) ? e($form_data['full_name']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email"
                            value="<?php echo !empty($form_data['email']) ? e($form_data['email']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <label for="phone">Contact Number</label>
                        <input type="tel" id="phone" name="phone" placeholder="Enter your contact number"
                            value="<?php echo !empty($form_data['phone']) ? e($form_data['phone']) : ''; ?>">
                    </div>

                    <div class="input-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password">
                        <small id="strengthMessage"></small>
                    </div>

                    <div class="input-group">
                        <label for="Cpassword">Confirm Password</label>
                        <input type="password" id="Cpassword" name="Cpassword" placeholder="Re-enter your password">
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