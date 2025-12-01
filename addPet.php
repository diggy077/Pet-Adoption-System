<?php
session_start();
include('connection.php');
include('navbar.php');

if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $age = $_POST['age'];
    $gender = $_POST['gender'];
    $breed = $_POST['breed'];
    $price = $_POST['price'];
    $category = $_POST['category'];
    $description = $_POST['description'];

    $image_name = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];

    $upload_folder = "assets/images/";

    if (move_uploaded_file($image_tmp, $upload_folder . $image_name)) {

        $sql = "INSERT INTO pets (name, age, gender, breed, price, image, category, description) 
                VALUES ('$name', '$age', '$gender', '$breed', '$price', '$image_name', '$category', '$description')";

        if (mysqli_query($con, $sql)) {
            $message = "<div class='message success_message'>New Pet Added Successfully!</div>";
        } else {
            $message = "<div class='message error_message'>Error Adding New Pet!</div>";
        }
    } else {
        $message = "<div class='message error_message'>Error Adding New Pet!</div>";
    }
}
?>
<html>

<head>
    <title>Add Pets| PetAdopt</title>
    <style>
        body {
            font-family: 'Agdasima';
        }

        .container {
            width: 50vw;
            margin: 1.5rem auto;
            padding: 1rem 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .container h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            color: #2b4660;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            font-weight: bold;
            font-size: 1.5rem;
            text-align: center;
        }

        .success_message {
            color: green;
        }

        .error_message {
            color: red;
        }

        form {
            text-align: left;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            font-size: 1.25rem;
            font-weight: bold;
            /* margin-top: 1rem; */
            color: #2b4660;
        }

        .container input[type="file"] {
            margin-top: 0.3rem;
            margin-bottom: 0.5rem;
            font-family: 'Agdasima';
        }

        /* .addPetinput {
            width: 100%;
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 1rem;
        } */

        .container input[type="text"],
        .container input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 0.3rem;
            border: 1px solid #88BDF2;
            border-radius: 6px;
            margin-top: 0.3rem;
            margin-bottom: rem;
            font-size: 1rem;
            font-family: 'Agdasima';
        }

        textarea {
            height: 80px;
            margin-bottom: 0rem;
        }

        .submit-btn {
            margin-top: 0.5rem;
            margin-bottom: 1rem;
            width: 100%;
            background-color: #88BDF2;
            border: 0rem;
            border-radius: 10px;
            padding: 7px;
            font-size: 1.5rem;
            font-family: 'Agdasima';
            font-weight: bold;
            cursor: pointer;
        }

        .submit-btn:hover {
            background-color: #5596D6;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>➕ Add New Pet</h2>
        <?php if (!empty($message)) { ?>
            <?= $message ?>
        <?php } ?>

        <form action="addPet.php" method="POST" enctype="multipart/form-data">
            <div class="addPetinput">
                <label for="name">Pet Name</label>
                <input type="text" name="name" required>
            </div>

            <div class="addPetinput">
                <label for="age">Age (months)</label>
                <input type="number" name="age" required>
            </div>

            <div class="addPetinput">
                <label for="gender">Gender</label>
                <select name="gender">
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>

            <div class="addPetinput">
                <label for="breed">Breed</label>
                <input type="text" name="breed" required>
            </div>

            <div class="addPetinput">
                <label for="price">Price</label>
                <input type="number" name="price" required>
            </div>

            <div class="addPetinput">
                <label for="image">Pet Image</label>
                <input type="file" name="image" required>
            </div>

            <div class="addPetinput">
                <label for="category">Category</label>
                <select name="category" required>
                    <option value="Dog">Dog</option>
                    <option value="Cat">Cat</option>
                    <option value="Rabbit">Rabbit</option>
                    <select>
            </div>

            <div class="addPetinput">
                <label for="description">Description</label>
                <textarea name="description" required></textarea>
            </div>

            <button type="submit" class="submit-btn">Add Pet</button>
        </form>
    </div>
</body>

</html>