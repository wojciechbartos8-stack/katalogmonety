<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>User Panel</title>

<style>
body{font-family:Arial;background:#f4f4f4;}
.box{
    width:70%;
    margin:40px auto;
    background:white;
    padding:20px;
    border-radius:10px;
}
a{
    display:block;
    margin:10px 0;
    padding:10px;
    background:#1e88e5;
    color:white;
    text-decoration:none;
    border-radius:6px;
}
</style>
</head>

<body>

<div class="box">

<h1>?? User Panel</h1>
<p>Witaj: <b><?= $_SESSION['user'] ?></b></p>

<hr>

<a href="kontynenty.php">?? Katalog monet</a>
<a href="add_panstwo.php">?? Dodaj państwo</a>
<a href="add_coin.php">?? Dodaj monetę</a>
<a href="logout.php">?? Wyloguj</a>

</div>

</body>
</html>