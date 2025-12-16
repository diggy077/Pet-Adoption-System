<?php

session_start();

include("connection.php");
include("navbar.php");
    
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: browsePets.php");
        exit();
    }
    
    $pet_id = mysqli_real_escape_string($con, $_GET['id']);
    if(isset($_SESSION['id'])){

        $user_id = $_SESSION['id'];
        $user_role = $_SESSION['role'];
    }

    
    $query = "SELECT * FROM pets WHERE id = '$pet_id'";
    
    $result = mysqli_query($con, $query);
    
    
    if (!$result || mysqli_num_rows($result) == 0) {
        header("Location: browsePets.php");

}
$pet = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo htmlspecialchars($pet['name']); ?> | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

</head>

<body>
    <div class="pet-details-wrapper">
        <div class="pet-hero-section">
            <img src="assets/images/<?php echo $pet['image'] ?? 'default_pet.jpg'; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
            <div class="pet-hero-overlay">
                <h1><?php echo htmlspecialchars($pet['name']); ?> 🐾</h1>
                <p class="pet-breed"><?php echo htmlspecialchars($pet['breed']); ?></p>
            </div>
        </div>

        <div class="pet-quick-info">
            <div class="quick-info-card">
                <i class="fas fa-birthday-cake"></i>
                <div class="quick-info-content">
                    <span class="info-label">Age</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['age']); ?> months</span>
                </div>
            </div>

            <div class="quick-info-card">
                <i class="fas fa-venus-mars"></i>
                <div class="quick-info-content">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['gender']); ?></span>
                </div>
            </div>

            <div class="quick-info-card">
                <i class="fas fa-paw"></i>
                <div class="quick-info-content">
                    <span class="info-label">Category</span>
                    <span class="info-value"><?php echo htmlspecialchars($pet['category']); ?></span>
                </div>
            </div>
        </div>

        <div class="pet-details-content">
            <div class="details-main">
                <div class="about-section">
                    <h2><i class="fas fa-heart"></i> About <?php echo htmlspecialchars($pet['name']); ?></h2>
                    <p><?php echo nl2br(htmlspecialchars($pet['description'])); ?></p>
                </div>

                <div class="additional-info-section">
                    <h2><i class="fas fa-info-circle"></i> Additional Information</h2>
                    <div class="info-grid-details">
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-tag"></i> Breed</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pet['breed']); ?></span>
                        </div>
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-ruler-vertical"></i> Size</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pet['size'] ?? 'Medium'); ?></span>
                        </div>
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-weight"></i> Weight</span>
                            <span class="detail-value"><?php echo htmlspecialchars($pet['weight'] ?? 'N/A'); ?></span>
                        </div>
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="detail-value status-<?php echo strtolower($pet['status']); ?>">
                                <?php echo ucfirst(htmlspecialchars($pet['status'])); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="pet-actions">
                <?php if ($pet['status'] == 'available'): ?>
                    <a href="adoptionRequest.php?pet_id=<?php echo $pet['id']; ?>" class="btn-adopt-pet">
                        <i class="fas fa-heart"></i> Adopt <?php echo htmlspecialchars($pet['name']); ?>
                    </a>
                <?php else: ?>
                    <button class="btn-adopt-pet btn-disabled" disabled>
                        <i class="fas fa-ban"></i> Not Available
                    </button>
                <?php endif; ?>
                
                <a href="browsePets.php" class="btn-back-browse">
                    <i class="fas fa-arrow-left"></i> Back to Browse
                </a>

                <?php if (isset($user_role) && $user_role == 'admin'): ?>
                    <a href="editPet.php?id=<?php echo $pet['id']; ?>" class="btn-edit-pet">
                        <i class="fas fa-edit"></i> Edit Pet
                    </a>
                    <button class="btn-delete-pet" onclick="openDeleteModal(<?php echo $pet['id']; ?>)">
                        <i class="fas fa-trash"></i> Delete Pet
                    </button>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php if (isset($user_role) && $user_role == 'admin'): ?>
    <div id="deleteModal">
        <div id="deleteModalContent">
            <p>Are you sure you want to delete <?php echo htmlspecialchars($pet['name']); ?>?</p>
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
    <?php endif; ?>
</body>

<?php include("footer.php"); ?>
</html>


