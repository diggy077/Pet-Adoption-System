<?php
session_start();
include("connection.php");
include("navbar.php");
include("includes/functions.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

$stmt = $con->prepare("SELECT full_name, email, phone_num, role, password FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user   = $result->fetch_assoc();
$stmt->close();

$success = "";
$error   = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $full_name        = sanitize_string($_POST['full_name']);
    $phone_num        = sanitize_phone($_POST['phone_num']);
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['password'];
    $confirm_password = $_POST['password1'];

    if (empty($full_name) || empty($phone_num) || empty($current_password)) {
        $error = "Full name, phone number and current password are required.";
    }

    if (empty($error)) {
        if (!preg_match("/^[a-zA-Z\s'\-]+$/", $full_name)) {
            $error = "Full name must contain letters only, no numbers or special characters.";
        }
    }

    if (empty($error)) {
        if (!preg_match('/^(98|97)\d{8}$/', $phone_num)) {
            $error = "Phone number must be a valid 10-digit Nepali number starting with 98 or 97.";
        }
    }

    if (empty($error)) {
        if (!verify_password($current_password, $user['password'])) {
            $error = "Current password is incorrect.";
        }
    }

    if (empty($error)) {
        if (!empty($new_password) || !empty($confirm_password)) {
            if (strlen($new_password) < 8) {
                $error = "New password must be at least 8 characters.";
            } else if ($new_password !== $confirm_password) {
                $error = "New passwords do not match.";
            }
        }
    }

    if (empty($error)) {
        if (!empty($new_password)) {
            $hashed_password = hash_password($new_password);
            $stmt = $con->prepare("UPDATE users SET full_name = ?, phone_num = ?, password = ? WHERE id = ?");
            $stmt->bind_param("sssi", $full_name, $phone_num, $hashed_password, $user_id);
        } else {
            $stmt = $con->prepare("UPDATE users SET full_name = ?, phone_num = ? WHERE id = ?");
            $stmt->bind_param("ssi", $full_name, $phone_num, $user_id);
        }

        if ($stmt->execute()) {
            $success = "Profile updated successfully!";
            $stmt->close();

            $stmt = $con->prepare("SELECT full_name, email, phone_num, role, password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user   = $result->fetch_assoc();
            $stmt->close();
        } else {
            $error = "Something went wrong. Please try again.";
        }
    }
}
?>

<html>

<head>
    <title>Edit Profile | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="edit-container">
        <h1>Edit Profile</h1>

        <?php
        if (!empty($success)) {
            echo '<p class="message success">' . e($success) . '</p>';
        } else if (!empty($error)) {
            echo '<p class="message error">' . e($error) . '</p>';
        }
        ?>

        <form method="POST">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" id="full_name" name="full_name" value="<?php echo e($user['full_name']); ?>">
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?php echo e($user['email']); ?>" readonly>
                <small style="color: #6c757d; font-size:1.3rem">Email cannot be changed</small>
            </div>

            <div class="form-group">
                <label for="phone_num">Phone Number</label>
                <input type="text" id="phone_num" name="phone_num" value="<?php echo e($user['phone_num']); ?>">
            </div>

            <div class="form-group full-width">
                <label for="current_password">Current Password <span style="color:red">*</span></label>
                <input type="password" id="current_password" name="current_password">
            </div>

            <div class="form-group full-width">
                <label for="password">New Password (optional)</label>
                <input type="password" id="password" name="password">
            </div>

            <div class="form-group full-width">
                <label for="password1">Confirm New Password</label>
                <input type="password" id="password1" name="password1">
                <small style="color: #6c757d; font-size: 1.3rem;">Enter a new password only if you want to change your current password</small>
            </div>

            <div class="btn-container">
                <a href="myprofile.php" class="cancel-btn">Cancel</a>
                <button type="submit">Save Changes</button>
            </div>
        </form>
    </div>
</body>
</html>