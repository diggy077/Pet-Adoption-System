<?php
$dbhost = "localhost";
$dbuser = "PetAdoption";
$dbpass = "123";
$dbname = "projectdb";

if(!$con=mysqli_connect($dbhost,$dbuser,$dbpass,$dbname))
{
    die("Failed to connect");
}
?>