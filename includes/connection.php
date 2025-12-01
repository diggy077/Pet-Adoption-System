<?php

$con = mysqli_connect("localhost", "digbj", "digbj123", "petadopt");

if(!$con){
    die("Connection failed: " . mysqli_connect_error());
}
?>