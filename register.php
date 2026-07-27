<?php
require_once "db_connect.php";

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $login = $_POST['login'];
    $pass  = $_POST['password'];

    mysqli_query($conn,"INSERT INTO users(login,password,role)
    VALUES('$login','$pass','user')");

    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Rejestracja</title>

<style>
body{font-family:Arial;background:#f4f4f4;}
.box{
    width:300px;
    margin:80px auto;
    background:white;
    padding:20px;
    border-radius:10px;
}
input,button{
    width:100%;
    padding:10px;
    margin:5px 0;
}
</style>
</head>

<body>

<div class="box">

<h2>?? Rejestracja</h2>

<form method="post">
<input name="login" placeholder="Login">
<input name="password" type="password" placeholder="Has³o">
<button>Rejestruj</button>
</form>

</div>

</body>
</html>