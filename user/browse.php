<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../assets/css/browse.css">
    <link href='https://fonts.googleapis.com/css?family=Agdasima' rel='stylesheet'>
</head>
<body>
    <nav>
        <div class="logo">PetAdopt</div>
        <ul>
            <li><a href="user2.php">Home</a></li>
            <li><a href="browse.html" class="active">Browse Pets</a></li>
            <li><a href="about.php">About Us</a></li>
            <li><a href="contact.php">Contact</a></li>
        </ul>
        <button class="login-btn">Logout</button>
    </nav>

    <section class="page-title">
        <h1>Find Your Perfect Pet</h1>
        <p>Browse through our collection of adorable pets ready for adoption.</p>
    </section>

    <section class="search-filters">
        <select class="filter">
            <option value="">All Types</option>
            <option value="dog">Dog</option>
            <option value="cat">Cat</option>
            <option value="rabbit">Rabbit</option>
        </select>
        <select class="filter">
            <option value="">All Ages</option>
            <option value="puppy">Puppy/Kitten</option>
            <option value="adult">Adult</option>
            <option value="senior">Senior</option>
        </select>
    </section>

    <section class="pet-grid-section">
        <div class="pet-grid">
            <div class="pet-card">
                <img src="assets/images/dog.jpg" alt="Bella">
                <h3>Bella</h3>
                <p>Breed: Labrador</p>
                <p>Age: 2 years</p>
                <button>Adopt</button>
            </div>

            <div class="pet-card">
                <img src="assets/images/cat.jpg" alt="Kitty">
                <h3>Kitty</h3>
                <p>Breed: Persian</p>
                <p>Age: 1 year</p>
                <button>Adopt</button>
            </div>

            <div class="pet-card">
                <img src="assets/images/rabbit.jpg" alt="Max">
                <h3>Max</h3>
                <p>Breed: Bunny</p>
                <p>Age: 3 months</p>
                <button>Adopt</button>
            </div>

            <div class="pet-card">
                <img src="assets/images/dog2.jpg" alt="Rocky">
                <h3>Rocky</h3>
                <p>Breed: Beagle</p>
                <p>Age: 4 years</p>
                <button>Adopt</button>
            </div>
        </div>

        <div class="pagination">
            <button>&laquo; Prev</button>
            <button class="active">1</button>
            <button>2</button>
            <button>3</button>
            <button>Next &raquo;</button>
        </div>
    </section>

    <footer>
        <p>© 2025 PetAdopt. All Rights Reserved.</p>
    </footer>
</body>
</html>

</body>
</html>