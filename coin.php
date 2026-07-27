<?php
require_once "db_connect.php";

$id = (int)$_GET['id'];

$sql = "SELECT * FROM coin WHERE id=$id";
$res = mysqli_query($conn, $sql);
$c = mysqli_fetch_assoc($res);

if(!$c){
    die("Nie znaleziono monety");
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Moneta #<?= $c['id'] ?></title>

<style>
body{
    font-family: Arial;
    background:#f4f4f4;
}

.container{
    width:70%;
    margin:30px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

h1{
    text-align:center;
}

.field{
    padding:5px 0;
    border-bottom:1px solid #eee;
}
</style>

</head>

<body>

<div class="container">

<h1>?? Moneta ID: <?= $c['id'] ?></h1>

<?php
// 1. Najważniejsze pola
if(isset($c['waluta']))
    echo "<div class='field'><b>Waluta:</b> ".$c['waluta']."</div>";

if(isset($c['nominał']))
    echo "<div class='field'><b>Nominał:</b> ".$c['nominał']."</div>";

if(isset($c['rok_bicia']))
    echo "<div class='field'><b>Rok bicia:</b> ".$c['rok_bicia']."</div>";

echo "<hr>";

// 2. AUTOMATYCZNIE WSZYSTKIE POLA
foreach($c as $key => $value)
{
    if($key == "id" || $key == "id_panstwo") continue;

    echo "<div class='field'><b>"
        .ucwords(str_replace("_"," ",$key))
        .":</b> ".$value."</div>";
}
?>

</div>

</body>
</html>