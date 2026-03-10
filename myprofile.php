<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

// Prepared statement to fetch user data
$stmt = $con->prepare("SELECT full_name, email, phone_num, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Map role to readable text
$role_map = [
    'superadmin' => 'Super Admin',
    'admin' => 'Admin',
    'user' => 'User'
];

$role_text = $role_map[$_SESSION['role']] ?? 'User';
?>
<html>
<head>
    <title>My Profile | PetAdopt</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main>
        <div class="profile-container">
            <section class="profile-header">
                <h1>MY PROFILE</h1>
            </section>
            <section class="profile-info-card">
                <div class="profile-photo">🐾</div>
                <div class="profile-details">
                    <div class="profile-name"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    <div class="profile-role"><?php echo $role_text; ?></div>
                </div>
            </section>
            <section class="personal-info-section">
                <div class="section-header">
                    <h2>PERSONAL INFORMATION</h2>
                    <a href="edit_profile.php" class="edit-btn">EDIT</a>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">NAME</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['full_name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">EMAIL</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">PHONE NUMBER</div>
                        <div class="info-value"><?php echo htmlspecialchars($user['phone_num']); ?></div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>