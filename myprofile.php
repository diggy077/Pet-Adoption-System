<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Fetch user data using prepared statement
$stmt = $con->prepare("SELECT full_name, email, phone_num, role, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$success = $error = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name']);
    $phone_num = trim($_POST['phone_num']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['password'];
    $password1 = $_POST['password1'];

    // SERVER-SIDE PHONE VALIDATION
    if (!preg_match("/^(98|97)[0-9]{8}$/", $phone_num)) {
        $error = "Phone number must start with 98 or 97 and be exactly 10 digits long.";
    } else {
        // Optional password update
        if (!empty($new_password) || !empty($password1)) {
            if (empty($current_password)) {
                $error = "Please enter your current password to change it.";
            } elseif ($current_password !== $user['password']) {
                $error = "Current password is incorrect!";
            } elseif ($new_password !== $password1) {
                $error = "New passwords do not match!";
            } else {
                // Update name, phone, and password
                $stmt = $con->prepare("UPDATE users SET full_name = ?, phone_num = ?, password = ? WHERE id = ?");
                $stmt->bind_param("sssi", $full_name, $phone_num, $new_password, $user_id);
                if ($stmt->execute()) {
                    $success = "Profile updated successfully!";
                } else {
                    $error = "Something went wrong. Please try again.";
                }
                $stmt->close();
            }
        } else {
            // Update only name and phone
            $stmt = $con->prepare("UPDATE users SET full_name = ?, phone_num = ? WHERE id = ?");
            $stmt->bind_param("ssi", $full_name, $phone_num, $user_id);
            if ($stmt->execute()) {
                $success = "Profile updated successfully!";
            } else {
                $error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    }

    // Refresh user data
    $stmt = $con->prepare("SELECT full_name, email, phone_num, role, password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}
?>

<html>
<head>
    <title>Edit Profile | PetAdopt</title>
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
            <small>Email cannot be changed</small>
        </div>

        <div class="form-group">
            <label for="phone_num">Phone Number</label>
            <input type="text" id="phone_num" name="phone_num" value="<?php echo htmlspecialchars($user['phone_num']); ?>" required
                   pattern="^(98|97)[0-9]{8}$"
                   title="Phone number must start with 98 or 97 and be 10 digits long">
        </div>

        <div class="form-group full-width">
            <label for="current_password">Current Password</label>
            <input type="password" id="current_password" name="current_password">
        </div>

        <div class="form-group full-width">
            <label for="password">New Password (optional)</label>
            <input type="password" id="password" name="password">
        </div>

        <div class="form-group full-width">
            <label for="password1">Confirm New Password</label>
            <input type="password" id="password1" name="password1">
            <small>Enter a new password only if you want to change current password</small>
        </div>

        <div class="btn-container">
            <a href="myprofile.php" class="cancel-btn">Cancel</a>
            <button type="submit">Save Changes</button>
        </div>
    </form>
</div>
</body>
</html>