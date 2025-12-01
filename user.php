<?php
session_start();
include("connection.php");
include("navbar.php");

if(!isset($_SESSION['email'])){
    header("Location: login.php");
    exit();
}

$user_id=$_SESSION['id'];
$query="SELECT full_name, email, phone_num, role FROM users WHERE id='$user_id'";
$result=mysqli_query($con, $query);
$user=mysqli_fetch_assoc($result);

$pets_query="SELECT * FROM pets WHERE status= 'available' LIMIT 3";
$pets_result= mysqli_query($con, $pets_query);

?>
<!DOCTYPE html>
<html>
    <head>
    <title>User Page | PetAdopt</title>
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        *{
            margin:0;
            padding:0;
        }
        html,body{
            height: auto ;
        }
        body{
            font-family:'Agdasima', sans-serif;
            background-color: #ffffff;
            color: #333;
            line-height:1.6;
            padding-top: 70px;
        }
        .dashboard{
            max-width: 1200px;
            margin: 0 auto;
            padding:2rem;
        }
        .welcome{
            text-align:center;
            padding:3rem 2rem;
            background-color: #F5EEC8;
            border: 2px solid #2b4660;
            border-radius:15px;
            margin-bottom:2rem;
        }
        .welcome h1{
            font-size:2.5rem;
            margin-bottom:1rem;
            color: #2b4660;
        }
        .welcome p{
            font-size: 20px;
            color: #2b4660;
        }
        .recommended-pets{
            border: 2px solid #2b4660;
            border-radius:15px;
            background-color: #ffffffff;
            padding: 3rem 2rem;
            margin-bottom: 2rem;
        }
        .recommended-pets h2{
            text-align:center;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: #2b4660;
        }
        .pet-grid{
            display:flex;
            gap: 2rem;
            justify-content:center;
        }
        .pet-link {
            text-decoration: none;
            color: inherit;
        }
        .pet-card{
            margin: 0 0.5rem;
        }
        .pet-card h2{
            margin-bottom:10px;
        }
        .pet-card p{
            font-size: 20px;
        }
    </style>
    </head>
    <body>
        <div class="dashboard">
            <section class="welcome">
                <h1>Welcome, <?php echo$user['full_name'] ?>! 🐾</h1>
                <p>We're excited to help you find your perfect companion.Start  your adoption journey today!</p>
            </section>
        <section class="recommended-pets">
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
        </div>
        </section>
    </body>
</html>
<?php include('footer.php') ?>