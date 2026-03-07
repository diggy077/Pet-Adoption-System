<?php
session_start();
include("connection.php");
include("navbar.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: browsePets.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: browsePets.php");
    exit();
}

$pet_id = intval($_GET['id']);

// Fetch pet details using prepared statement
$stmt = $con->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
$pet = $result->fetch_assoc();
$stmt->close();

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $pet_id = intval($_POST['pet_id']);
    $name = $_POST['name'];
    $age = intval($_POST['age']);
    $gender = $_POST['gender'];
    $breed = $_POST['breed'];
    $price = floatval($_POST['price']);
    $weight = $_POST['weight'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    // Fetch old image using prepared statement
    $stmt = $con->prepare("SELECT image FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $oldData = $result->fetch_assoc();
    $oldImage = $oldData['image'];
    $stmt->close();

    $imageName = $oldImage;

    if (!empty($_FILES['image']['name'])) {
        $imageName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/" . $imageName);
    }

    // Update pet details using prepared statement
    $stmt = $con->prepare("UPDATE pets SET 
                                name = ?, 
                                age = ?, 
                                gender = ?, 
                                breed = ?, 
                                price = ?, 
                                weight = ?, 
                                category = ?, 
                                description = ?, 
                                image = ? 
                            WHERE id = ?");
    $stmt->bind_param("sissdssssi", $name, $age, $gender, $breed, $price, $weight, $category, $description, $imageName, $pet_id);

    if ($stmt->execute()) {
        $message = "<div class='message success_message'>Pet Detail Updated Successfully!</div>";
    } else {
        $message = "<div class='message error_message'>Error Updating The Pet Detail</div>";
    }
    $stmt->close();

    // Refresh pet data
    $stmt = $con->prepare("SELECT * FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $pet = $result->fetch_assoc();
    $stmt->close();
}
?>
<html>

<head>
    <title>Edit Pets| PetAdopt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Edit Pet Detail</h2>
        <?php if (!empty($message)) { ?>
            <?= $message ?>
        <?php } ?>

        <form action="editPet.php?id=<?php echo $pet['id']; ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pet_id" value="<?php echo $pet['id']; ?>">

            <div class="addPetinput">
                <label for="name">Pet Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($pet['name']); ?>" required>
            </div>

            <div class="addPetinput">
                <label for="age">Age (months)</label>
                <input type="number" name="age" value="<?php echo $pet['age']; ?>" required>
            </div>

            <div class="addPetinput">
                <label for="gender">Gender</label>
                <select name="gender">
                    <option value="Male" <?php if ($pet['gender'] == 'Male') echo 'selected'; ?>>Male</option>
                    <option value="Female" <?php if ($pet['gender'] == 'Female') echo 'selected'; ?>>Female</option>
                </select>
            </div>

            <div class="addPetinput">
                <label for="breed">Breed</label>
                <input type="text" name="breed" value="<?php echo htmlspecialchars($pet['breed']); ?>">
            </div>

            <div class="addPetinput">
                <label for="price">Price</label>
                <input type="number" name="price" value="<?php echo htmlspecialchars($pet['price']); ?>" required>
            </div>

            <div class="addPetinput">
                <label for="weight">Weight (in kg)</label>
                <input type="text" name="weight" value="<?php echo htmlspecialchars($pet['weight']); ?>" required>
            </div>

            <div class="addPetinput">
                <label for="category">Category</label>
                <select name="category" required>
                    <option <?php if ($pet['category'] == 'Dog') echo 'selected'; ?>>Dog</option>
                    <option <?php if ($pet['category'] == 'Cat') echo 'selected'; ?>>Cat</option>
                    <option <?php if ($pet['category'] == 'Rabbit') echo 'selected'; ?>>Rabbit</option>
                    <select>
            </div>

            <div class="addPetinput">
                <label for="description">Description</label>
                <textarea name="description" rows="5"><?php echo htmlspecialchars($pet['description']); ?></textarea>
            </div>

            <div class="editPetinput">
                <label>Current Image:</label><br>
                <img src="assets/images/<?php echo $pet['image']; ?>" class="current-image">
            </div>

            <div class="editPetinput">
                <label>Upload New Image (optional):</label>
                <input type="file" name="image">
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Save Changes
            </button>

        </form>

        <a href="pet-details.php?id=<?php echo $pet['id']; ?>" class="back-link"> ← Back to Details</a>
    </div>
</body>

</html>