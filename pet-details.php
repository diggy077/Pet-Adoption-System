<?php
include("connection.php");

// if(!isset($_GET['id'])){
//     header("Location: user.php");
//     exit();
// }
$user = null;
$pet_id = intval($_GET['id']);
$query = "SELECT * FROM pets WHERE id = $pet_id";
$result = mysqli_query($con, $query);
$pet = mysqli_fetch_assoc($result);

if (!$pet) {
    echo "<h2>Pet not Found!</h2>";
    exit();
}
?>
<html>

<head>
    <title><?php echo htmlspecialchars($pet['name']); ?> | Pet Details</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="detail-container">
        <div class="image-section">
            <img src="assets/images/<?php echo $pet['image'] ?? 'default_pet.jpg'; ?>" alt="<?php echo htmlspecialchars($pet['name']); ?>">
        </div>
        <div class="details-section">
            <h1><?php echo htmlspecialchars($pet['name']); ?></h1>
            <div class="info"><strong>Age:</strong><?php echo htmlspecialchars($pet['age']); ?> months</div>
            <div class="info"><strong>Gender:</strong><?php echo htmlspecialchars($pet['gender']); ?></div>
            <div class="info"><strong>Breed:</strong><?php echo htmlspecialchars($pet['breed']); ?></div>
            <div class="info"><strong>Color:</strong><?php echo htmlspecialchars($pet['color'] ?? 'Unknown'); ?></div>
            <div class="price">Rs. <?php echo htmlspecialchars($pet['price']); ?></div>
            <?php if (!$user): ?>
                <a href="login.php?id=<?php echo $pet['id'] ?>" class="adpt-btn">Adopt Now</a>
            <?php else: ?>
                <a href="adoption.php?id=<?php echo $pet['id'] ?>" class="adpt-btn">Adopt Now</a>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>