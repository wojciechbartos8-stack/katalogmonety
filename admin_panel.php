<?php
session_start();

if(!isset($_SESSION['role']) || $_SESSION['role'] != "admin"){
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Admin Panel</title>

<style>
body{font-family:Arial;background:#222;color:white;}
.box{
    width:70%;
    margin:40px auto;
    background:#333;
    padding:20px;
    border-radius:10px;
}
a{
    display:block;
    margin:10px 0;
    padding:10px;
    background:#e53935;
    color:white;
    text-decoration:none;
    border-radius:6px;
}
</style>
</head>

<body>

<div class="box">

<h1>?? Admin Panel</h1>
<p>Witaj admin: <b><?= $_SESSION['user'] ?></b></p>

<hr>

<a href="kontynenty.php">?? Katalog monet</a>
<a href="add_panstwo.php">?? Dodaj państwo</a>
<a href="add_coin.php">?? Dodaj monetę</a>
<a href="logout.php">?? Wyloguj</a>

</div>

</body>
</html>