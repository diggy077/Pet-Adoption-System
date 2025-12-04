<?php
include('connection.php');

$user_id = $_SESSION['id'];
$query = "SELECT full_name, email, phone_num, role FROM users WHERE id='$user_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

if ($user['role'] == 'superadmin') {
    $role_text = "Superadmin";
} elseif ($user['role'] == 'admin') {
    $role_text = "Admin";
} else {
    $role_text = "User";
}

?>
<html>
<head>
    <title>Browse Pets | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css" >
</head>

<body>
    <header>
        <div class="navigationbar">
            <a class="leftsection">
                <img src="assets/images/paw.png" alt="Logo" class="logo">
                <span class="Title">PetAdopt</span>
            </a>
            <div class="MidSection">
                <a href="user.php">Home</a>
                <a href="browsePets.php">Browse Pets</a>
                <a href="myRequests.php">My Requests</a>
                <a href="myprofile.php">My Profile</a>
                <?php if ($user['role'] == 'superadmin'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif; ?>
            </div>
            <div class="Login_Button">
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
</body>

</html>