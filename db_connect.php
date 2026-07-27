<?php
$host = "localhost";
$user = "root";
$pass = "mysql"; // albo "" jeśli u Ciebie działa
$db   = "katalogmonety";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Błąd DB: " . mysqli_connect_error());
}
?>