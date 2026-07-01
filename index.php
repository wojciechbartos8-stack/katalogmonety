<?php
session_start();

$host = "localhost";
$user = "root";
$password = "mysql"; // Zmień jeśli używasz innego hasła w AMPPS
$database = "katalogmonety";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Błąd połączenia z bazą: " . $conn->connect_error);
}

$conn->set_charset("utf8");

// Pobranie 10 najnowszych monet
$sql = "
    SELECT c.*, p.nazwa_panstwa
    FROM coin c
    LEFT JOIN panstwo p ON c.id_panstwo = p.id
    ORDER BY c.id DESC
    LIMIT 10
";

$monety = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Monet</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f4f4;
        }

        header {
            background: #8B4513;
            color: white;
            text-align: center;
            padding: 40px;
        }

        nav {
            background: #654321;
            padding: 15px;
            text-align: center;
        }

        nav a {
            color: white;
            text-decoration: none;
            margin: 0 12px;
            font-weight: bold;
        }

        nav a:hover {
            color: gold;
        }

        .container {
            width: 90%;
            max-width: 1200px;
            margin: 30px auto;
        }

        .box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .card {
            background: #fafafa;
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 8px;
        }

        footer {
            background: #8B4513;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<header>
    <h1>Katalog Monet</h1>
    <p>Internetowy katalog monet świata</p>
</header>

<nav>
    <a href="index.php">Strona główna</a>
    <a href="coin.php">Monety</a>
    <a href="panstwo.php">Państwa</a>
    <a href="kontynenty.php">Kontynenty</a>
    <a href="epoka.php">Epoki</a>
    <a href="contact.php">Kontakt</a>

    <?php if (isset($_SESSION['login'])): ?>

        <?php if (isset($_SESSION['rola']) && $_SESSION['rola'] === 'admin'): ?>
            <a href="admin_panel.php">Panel admina</a>
        <?php else: ?>
            <a href="user_panel.php">Panel użytkownika</a>
        <?php endif; ?>

        <a href="logout.php">Wyloguj</a>

    <?php else: ?>

        <a href="login.php">Zaloguj</a>
        <a href="register.php">Zarejestruj się</a>

    <?php endif; ?>
</nav>

<div class="container">

    <div class="box">
        <h2>Witaj w Katalogu Monet</h2>

        <p>
            Serwis umożliwia przeglądanie monet z różnych państw świata.
            Możesz przeglądać monety według państw, kontynentów oraz epok historycznych.
        </p>
    </div>

    <div class="box">

        <h2>Najnowsze monety</h2>

        <?php
        if ($monety && $monety->num_rows > 0) {

            while ($row = $monety->fetch_assoc()) {

                echo "<div class='card'>";

                echo "<h3>" . htmlspecialchars($row['waluta']) . "</h3>";

                echo "<p><strong>Państwo:</strong> "
                    . htmlspecialchars($row['nazwa_panstwa'] ?? 'Brak danych')
                    . "</p>";

                echo "<p><strong>Nominał:</strong> "
                    . htmlspecialchars($row['nominał'])
                    . "</p>";

                echo "<p><strong>Rok bicia:</strong> "
                    . htmlspecialchars($row['rok_bicia'])
                    . "</p>";

                echo "</div>";
            }

        } else {

            echo "<p>Brak monet w bazie danych.</p>";
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