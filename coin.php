<?php
session_start();

$host = "localhost";
$user = "root";
$password = "mysql";
$database = "katalogmonety";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Błąd połączenia: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// MONETY + JOIN do państwa
$monety = $conn->query("
    SELECT coin.*, panstwo.nazwa_panstwa
    FROM coin
    LEFT JOIN panstwo ON coin.id_panstwo = panstwo.id
    ORDER BY coin.id DESC
");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Monety - Katalog Monet</title>

<style>

body{
    margin:0;
    font-family:Arial;
    background:#f4f4f4;
}

header{
    background:#8B4513;
    color:white;
    text-align:center;
    padding:30px;
}

.container{
    width:90%;
    max-width:1200px;
    margin:30px auto;
}

.card{
    background:white;
    padding:15px;
    margin-bottom:15px;
    border-radius:8px;
    border:1px solid #ddd;
}

nav{
    background:#654321;
    padding:15px;
    text-align:center;
}

nav a{
    color:white;
    text-decoration:none;
    margin:0 10px;
    font-weight:bold;
}

nav a:hover{
    color:gold;
}

</style>
</head>

<body>

<header>
    <h1>Katalog Monet</h1>
</header>

<nav>
    <a href="index.php">Strona główna</a>
    <a href="coin.php">Monety</a>
    <a href="panstwo.php">Państwa</a>
    <a href="epoki.php">Epoki</a>
    <a href="kontakt.php">Kontakt</a>
</nav>

<div class="container">

<h2>Lista monet</h2>

<?php
if($monety && $monety->num_rows > 0){

    while($row = $monety->fetch_assoc()){

        echo "<div class='card'>";

        echo "<h3>" . htmlspecialchars($row['waluta']) . "</h3>";

        echo "<p><b>Nominał:</b> " . htmlspecialchars($row['nominał']) . "</p>";

        echo "<p><b>Rok bicia:</b> " . htmlspecialchars($row['rok_bicia']) . "</p>";

        echo "<p><b>Państwo:</b> " . htmlspecialchars($row['nazwa_panstwa']) . "</p>";

        echo "</div>";
    }

} else {
    echo "<p>Brak monet w bazie.</p>";
}
?>

</div>

</body>
</html>

<?php
$conn->close();
?>