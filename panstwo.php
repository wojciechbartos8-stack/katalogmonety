<?php
session_start();

$host = "localhost";
$user = "root";
$password = "mysql";
$database = "katalogmonety";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// POBRANIE PAŃSTW (POPRAWIONE)
$panstwa = $conn->query("
    SELECT *
    FROM panstwo
    ORDER BY nazwa_panstwa ASC
");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Państwa - Katalog Monet</title>

<style>

body{
    margin:0;
    font-family:Arial, sans-serif;
    background:#f4f4f4;
}

header{
    background:#8B4513;
    color:white;
    text-align:center;
    padding:40px;
}

nav{
    background:#654321;
    padding:15px;
    text-align:center;
}

nav a{
    color:white;
    text-decoration:none;
    margin:0 15px;
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

.box{
    background:white;
    padding:20px;
    border-radius:10px;
    margin-bottom:20px;
}

.card{
    background:#fafafa;
    border:1px solid #ddd;
    border-radius:8px;
    padding:15px;
    margin-bottom:10px;
}

footer{
    background:#8B4513;
    color:white;
    text-align:center;
    padding:20px;
    margin-top:30px;
}

</style>
</head>

<body>

<header>
    <h1>Państwa</h1>
    <p>Lista państw w katalogu monet</p>
</header>

<nav>

    <a href="index.php">Strona główna</a>
    <a href="coin.php">Monety</a>
    <a href="panstwo.php">Państwa</a>
    <a href="epoki.php">Epoki</a>
    <a href="kontakt.php">Kontakt</a>

    <?php if(isset($_SESSION['login'])): ?>

        <?php if(isset($_SESSION['rola']) && $_SESSION['rola'] == 'admin'): ?>
            <a href="panel_admin.php">Panel admina</a>
        <?php else: ?>
            <a href="user_panel.php">Panel użytkownika</a>
        <?php endif; ?>

        <a href="wyloguj.php">Wyloguj</a>

    <?php else: ?>

        <a href="logowanie.php">Zaloguj</a>
        <a href="rejestracja.php">Zarejestruj się</a>

    <?php endif; ?>

</nav>

<div class="container">

    <div class="box">
        <h2>Lista państw</h2>

        <?php
        if($panstwa && $panstwa->num_rows > 0){

            while($row = $panstwa->fetch_assoc()){

                echo "<div class='card'>";

                echo "<h3>" . htmlspecialchars($row['nazwa_panstwa']) . "</h3>";

                echo "</div>";
            }

        } else {
            echo "<p>Brak państw w bazie danych.</p>";
        }
        ?>

    </div>

</div>

<footer>
    Katalog Monet &copy; <?php echo date("Y"); ?>
</footer>

</body>
</html>

<?php
$conn->close();
?>