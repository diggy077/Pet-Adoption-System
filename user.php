<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['full_name'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];

$stmt = $con->prepare("SELECT full_name, email, phone_num, role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$stmt = $con->prepare("SELECT * FROM pets WHERE status = ? LIMIT 3");
$status = "available";
$stmt->bind_param("s", $status);
$stmt->execute();
$pets_result = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html>
    <head>
    <title>User Page | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    </head>
    <body>
        <div class="dashboard">
            <section class="welcome">
                <h1>Welcome, <?php echo$user['full_name'] ?>! 🐾</h1>
                <p>We're excited to help you find your perfect companion.Start  your adoption journey today!</p>
            </section>

            <section class="pets">
                <h2>Some Of Our Pets</h2>
                <div class="pet-grid">
                    <?php if($pets_result && mysqli_num_rows($pets_result)>0): ?>
                        <?php while($pet=mysqli_fetch_assoc($pets_result)): ?> 
                            <div class="pet-card">
                                <img src="assets/Images/<?php echo $pet['image'] ?? 'default_pet.jpg'; ?>" alt="<?php echo $pet['name']?>">
                                <h2><?php echo htmlspecialchars($pet['name']); ?></h2>
                                <p><?php echo htmlspecialchars($pet['breed']); ?></p>
                                <p> <?php echo $pet['age']; ?> months old</p><br>
                                <!-- <p><?php echo htmlspecialchars($pet['description']); ?></p> -->
                                <a href="pet-details.php?id=<?php echo $pet['id']; ?>" class="adopt-btn">View Details</a>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <h1 style="color:red">No pets available at the moment 😿</h1>
                    <?php endif; ?>
                </div>
            </section>
        </div>
    </body>
</html>
<?php include('footer.php') ?>