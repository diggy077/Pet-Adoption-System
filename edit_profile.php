<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$query = "SELECT full_name, email, phone_num, role, password FROM users WHERE id='$user_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);
$success = $error = "";
$update_query = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = $_POST['full_name'];
    $phone_num = $_POST['phone_num'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];
    $password1 = $_POST['password1'];

    if (!empty($new_password) || !empty($password1)) {
        if (empty($current_password)) {
            $error = "Please enter your current password.";
        } elseif ($current_password !== $user['password']) {
            $error = "Password Incorrect!";
        } elseif ($new_password !== $password1) {
            $error = "New passwords do not match!";
        } else {
            $update_query = "UPDATE users SET full_name='$full_name', phone_num='$phone_num', password='$new_password' 
                             WHERE id='$user_id'";
        }
    }
}
if (empty($error) && $update_query) {
    if (mysqli_query($con, $update_query)) {
        $success = "Profile updated successfully!";
        $result = mysqli_query($con, $query);
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Something went wrong. Please try again.";
    }
}
?>

<html>

<head>
    <title>EditProfile | PetAdopt</title>
    <link rel="stylesheet" href="css/landing.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: auto;
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 100;
        }

        body {
            color: #2b4660;
            padding-top: 70px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family:Agdasima;
        }

        .edit-container {
            width: 800px;
            margin: 2rem auto;
            background-color: #f5eec8;
            border: 2px solid #2b4660;
            border-radius: 20px;
            padding: 2rem;
        }

        h1 {
            text-align: center;
            font-size: 2.2rem;
            margin-bottom: 1.5rem;
            color: #2b4660;
            position: relative;
            padding-bottom: 10px;
        }

        form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .full-width {
            grid-column: 1 / span 2;
        }

        label {
            font-weight: bold;
            font-size: 1.5rem;
            color: #2b4660;
            margin-bottom: 8px;
        }

        input {
            padding: 0.5rem 1rem;
            font-family: "Agdasima";
            font-size: 1.5rem;
            border-radius: 10px;
        }

        .btn-container {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 1.5rem;
            grid-column: 1 /span 2;
        }

        button,
        a.cancel-btn {
            text-align: center;
            border: none;
            border-radius: 10px;
            padding: 0.8rem 1.5rem;
            font-family: "Agdasima";
            font-size: 1.5rem;
            font-weight: bold;
            background-color: #ffffff;
            border: 2px solid #2b4660;
            color: #2b4660;
            text-decoration: none;
        }

        a.cancel-btn:hover {
            background-color: #2b4660;
            color: #fff;
        }

        button {
            background-color: #88BDF2;
            color: #000000;
        }

        button:hover {
            background-color: #5596D6;
            color: #fff;
        }

        .message {
            text-align: center;
            font-weight: bold;
            margin-bottom: 1rem;
            padding: 10px;
            border-radius: 8px;
            grid-column: 1 / span 2;
        }

        .success {
            color: green;
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            font-size: 1rem;
        }

        .error {
            color: red;
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            font-size: 1rem;
        }
    </style>
</head>

<body>
    <div class="edit-container">
        <h1>Edit Profile</h1>
        <?php if ($success): ?>
            <p class="message success"><?php echo $success; ?></p>
        <?php elseif ($error): ?>
            <p class="message error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                <small style="color: #6c757d; font-size:1.3rem">Email cannot be changed</small>
            </div>
            <div class="form-group">
                <label for="phone_num">Phone Number</label>
                <input type="text" id="phone_num" name="phone_num" value="<?php echo htmlspecialchars($user['phone_num']); ?>" required>
            </div>
            <div class="form-group full-width">
                <label for="password">Current Password</label>
                <input type="password" id="password" name="current_password">
            </div>
            <div class="form-group full-width">
                <label for="password">New Password (optional)</label>
                <input type="password" id="password" name="password">
            </div>
            <div class="form-group full-width">
                <label for="password">Confirm Password </label>
                <input type="password" id="password1" name="password1">
                <small style="color: #6c757d; font-size: 1.3rem;">Enter a new password only if you want to change current password</small>
            </div>
            <div class="btn-container">
                <a href="myprofile.php" class="cancel-btn">Cancel</a>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>