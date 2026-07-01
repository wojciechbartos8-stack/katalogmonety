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

// POBRANIE KONTYNENTÓW
$kontynenty = $conn->query("
    SELECT *
    FROM kontynenty
    ORDER BY nazwa_kontynentu ASC
");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kontynenty - Katalog Monet</title>

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

</style>
</head>

<body>

<header>
    <h1>Kontynenty</h1>
</header>

<nav>
    <a href="index.php">Strona główna</a>
    <a href="coin.php">Monety</a>
    <a href="panstwo.php">Państwa</a>
    <a href="kontynenty.php">Kontynenty</a>
    <a href="epoki.php">Epoki</a>
    <a href="kontakt.php">Kontakt</a>
</nav>

<div class="container">

<h2>Lista kontynentów</h2>

<?php
if($kontynenty && $kontynenty->num_rows > 0){

    while($row = $kontynenty->fetch_assoc()){

        echo "<div class='card'>";
        echo "<h3>" . htmlspecialchars($row['nazwa_kontynentu']) . "</h3>";
        echo "</div>";
    }

} else {
    echo "<p>Brak kontynentów w bazie.</p>";
}
?>

</div>

</body>
</html>

<?php
$conn->close();
?>