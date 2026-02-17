<?php
include('connection.php');

$user = null;

if (isset($_SESSION['id'])) {
    $user_id = $_SESSION['id'];
    $query = "SELECT full_name, email, phone_num, role FROM users WHERE id='$user_id'";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);
}

?>
<html>

<head>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <header>
        <div class="navigationbar">
            <a class="leftsection">
                <img src="assets/images/logo.png" alt="Logo" class="logo">
                <span class="Title">PetAdopt</span>
            </a>
            <div class="MidSection">
                <?php if ($user): ?>
                    <a href="user.php">Home</a>
                <?php else: ?>
                    <a href="index.php">Home</a>
                <?php endif; ?>
                <a href="browsePets.php">Browse Pets</a>

                <?php if ($user): ?>
                    <?php if ($user['role'] == 'admin'): ?>
                        <a href="userRequests.php">User Request</a>
                    <?php else: ?>
                        <a href="myRequests.php">My Requests</a>
                    <?php endif; ?>
                    <a href="myprofile.php">My Profile</a>
                    <?php if ($user['role'] == 'superadmin'): ?>
                        <a href="adminpanel.php">Admin Panel</a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="index.php#about">About Us</a>
                    <a href="index.php#footer">Contact</a>
                <?php endif; ?>
            </div>
            <div class="Login_Button">
                <?php if ($user): ?>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
</body>

</html>