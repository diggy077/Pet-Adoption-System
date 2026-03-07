<?php 
session_start();
include("connection.php");

if(!isset($_SESSION['id'])){
    header("Location: login.php");
    exit();
}

if(isset($_GET['id'])){
    $pet_id = intval($_GET['id']);
    $stmt = $con->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: browsePets.php");
exit();
?>