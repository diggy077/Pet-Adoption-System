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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&display=swap');

        body {
            display: flex;
            flex-direction: column;
            padding-top: 70px;
            box-sizing: border-box;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .navigationbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            font-family: 'Agdasima', sans-serif;
            border: 1px solid #BDDDFC;
            background-color: #BDDDFC;
            height: 70px;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .leftsection {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo {
            height: 60px;
            width: 50px;
        }

        .Title {
            font-size: 25px;
            color: black;
            font-weight: 700;
        }

        .MidSection {
            display: flex;
            gap: 25px;
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
        }

        .MidSection a {
            color: black;
            font-size: 20px;
            font-weight: 700;
            text-decoration: none;
        }

        .MidSection :hover {
            color: white;
        }

        .Login_Button {
            display: flex;
            align-items: center;
        }

        .Login_Button a {
            color: black;
            font-size: 20px;
            font-weight: 700;
            padding: 5px 20px;
            border: 1px solid #88BDF2;
            background-color: #88BDF2;
            border-radius: 10px;
            text-decoration: none;
        }

        .Login_Button a:hover {
            background-color: #5596D6;
            color: #ffffff;
        }
    </style>
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