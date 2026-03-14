<?php
session_start();
include("connection.php");
include("navbar.php");
include("includes/functions.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: browsePets.php");
    exit();
}

$pet_id = sanitize_int($_GET['id']);

/* FETCH PET + SHELTER DETAILS */
$stmt = $con->prepare("
    SELECT pets.*, users.full_name, users.email, users.phone_num
    FROM pets
    LEFT JOIN users ON pets.admin_id = users.id
    WHERE pets.id = ?
");

$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
$pet    = $result->fetch_assoc();
$stmt->close();

if (!$pet) {
    header("Location: browsePets.php");
    exit();
}

$user_role = null;
$user_id   = null;
if (isset($_SESSION['id'])) {
    $user_id   = sanitize_int($_SESSION['id']);
    $user_role = sanitize_string($_SESSION['role']);
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><?php echo e($pet['name']); ?> | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
<<<<<<< HEAD
    <div class="pet-details-wrapper">
        <div class="pet-hero-section">
            <img src="assets/images/<?php echo e($pet['image'] ?? 'default_pet.jpg'); ?>" alt="<?php echo e($pet['name']); ?>">
            <div class="pet-hero-overlay">
                <h1><?php echo e($pet['name']); ?> 🐾</h1>
                <p class="pet-breed"><?php echo e($pet['breed']); ?></p>
            </div>
        </div>

        <div class="pet-quick-info">
            <div class="quick-info-card">
                <i class="fas fa-birthday-cake"></i>
                <div class="quick-info-content">
                    <span class="info-label">Age</span>
                    <span class="info-value"><?php echo e($pet['age']); ?> months</span>
                </div>
            </div>

            <div class="quick-info-card">
                <i class="fas fa-venus-mars"></i>
                <div class="quick-info-content">
                    <span class="info-label">Gender</span>
                    <span class="info-value"><?php echo e($pet['gender']); ?></span>
                </div>
            </div>

            <div class="quick-info-card">
                <i class="fas fa-paw"></i>
                <div class="quick-info-content">
                    <span class="info-label">Category</span>
                    <span class="info-value"><?php echo e($pet['category']); ?></span>
                </div>
            </div>
        </div>

        <div class="pet-details-content">
            <div class="details-main">
                <div class="about-section">
                    <h2><i class="fas fa-heart"></i> About <?php echo e($pet['name']); ?></h2>
                    <p><?php echo nl2br(e($pet['description'])); ?></p>
                </div>

                <div class="additional-info-section">
                    <h2><i class="fas fa-info-circle"></i> Additional Information</h2>
                    <div class="info-grid-details">
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-tag"></i> Breed</span>
                            <span class="detail-value"><?php echo e($pet['breed']); ?></span>
                        </div>
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-weight"></i> Weight</span>
                            <span class="detail-value"><?php echo e($pet['weight'] ?? 'N/A'); ?> kg</span>
                        </div>
                        <div class="info-detail-item">
                            <span class="detail-label"><i class="fas fa-check-circle"></i> Status</span>
                            <span class="detail-value status-<?php echo strtolower(e($pet['status'])); ?>">
                                <?php echo ucfirst(e($pet['status'])); ?>
                            </span>
                        </div>
                    </div>

                </div>
            </div>

<<<<<<< HEAD
            <div class="pet-actions">

                <?php
                // Show adopt button only for regular logged-in users
                if ($user_role === 'user') {
                    if ($pet['status'] == 'available') {
                        echo '<a href="adoptionRequest.php?pet_id=' . sanitize_int($pet['id']) . '" class="btn-adopt-pet">
                                <i class="fas fa-heart"></i> Adopt ' . e($pet['name']) . '
                              </a>';
                    } else {
                        echo '<button class="btn-adopt-pet btn-disabled" disabled>
                                <i class="fas fa-ban"></i> Not Available
                              </button>';
                    }
                }

                // Show login prompt only for guests (not logged in)
                if ($user_role === null) {
                    echo '<a href="login.php" class="btn-adopt-pet">
                            <i class="fas fa-heart"></i> Login to Adopt ' . e($pet['name']) . '
                          </a>';
                }
                ?>

                <a href="browsePets.php" class="btn-back-browse">
                    <i class="fas fa-arrow-left"></i> Back to Browse
                </a>

                <?php
                if ($user_role === 'admin') {
                    echo '<a href="editPet.php?id=' . sanitize_int($pet['id']) . '" class="btn-edit-pet">
                            <i class="fas fa-edit"></i> Edit Pet
                          </a>
                          <button class="btn-delete-pet" onclick="openDeleteModal(' . sanitize_int($pet['id']) . ')">
                            <i class="fas fa-trash"></i> Delete Pet
                          </button>';
                }
                ?>
=======

            <!-- SHELTER CONTACT -->
            <div class="additional-info-section">
                <h2><i class="fas fa-home"></i> Shelter Contact</h2>

                <div class="info-grid-details">

                    <!-- <div class="info-detail-item">
                        <span class="detail-label">
                            <i class="fas fa-user"></i> Shelter Name
                        </span>
                        <span class="detail-value">
                            <?php echo htmlspecialchars($pet['full_name'] ?? 'Unknown'); ?>
                        </span>
                    </div> -->

                    <div class="info-detail-item">
                        <span class="detail-label">
                            <i class="fas fa-envelope"></i> Email
                        </span>
                        <span class="detail-value">
                            <?php echo htmlspecialchars($pet['email'] ?? 'N/A'); ?>
                        </span>
                    </div>

                    <div class="info-detail-item">
                        <span class="detail-label">
                            <i class="fas fa-phone"></i> Contact Number
                        </span>
                        <span class="detail-value">
                            <?php echo htmlspecialchars($pet['phone_num'] ?? 'N/A'); ?>
                        </span>
                    </div>

                </div>
            </div>
>>>>>>> 1e7ff2256c317eb1e18f01eb1ab0dab82d699111

            </div>
        </div>

    <?php
    if ($user_role === 'admin') {
        echo '
    <div id="deleteModal">
        <div id="deleteModalContent">
            <p>Are you sure you want to delete ' . e($pet['name']) . '?</p>
            <a id="confirmDelete" href=""><button id="deleteBtn">Delete</button></a>
            <button id="cancelBtn" onclick="closeDeleteModal()">Cancel</button>
        </div>

    </div>

<<<<<<< HEAD
    <script>
        function openDeleteModal(petId) {
            document.getElementById("deleteModal").style.display = "block";
            document.getElementById("confirmDelete").href = "deletePet.php?id=" + petId;
        }

        function closeDeleteModal() {
            document.getElementById("deleteModal").style.display = "none";
        }
    </script>';
    }
    ?>
=======
</div>


<?php 
if (isset($user_role) && $user_role == 'admin') {
?>

<div id="deleteModal">

    <div id="deleteModalContent">
        <p>Are you sure you want to delete <?php echo htmlspecialchars($pet['name']); ?>?</p>

        <a id="confirmDelete" href="">
            <button id="deleteBtn">Delete</button>
        </a>

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

<?php 
}
?>


>>>>>>> 1e7ff2256c317eb1e18f01eb1ab0dab82d699111
</body>

<?php include("footer.php"); ?>

</html>