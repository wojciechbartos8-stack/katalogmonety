<?php
$host = "localhost";
$dbname = "katalogmonety";
$user = "root";
$password = "mysql"; // jeśli u Ciebie AMPPS ma inne hasło, zmień tutaj

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą katalogmonety: " . $conn->connect_error);
}

$conn->set_charset("utf8");
?>