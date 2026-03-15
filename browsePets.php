<?php
session_start();
include("connection.php");
include("navbar.php");
include("includes/functions.php");

if (isset($_SESSION['id'])) {
    $user_role = $_SESSION['role'];
    $user_id   = $_SESSION['id'];
}

$status = "available";

$params = [];
$types   = "";
$sql     = "SELECT * FROM pets WHERE status = ?";
$params[] = $status;
$types   .= "s";

if (isset($user_role) && $user_role === 'admin') {
    $sql .= " AND admin_id = ?";
    $params[] = $user_id;
    $types .= "i";
}

if (isset($_GET['category']) && !empty($_GET['category'])) {
    $category = sanitize_string($_GET['category']);
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$stmt = $con->prepare($sql);

$stmt->bind_param($types, ...$params);

$stmt->execute();
$pets_result = $stmt->get_result();

$stmt->execute();
$pets_result = $stmt->get_result();

if (isset($_POST['add_category'])) {

    $new_category = strtolower(sanitize_string($_POST['category_name']));

    $result = $con->query("SHOW COLUMNS FROM pets LIKE 'category'");
    $row = $result->fetch_assoc();

    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
    $enum_values = str_getcsv($matches[1], ',', "'");

    if (!in_array($new_category, $enum_values)) {

        $enum_values[] = $new_category;

        $enum_string = "'" . implode("','", $enum_values) . "'";

        $alter_query = "ALTER TABLE pets MODIFY category ENUM($enum_string)";

        if ($con->query($alter_query)) {
            $success_message = "Category added successfully!";
        } else {
            $error_message = "Failed to add category.";
        }
    } else {
        $error_message = "Category already exists.";
    }
}
?>

<html>

<head>
    <title>Browse Pets | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <?php
    if (!empty($success_message)) {
        echo '<div class="success-message">' . e($success_message) . '</div>';
    }

    if (!empty($error_message)) {
        echo '<div class="error-message">' . e($error_message) . '</div>';
    }
    ?>
    <div class="browse-container">

        <div class="browse-header">
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === "admin") { ?>

                <h1>🐾 Your Pets</h1>

            <?php } else { ?>

                <h1>🐾 Browse All Pets</h1>
                <p>Find your perfect companion from our list of adorable pets available for adoption.</p>

            <?php } ?>



            <form method="GET" action="browsePets.php" class="category-form">
                <label for="category" style="font-size:1.1rem; font-weight:bold; color:#2b4660;">Category:</label>
                <select name="category" id="category" onchange="this.form.submit()">
                    <option value="">All</option>

                    <?php
                    $result = $con->query("SHOW COLUMNS FROM pets LIKE 'category'");
                    $row = $result->fetch_assoc();

                    preg_match("/^enum\((.*)\)$/", $row['Type'], $matches);
                    $categories = str_getcsv($matches[1], ',', "'");

                    foreach ($categories as $cat) {

                        $selected = "";
                        if (isset($_GET['category']) && strtolower($_GET['category']) == $cat) {
                            $selected = "selected";
                        }

                        echo "<option value='$cat' $selected>" . ucfirst($cat) . "</option>";
                    }
                    ?>

                </select>
            </form>

            <?php
            if (isset($user_role) && $user_role === 'admin') {
                echo '
    <div class="admin-options">
        <button class="adopt-btn" onclick="openCategoryModal()">📂 Add Category</button>
        <a href="addPet.php" class="adopt-btn">➕ Add New Pet</a>
    </div>';
            }
            ?>
        </div>

        <div class="pet-grid">
            <?php
            if ($pets_result && mysqli_num_rows($pets_result) > 0) {
                while ($pet = mysqli_fetch_assoc($pets_result)) {
                    echo '
                    <div class="pet-card">
                        <img src="assets/images/' . e($pet['image'] ?? 'default_pet.jpg') . '" alt="' . e($pet['name']) . '">
                        <h2>' . e($pet['name']) . '</h2>
                        <p>' . e($pet['breed']) . '</p>
                        <p>' . e($pet['age']) . ' months old</p>
                        <a href="pet-details.php?id=' . sanitize_int($pet['id']) . '" class="adopt-btn">View Details</a>';

                    if (isset($user_role) && $user_role === 'admin') {
                        echo '
                        <button class="adopt-btn" style="background-color:red; color:white; margin-top:5px;" onclick="openDeleteModal(' . sanitize_int($pet['id']) . ')">
                            <i class="fa-solid fa-trash"></i>
                        </button>';
                    }

                    echo '</div>';
                }
            } else {
                echo '<h2 style="color:red; text-align:center;">No pets available at the moment 😿</h2>';
            }
            ?>
        </div>
    </div>

    <div id="categoryModal">
        <div id="categoryModalContent">

            <h3>Add Pet Category</h3>

            <form method="POST" action="">
                <input type="text" name="category_name" placeholder="Enter category name" required>

                <div style="margin-top:15px;">
                    <button type="submit" name="add_category" id="saveCategoryBtn">Add</button>
                    <button type="button" id="cancelCategoryBtn" onclick="closeCategoryModal()">Cancel</button>
                </div>
            </form>

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
        function openCategoryModal() {
            document.getElementById("categoryModal").style.display = "block";
        }

        function closeCategoryModal() {
            document.getElementById("categoryModal").style.display = "none";
        }

        window.onclick = function(event) {

            const categoryModal = document.getElementById("categoryModal");

            if (event.target === categoryModal) {
                categoryModal.style.display = "none";
            }

        }

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