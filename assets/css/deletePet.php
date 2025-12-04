<?php 
session_start();
include("connection.php");

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){
    $pet_id=intval($_GET['id']);

    $query="DELETE FROM pets WHERE id= $pet_id";
    mysqli_query($con,$query);
}

header("Location: browsePets.php");
exit();
?>