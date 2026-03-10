<?php
session_start();
include("connection.php");
include("includes/functions.php");

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $pet_id = sanitize_int($_GET['id']);

    $stmt = $con->prepare("DELETE FROM pets WHERE id = ?");
    $stmt->bind_param("i", $pet_id);
    $stmt->execute();
    $stmt->close();
}

header("Location: browsePets.php");
exit();
?>