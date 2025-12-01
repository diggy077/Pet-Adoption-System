<!-- <nav>
    <a href="landing.php" class="logo-link">
        <img src="assets/images/logo1.png" alt="Logo" class="logo">
        <span class=Title>PetAdopt</span>
    </a>
    <div class="logo">PetAdopt</div>
    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="browse.php">Browse Pets</a></li>
        <li><a href="about.php">About Us</a></li>
        <li><a href="contact.php">Contact</a></li>
    </ul>
    <a href="login.php"><button class="login-btn">Login or Register</button></a>
</nav>

<link rel="stylesheet" href="assets/css/style.css"> -->
                $_SESSION['user_type'] = 'user'
<?php
session_start(); // ensure session is active
?>

<nav>
    <div class="logo">PetAdopt</div>

    <ul>
        <li><a href="index.php">Home</a></li>
        <li><a href="browse.php">Browse Pets</a></li>

        <?php
        if (isset($_SESSION['user_type'])) {

            // USER MENU
            if ($_SESSION['user_type'] = 'user') {
                echo '<li><a href="shelters.php">Shelters</a></li>';
            }
            // ADMIN MENU
            else if ($_SESSION['user_type'] = 'admin') {
                echo '<li><a href="dashboard.php">Dashboard</a></li>';
                echo '<li><a href="manage_pets.php">Manage Pets</a></li>';
            }
            // SUPER ADMIN (if you have one)
            else if ($_SESSION['role'] === 'super_admin') {
                echo '<li><a href="admin_panel.php">Admin Panel</a></li>';
                echo '<li><a href="manage_shelters.php">Manage Shelters</a></li>';
            }

        } else {
            // PUBLIC (not logged in)
            echo '<li><a href="about.php">About Us</a></li>';
            echo '<li><a href="contact.php">Contact</a></li>';
        }
        ?>
    </ul>

    <?php
    if (isset($_SESSION['role'])) {
        echo '<a href="logout.php"><button class="login-btn">Logout</button></a>';
    } else {
        echo '<a href="login.php"><button class="login-btn">Login or Register</button></a>';
    }
    ?>
</nav>

<link rel="stylesheet" href="assets/css/style.css">
