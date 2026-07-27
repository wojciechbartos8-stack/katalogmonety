<?php
require_once "db_connect.php";

$login = "admin";
$pass = password_hash("1234", PASSWORD_DEFAULT);

mysqli_query($conn,"INSERT INTO users(login,password,role)
VALUES('$login','$pass','admin')");

echo "ADMIN UTWORZONY";
?>