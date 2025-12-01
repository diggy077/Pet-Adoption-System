<?php 
include("connection.php");

if(!isset($_GET['id'])){
    header("Location: user.php");
    exit();
}

$pet_id = intval($_GET['id']);
$query = "SELECT * FROM pets WHERE id = $pet_id";
$result = mysqli_query($con, $query);
$pet = mysqli_fetch_assoc($result);

if(!$pet){
    echo "<h2>Pet not Found!</h2>";
    exit();
}
?>
<html>
<head>
    <title><?php echo htmlspecialchars($pet['name']); ?> | Pet Details</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Agdasima:wght@400;700&display=swap');
        body{
            font-family:"Agdasima", sans-serif;
            background-color: #f8f6f1;
            margin: 0;
            padding: 0;
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            overflow:hidden;
        }
        .container{
            width:80%;
            max-width: 900px;
            height: 80vh;
            background-color: #ffffff;
            border-radius: 20px;
            display: flex;
            align-items: center;
            overflow:hidden;
        }
        .image-section{
            flex: 1 1 45%;
            height: 100%;
            background-color: #f2efe9;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .image-section img {
            width: 90%;
            aspect-ratio: 1 / 1;  
            border-radius: 15px;
        }
        .details-section {
            flex: 1 1 55%;
            padding: 2rem 2.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        h1 {
            font-size: 1.9rem;
            color: #2b4660;
            margin-bottom: 1rem;
        }
        .info {
            margin: 0.5rem 0;
            font-size: 1.5rem;
            color: #444;
        }
        .info strong {
            color: #2b4660;
            display: inline-block;
            width: 120px;
        }
        .price {
            font-size: 1.5rem;
            font-weight: 600;
            color: #2b4660;
            background-color: #f5eec8;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }
        .adopt-btn {
            margin-top: 2rem;
            background-color: #2b4660;
            color: #fff;
            padding: 0.8rem 1.8rem;
            border-radius: 30px;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            width: fit-content;
        }
        .adopt-btn:hover {
            background-color: #f5eec8;
            color: #2b4660;
        }
    </style>
</head>
<body>
    <div class="container">
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
            <a href="adoption-request.php?id=<?php echo $pet['id']?>" class="adopt-btn">Adopt Now</a>
        </div>
    </div>
</body>
</html>
