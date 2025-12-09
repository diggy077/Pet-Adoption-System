<?php
session_start();
include("connection.php");
include("navbar.php");

// if (!isset($_SESSION['id'])) {
//     header("Location: login.php");
//     exit();
// }
$user=null;
if(isset($_SESSION['id'])){
    $user_id = $_SESSION['id'];
    $query = "SELECT full_name, role FROM users WHERE id=$user_id";
    $result = mysqli_query($con, $query);
    $user = mysqli_fetch_assoc($result);
}
    
    $category_filter = "";
    if (isset($_GET['category']) && !empty($_GET['category'])) {
        $category = mysqli_real_escape_string($con, $_GET['category']);
        $category_filter = "AND category = '$category'";
    }
    $pets_query = "SELECT * FROM pets WHERE status= 'available' $category_filter";
    $pets_result = mysqli_query($con, $pets_query);
?>

<html>

<head>
    <title>Browse Pets| PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css" >
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="browse-container">
        
        <div class="browse-header">
            <h1>🐾 Browse All Pets</h1>
            <p>Find your perfect companion from out list of adorable pets available for adoption.</p>
            
            <form method="GET" action="browsePets.php" style="margin-top: 1.0rem;">
                <label for="category" style="font-size:1.1rem; font-weight:bold; color:#2b4660;">Category:</label>
                <select name="category" id="category" onchange="this.form.submit()" style="padding: 0.5rem 1rem; border-radius:5px; border:1px solid #2b4660; margin-left:10px; font-size:1rem;">
                    <option value="">All</option>
                    <option value="Dog" <?php if (isset($_GET['category']) && $_GET['category'] == 'Dog') echo 'selected'; ?>>Dog</option>
                    <option value="Cat" <?php if (isset($_GET['category']) && $_GET['category'] == 'Cat') echo 'selected'; ?>>Cat</option>
                    <option value="Rabbit" <?php if (isset($_GET['category']) && $_GET['category'] == 'Rabbit') echo 'selected'; ?>>Rabbit</option>
                </select>
            </form>
            <?php if($user): ?>
                <?php if ($user['role'] == 'admin'): ?>
                    <div style="margin-top: 1rem; text-align:right;">
                        <a href="addPet.php" class="adopt-btn">
                            ➕ Add New Pet
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="pet-grid">
            <?php if ($pets_result && mysqli_num_rows($pets_result) > 0): ?>
                <?php while ($pet = mysqli_fetch_assoc($pets_result)): ?>
                    <div class="pet-card">
                        <img src="assets/images/<?php echo $pet['image'] ?? 'default_pet.jpg'; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
                        <h2><?php echo htmlspecialchars($pet['name']); ?></h2>
                        <p><?php echo htmlspecialchars($pet['breed']); ?></p>
                        <p><?php echo $pet['age']; ?> months old</p>
                        <a href="pet-details.php?id=<?php echo $pet['id']; ?>" class="adopt-btn"> View Details</a>
                        <?php if($user): ?>
                        <?php if ($user['role'] == 'admin'): ?>
                            <button class="adopt-btn" style="background-color:red; color:white; margin-top:5px;" onclick="openDeleteModal(<?php echo $pet['id']; ?>)">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        <?php endif; ?>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <h2 style="color:red; text-align:center;">No pets available at the moment 😿</h2>
            <?php endif; ?>
        </div>
    </div>

                    
    <div id="deleteModal">
        <div id="deleteModalContent">
            <p>Are you sure you want to delete this pet?</p>
            <a id="confirmDelete" href=""><button id="deleteBtn">Delete</button></a>
            <button id="cancelBtn" onclick="closeDeleteModal()">Cancel</button>
        </div>
    </div>
                        <script>
        function openDeleteModal(petId) {
            document.getElementById('deleteModal').style.display = 'block';
            document.getElementById('confirmDelete').href = 'deletePet.php?id=' + petId;
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        </script>
</body>

</html>

<?php include("footer.php"); ?>