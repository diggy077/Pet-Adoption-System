<?php
session_start();
include('connection.php');
include('navbar.php');
include('includes/functions.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name        = sanitize_string($_POST['name']);
    $age         = sanitize_int($_POST['age']);
    $gender      = sanitize_string($_POST['gender']);
    $breed       = sanitize_string($_POST['breed']);
    $price       = sanitize_price($_POST['price']);
    $weight      = sanitize_price($_POST['weight']);
    $category    = sanitize_string($_POST['category']);
    $description = sanitize_text($_POST['description']);

    $image_name    = sanitize_image_filename($_FILES['image']['name']);
    $image_tmp     = $_FILES['image']['tmp_name'];
    $upload_folder = "assets/images/";

    $_SESSION['form_data'] = [
        'name'        => $name,
        'age'         => $age,
        'gender'      => $gender,
        'breed'       => $breed,
        'price'       => $price,
        'weight'      => $weight,
        'category'    => $category,
        'description' => $description,
    ];

    if (empty($name) || empty($age) || empty($gender) || empty($breed) || empty($price) || empty($weight) || empty($category) || empty($description)) {
        $_SESSION['error_message'] = "All fields are required.";
        header("Location: addPet.php");
        exit;
    }

    if (!preg_match("/^[a-zA-Z\s'\-]+$/", $name)) {
        $_SESSION['error_message'] = "Pet name must contain letters only, no numbers or special characters.";
        header("Location: addPet.php");
        exit;
    }

    if ($age <= 0) {
        $_SESSION['error_message'] = "Age must be a positive number.";
        header("Location: addPet.php");
        exit;
    }

    if ($price < 0) {
        $_SESSION['error_message'] = "Price cannot be negative.";
        header("Location: addPet.php");
        exit;
    }

    if ($price == 0) {
        $_SESSION['error_message'] = "Price must be a positive number.";
        header("Location: addPet.php");
        exit;
    }

    if (!is_numeric($weight) || $weight < 0) {
        $_SESSION['error_message'] = "Weight cannot be negative.";
        header("Location: addPet.php");
        exit;
    }

    if ($weight == 0) {
        $_SESSION['error_message'] = "Weight must be a positive number.";
        header("Location: addPet.php");
        exit;
    }

    if ($_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $_SESSION['error_message'] = "Image upload failed.";
        header("Location: addPet.php");
        exit;
    }

    $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_ext)) {
        $_SESSION['error_message'] = "Image must be a jpg, jpeg, png, gif, or webp file.";
        header("Location: addPet.php");
        exit;
    }

    if (file_exists($upload_folder . $image_name)) {
        $_SESSION['error_message'] = "An image with that filename already exists. Please rename your image and try again.";
        header("Location: addPet.php");
        exit;
    }

    if (move_uploaded_file($image_tmp, $upload_folder . $image_name)) {
        $admin_id = $_SESSION['id'];
        $stmt = $con->prepare("INSERT INTO pets (name, age, gender, breed, price, image, category, description, weight, admin_id) 
                               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissdssssi", $name, $age, $gender, $breed, $price, $image_name, $category, $description, $weight, $admin_id);

        if ($stmt->execute()) {
            unset($_SESSION['form_data']);
            $_SESSION['success_message'] = "New Pet Added Successfully!";
            header("Location: addPet.php");
            exit;
        } else {
            $_SESSION['error_message'] = "Error Adding New Pet!";
            header("Location: addPet.php");
            exit;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Error Uploading Pet Image!";
        header("Location: addPet.php");
        exit;
    }
}

$message = "";
if (!empty($_SESSION['success_message'])) {
    $message = "<div class='message success_message'>" . e($_SESSION['success_message']) . "</div>";
    unset($_SESSION['success_message']);
}

if (!empty($_SESSION['error_message'])) {
    $message = "<div class='message error_message'>" . e($_SESSION['error_message']) . "</div>";
    unset($_SESSION['error_message']);
}

$form_data = [];
if (!empty($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    unset($_SESSION['form_data']);
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
                <input type="text" name="name"
                    value="<?php echo !empty($form_data['name']) ? e($form_data['name']) : ''; ?>">
            </div>

            <div class="addPetinput">
                <label for="age">Age (months)</label>
                <input type="number" name="age"
                    value="<?php echo !empty($form_data['age']) ? e($form_data['age']) : ''; ?>">
            </div>

            <div class="addPetinput">
                <label for="gender">Gender</label>
                <select name="gender">
                    <option value="Male" <?php echo (!empty($form_data['gender']) && $form_data['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (!empty($form_data['gender']) && $form_data['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                </select>
            </div>

            <div class="addPetinput">
                <label for="breed">Breed</label>
                <input type="text" name="breed"
                    value="<?php echo !empty($form_data['breed']) ? e($form_data['breed']) : ''; ?>">
            </div>

            <div class="addPetinput">
                <label for="price">Price</label>
                <input type="number" name="price"
                    value="<?php echo !empty($form_data['price']) ? e($form_data['price']) : ''; ?>">
            </div>

            <div class="addPetinput">
                <label for="weight">Weight (in kg)</label>
                <input type="text" name="weight"
                    value="<?php echo !empty($form_data['weight']) ? e($form_data['weight']) : ''; ?>">
            </div>

            <div class="addPetinput">
                <label for="image">Pet Image</label>
                <input type="file" name="image" id="image" accept=".jpg,.jpeg,.png,.gif,.webp">
            <small id="imageError" style="color:red; display:block; margin-top:3px;"></small>
            </div>

            <div class="addPetinput">
                <label for="category">Category</label>
                <select name="category">
                    <?php
                    $result = $con->query("SHOW COLUMNS FROM pets LIKE 'category'");
                    $row = $result->fetch_assoc();
                    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
                    $categories = str_getcsv($matches[1], ',', "'");

                    foreach ($categories as $cat) {
                        $selected = (!empty($form_data['category']) && $form_data['category'] == $cat) ? 'selected' : '';
                        echo "<option value='" . e($cat) . "' $selected>" . ucfirst($cat) . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="addPetinput">
                <label for="description">Description</label>
                <textarea name="description"><?php echo !empty($form_data['description']) ? e($form_data['description']) : ''; ?></textarea>
            </div>

            <button type="submit" class="submit-btn">Add Pet</button>
        </form>
    </div>
</body>

<script>
    document.getElementById("image").addEventListener("change", function () {
        const file = this.files[0];
        const error = document.getElementById("imageError");
        const allowed = ["jpg", "jpeg", "png", "gif", "webp"];
        const maxSize = 2 * 1024 * 1024;

        error.textContent = "";

        if (file) {
            const ext = file.name.split(".").pop().toLowerCase();
            if (!allowed.includes(ext)) {
                error.textContent = "Image must be a jpg, jpeg, png, gif, or webp file.";
                this.value = "";
            } else if (file.size > maxSize) {
                error.textContent = "Image must not exceed 2MB.";
                this.value = "";
            }
        }
    });
</script>

</html>