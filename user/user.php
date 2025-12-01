<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | PetAdopt</title>
    <link rel="stylesheet" href="../assets/css/user.css">
    <link href='https://fonts.googleapis.com/css?family=Agdasima' rel='stylesheet'>
</head>
<body>
    <nav>
        <div class="logo">PetAdopt</div>
        <ul>
            <li><a href="user2.php">Home</a></li>
            <li><a href="browse.php">Browse Pets</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <button class="login-btn">Logout</button>
    </nav>

    <section class="welcome">
        <h1>Welcome, <span>User!</span></h1>
        <p>Here’s a quick overview of your activity and favorite pets.</p>
    </section>

    <section class="dashboard-panels">
        <div class="panel">
            <h3>🐶 My Adoption Requests</h3>
            <p>Track the status of all your submitted adoption requests.</p>
            <button>View Requests</button>
        </div>

        <div class="panel">
            <h3>❤️ Favorite Pets</h3>
            <p>Quick access to pets you’ve added to your favorites list.</p>
            <button>View Favorites</button>
        </div>

        <div class="panel">
            <h3>🔍 Browse Pets</h3>
            <p>Discover new pets available for adoption in your area.</p>
            <button onclick="window.location.href='browse.php'">Browse Pets</button>
        </div>

        <div class="panel">
            <h3>👤 My Profile</h3>
            <p>Update your profile, contact info, and preferences.</p>
            <button>Manage Profile</button>
        </div>
    </section>

    <section class="recent-pets">
        <h2>Recently Added Pets</h2>
        <div class="pet-grid">
            <div class="pet-card">
                <img src="assets/images/dog.jpg" alt="Dog">
                <h3>Bella</h3>
                <p>Age: 2 years</p>
                <button>View Details</button>
            </div>
            <div class="pet-card">
                <img src="assets/images/dog.jpg" alt="Cat">
                <h3>Kitty</h3>
                <p>Age: 1 year</p>
                <button>View Details</button>
            </div>
            <div class="pet-card">
                <img src="assets/images/dog.jpg" alt="Rabbit">
                <h3>Max</h3>
                <p>Age: 3 months</p>
                <button>View Details</button>
            </div>
        </div>
    </section>

    <footer>
        <p>© 2025 PetAdopt. All Rights Reserved.</p>
    </footer>
</body>
</html>
