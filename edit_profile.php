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
    <link rel="stylesheet" href="css/style.css">
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