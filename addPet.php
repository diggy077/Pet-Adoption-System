<?php
session_start();
include('connection.php');
include('navbar.php');
include('includes/functions.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = sanitize_string($_POST['name']);
    $age         = sanitize_int($_POST['age']);
    $gender      = sanitize_string($_POST['gender']);
    $breed       = sanitize_string($_POST['breed']);
    $price       = sanitize_price($_POST['price']);
    $weight      = sanitize_string($_POST['weight']);
    $category    = sanitize_string($_POST['category']);
    $description = sanitize_text($_POST['description']);

    $image_name = sanitize_image_filename($_FILES['image']['name']);
    $image_tmp  = $_FILES['image']['tmp_name'];
    $upload_folder = "assets/images/";

    if (empty($name) || empty($age) || empty($gender) || empty($breed) || empty($price) || empty($weight) || empty($category) || empty($description)) {
        $message = "<div class='message error_message'>All fields are required.</div>";
    }

    if (empty($message)) {
        if ($age <= 0) {
            $message = "<div class='message error_message'>Age must be a positive number.</div>";
        }
    }

    if (empty($message)) {
        if ($price <= 0) {
            $message = "<div class='message error_message'>Price must be a positive number.</div>";
        } else if ($price == 0) {
            $message = "Price must be a positive number.";
        }
    }
    
    if (empty($message)) {
        if ($weight <= 0) {
            $message = "<div class='message error_message'>Weight must be a positive number.</div>";
        } else if ($weight == 0) {
            $message = "Weight must be a positive number.";
        }
    }

    if (empty($message)) {
        if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $message = "<div class='message error_message'>Image upload failed.</div>";
        } else {
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed_ext)) {
                $message = "<div class='message error_message'>Image must be a jpg, jpeg, png, gif, or webp file.</div>";
            }
        }
    }

    if (empty($message)) {
        if (move_uploaded_file($image_tmp, $upload_folder . $image_name)) {
            $stmt = $con->prepare("INSERT INTO pets (name, age, gender, breed, price, image, category, description, weight) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("sissdssss", $name, $age, $gender, $breed, $price, $image_name, $category, $description, $weight);
            if ($stmt->execute()) {
                $message = "<div class='message success_message'>New Pet Added Successfully!</div>";
            } else {
                $message = "<div class='message error_message'>Error Adding New Pet!</div>";
            }
            $stmt->close();
        } else {
            $message = "<div class='message error_message'>Error Uploading Pet Image!</div>";
        }
    }
}
?>
<html>

<head>
    <title>Add Pets | PetAdopt</title>
</head>

<body>
    <div class="container">
        <h2>➕ Add New Pet</h2>

        <?php
        if (!empty($message)) {
            echo $message;
        }
        ?>

        <form action="addPet.php" method="POST" enctype="multipart/form-data">
            <div class="addPetinput">
                <label for="name">Pet Name</label>
                <input type="text" name="name">
            </div>

            <div class="addPetinput">
                <label for="age">Age (months)</label>
                <input type="number" name="age">
            </div>

            <div class="addPetinput">
                <label for="gender">Gender</label>
                <select name="gender">
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>
            </div>

            <div class="addPetinput">
                <label for="breed">Breed</label>
                <input type="text" name="breed">
            </div>

            <div class="addPetinput">
                <label for="price">Price</label>
                <input type="number" name="price">
            </div>

            <div class="addPetinput">
                <label for="weight">Weight (in kg)</label>
                <input type="text" name="weight">
            </div>

            <div class="addPetinput">
                <label for="image">Pet Image</label>
                <input type="file" name="image">
            </div>

            <div class="addPetinput">
                <label for="category">Category</label>
                <select name="category">
                    <option value="dog">Dog</option>
                    <option value="cat">Cat</option>
                    <option value="rabbit">Rabbit</option>
                </select>
            </div>

            <div class="addPetinput">
                <label for="description">Description</label>
                <textarea name="description"></textarea>
            </div>

            <button type="submit" class="submit-btn">Add Pet</button>
        </form>
    </div>
</body>

</html>