<?php
session_start();
include("connection.php");
include("navbar.php");

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id'];
$query = "SELECT full_name, email, phone_num, role FROM users WHERE id='$user_id'";
$result = mysqli_query($con, $query);
$user = mysqli_fetch_assoc($result);

$role_text=($user['role']==1)?"Admin" : "User";
?>

<html>
<head>
    <title>My Profile | PetAdopt</title>
    <link rel="stylesheet" href="css/landing.css">
    <style>
        *{
            margin:0;
            padding:0;
            box-sizing: border-box;
        }
        html,body{
            height: auto;
        }
        body{
            font-family: 'Agdasima',sans-serif;
            background-color: #ffffff;
            color: #333;
            line-height:1.6;
            padding-top: 70px;
        }
        .profile-container{
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        .profile-header{
            margin-bottom: 0rem;
            text-align: center;
        }
        .profile-header h1{
            font-size: 2.5rem;
            color: #2b4660;
            margin-bottom: 0.5rem;
        }
        .profile-info-card{
            background-color: #F5EEC8;
            border: 2px solid #2b4660;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
        }
        .profile-photo{
            width: 120px;
            height:120px;
            border-radius:50%;
            background-color: #88BDF2;
            display:flex;
            align-items:center;
            justify-content:center;
            border: 3px solid #2b4660;
            font-size: 3rem;
            color: #2b4660;
        }
        .profile-details{
            flex:1;
            padding-left:2rem;
        }
        .profile-name{
            font-size: 2rem;
            font-weight: 70px;
            color: #2b4660;
            margin-bottom:0.5rem;
        }
        .profile-role {
            font-size: 1.2rem;
            color: #666;
            background-color: #88BDF2;
            padding: 0.3rem 1rem;
            border-radius: 20px;
            display: inline-block;
        }
        .personal-info-section{
            background-color: #F5EEC8;
            border: 2px solid #2b4660;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 0rem;
        }
        .section-header{
            display:flex;
            justify-content:space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #2b4660;
        }
        .section-header h2{
            font-size:1.8rem;
            color: #2b4660;
        }
        .edit-btn{
            background-color: #88BDF2;
            border: 2px solid #2b4660;
            border-radius: 8px;
            padding: 0.5rem 1.5rem;
            font-family: "Agdasima";
            font-weight: 700;
            font-size: 1.1rem;
            cursor: pointer;
            text-decoration: none;
            color: black;
        }
        .edit-btn:hover{
            background-color: #5596D6;
            color: #ffffff;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
        }
        .info-item{
            background-color: #ffffff;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #88BDF2
        }
        .info-label{
            font-weight: 700;
            color: #2b4660;
            font-size: 1.2rem;
            margin-bottom: 0.5rem;
        }
        .info-value{
            font-size: 1.2rem;
            color: #333;
        }
    </style>
</head>
<body>
    <main>
        <div class="profile-container">
            <section class="profile-header">
                <h1>MY PROFILE</h1>
            </section>
            <section class="profile-info-card">
                <div class="profile-photo">
                    🐾
                </div>
                <div class="profile-details">
                    <div class="profile-name">
                        <?php echo htmlspecialchars($user['full_name']); ?>
                    </div>
                    <div class="profile-role"><?php echo $role_text; ?></div>
                </div>
            </section>
            <section class="personal-info-section">
                <div class=section-header>
                    <h2>PERSONAL INFORMATION</h2>
                    <a href="edit_profile.php" class="edit-btn">EDIT</a>
                </div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">NAME</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">EMAIL</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">PHONE NUMBER</div>
                        <div class="info-value">
                            <?php echo htmlspecialchars($user['phone_num']); ?>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
