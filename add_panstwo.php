<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: index.php");
    exit;
}

require_once "db_connect.php";

if($_SERVER['REQUEST_METHOD']=="POST"){

    $kont = (int)$_POST['id_kontynent'];
    $name = $_POST['nazwa_panstwa'];

    mysqli_query($conn,"INSERT INTO panstwo(id_kontynent,nazwa_panstwa)
    VALUES($kont,'$name')");

    header("Location: kontynenty.php");
    exit;
}
?>

<form method="post">
<h2>Dodaj pañstwo</h2>

ID kontynentu:<br>
<input name="id_kontynent"><br><br>

Nazwa pañstwa:<br>
<input name="nazwa_panstwa"><br><br>

<button>Dodaj</button>
</form>