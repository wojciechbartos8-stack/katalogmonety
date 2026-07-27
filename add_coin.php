<?php
session_start();
if(!isset($_SESSION['user'])) die("Brak dostêpu");

require_once "db_connect.php";

if($_SERVER['REQUEST_METHOD'] == "POST"){

    $id_panstwo = (int)$_POST['id_panstwo'];
    $waluta = $_POST['waluta'];
    $nominal = $_POST['nominal'];
    $rok = (int)$_POST['rok_bicia'];

    mysqli_query($conn, "INSERT INTO coin(id_panstwo,waluta,nomina³,rok_bicia)
    VALUES($id_panstwo,'$waluta','$nominal',$rok)");

    header("Location: kontynenty.php");
    exit;
}
?>

<form method="post">
<h2>Dodaj monetê</h2>

ID pañstwa:<br>
<input name="id_panstwo"><br>

Waluta:<br>
<input name="waluta"><br>

Nomina³:<br>
<input name="nominal"><br>

Rok bicia:<br>
<input name="rok_bicia"><br>

<button>Dodaj</button>
</form>