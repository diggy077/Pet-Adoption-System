<?php
session_start();
include("connection.php");
include("navbar.php");
include("includes/functions.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: browsePets.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: browsePets.php");
    exit();
}

$pet_id = sanitize_int($_GET['id']);

$stmt = $con->prepare("SELECT * FROM pets WHERE id = ?");
$stmt->bind_param("i", $pet_id);
$stmt->execute();
$result = $stmt->get_result();
$pet    = $result->fetch_assoc();
$stmt->close();

if (!$pet) {
    header("Location: browsePets.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $pet_id      = sanitize_int($_POST['pet_id']);
    $name        = sanitize_string($_POST['name']);
    $age         = sanitize_int($_POST['age']);
    $gender      = sanitize_string($_POST['gender']);
    $breed       = sanitize_string($_POST['breed']);
    $price       = sanitize_price($_POST['price']);
    $weight      = sanitize_string($_POST['weight']);
    $category    = sanitize_string($_POST['category']);
    $description = sanitize_text($_POST['description']);

    // Block 1 - all fields required
    if (empty($name) || empty($age) || empty($gender) || empty($breed) || empty($price) || empty($weight) || empty($category) || empty($description)) {
        $message = "<div class='message error_message'>All fields are required.</div>";
    }

    // Block 2 - age must be positive
    if (empty($message)) {
        if ($age <= 0) {
            $message = "<div class='message error_message'>Age must be a positive number.</div>";
        }
    }

    // Block 3 - price cannot be negative or zero
    if (empty($message)) {
        if ($price < 0) {
            $message = "<div class='message error_message'>Price cannot be negative.</div>";
        } else if ($price == 0) {
            $message = "<div class='message error_message'>Price must be a positive number.</div>";
        }
    }

    // Block 4 - handle image (optional on edit)
    if (empty($message)) {
        $stmt = $con->prepare("SELECT image FROM pets WHERE id = ?");
        $stmt->bind_param("i", $pet_id);
        $stmt->execute();
        $result  = $stmt->get_result();
        $oldData = $result->fetch_assoc();
        $imageName = $oldData['image'];
        $stmt->close();

        if (!empty($_FILES['image']['name'])) {
            if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                $message = "<div class='message error_message'>Image upload failed.</div>";
            } else {
                $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                $newImageName = sanitize_image_filename($_FILES['image']['name']);
                $ext          = strtolower(pathinfo($newImageName, PATHINFO_EXTENSION));

                if (!in_array($ext, $allowed_ext)) {
                    $message = "<div class='message error_message'>Image must be a jpg, jpeg, png, gif, or webp file.</div>";
                } else if (file_exists("assets/images/" . $newImageName)) {
                    $message = "<div class='message error_message'>An image with that filename already exists. Please rename your image and try again.</div>";
                } else {
                    if (move_uploaded_file($_FILES['image']['tmp_name'], "assets/images/" . $newImageName)) {
                        $imageName = $newImageName;
                    } else {
                        $message = "<div class='message error_message'>Error uploading image.</div>";
                    }
                }
            }
        }
    }

    // Block 5 - update DB
    if (empty($message)) {
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
            $message = "<div class='message error_message'>Error Updating The Pet Detail.</div>";
        }
        $stmt->close();

        // Refresh pet data
        $stmt = $con->prepare("SELECT * FROM pets WHERE id = ?");
        $stmt->bind_param("i", $pet_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $pet    = $result->fetch_assoc();
        $stmt->close();
    }
}
?>
<html>

<head>
    <title>Edit Pet | PetAdopt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div class="container">
        <h2><i class="fas fa-edit"></i> Edit Pet Detail</h2>

        <?php
        if (!empty($message)) {
            echo $message;
        }
        ?>

        <form action="editPet.php?id=<?php echo sanitize_int($pet['id']); ?>" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="pet_id" value="<?php echo sanitize_int($pet['id']); ?>">

            <div class="addPetinput">
                <label for="name">Pet Name</label>
                <input type="text" name="name" value="<?php echo e($pet['name']); ?>">
            </div>

            <div class="addPetinput">
                <label for="age">Age (months)</label>
                <input type="number" name="age" value="<?php echo e($pet['age']); ?>">
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
                <input type="text" name="breed" value="<?php echo e($pet['breed']); ?>">
            </div>

            <div class="addPetinput">
                <label for="price">Price</label>
                <input type="number" name="price" value="<?php echo e($pet['price']); ?>">
            </div>

            <div class="addPetinput">
                <label for="weight">Weight (in kg)</label>
                <input type="text" name="weight" value="<?php echo e($pet['weight']); ?>">
            </div>

            <div class="addPetinput">
                <label for="category">Category</label>
                <select name="category">
                    <option value="dog" <?php if ($pet['category'] == 'dog') echo 'selected'; ?>>Dog</option>
                    <option value="cat" <?php if ($pet['category'] == 'cat') echo 'selected'; ?>>Cat</option>
                    <option value="rabbit" <?php if ($pet['category'] == 'rabbit') echo 'selected'; ?>>Rabbit</option>
                </select>
            </div>

            <div class="addPetinput">
                <label for="description">Description</label>
                <textarea name="description" rows="5"><?php echo e($pet['description']); ?></textarea>
            </div>

            <div class="editPetinput">
                <label>Current Image:</label><br>
                <img src="assets/images/<?php echo e($pet['image']); ?>" class="current-image">
            </div>

            <div class="editPetinput">
                <label>Upload New Image (optional):</label>
                <input type="file" name="image">
            </div>

            <button type="submit" class="submit-btn">
                <i class="fas fa-save"></i> Save Changes
            </button>
        </form>

        <a href="pet-details.php?id=<?php echo sanitize_int($pet['id']); ?>" class="back-link">← Back to Details</a>
    </div>
</body>

</html>