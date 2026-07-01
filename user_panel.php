<?php
// ?? Po³±czenie z baz± danych
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "katalogmonety";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("? B³±d po³±czenia z baz± danych: " . $conn->connect_error);
}

// ?? Obs³uga formularza po klikniêciu przycisku "Dodaj"
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $country = trim($_POST['country']);
    $coin = trim($_POST['coin']);
    $year = (int)$_POST['year'];

    if (!empty($country) && !empty($coin) && $year > 0) {
        // Zabezpieczenie przed SQL injection
        $stmt = $conn->prepare("INSERT INTO coin (country, coin_name, year) VALUES (?, ?, ?)");
        $stmt->bind_param("ssi", $country, $coin, $year);

        if ($stmt->execute()) {
            echo "<p style='color:green;'>? Moneta <b>$coin</b> z kraju <b>$country</b> (rok $year) zosta³a dodana do bazy danych.</p>";
        } else {
            echo "<p style='color:red;'>? B³±d zapisu do bazy: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color:red;'>?? Wype³nij wszystkie pola!</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Panel u¿ytkownika — dodawanie monet</title>
<style>
    body {
        font-family: Arial;
        background: #eef1f5;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
    }
    form {
        background: white;
        padding: 20px 25px;
        border-radius: 10px;
        box-shadow: 0 0 10px #999;
        width: 350px;
    }
    h2 {
        text-align: center;
        color: #333;
    }
    label {
        font-weight: bold;
        margin-top: 8px;
        display: block;
    }
    input {
        width: 100%;
        padding: 8px;
        margin-top: 4px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }
    button {
        margin-top: 15px;
        width: 100%;
        background: #4CAF50;
        color: white;
        padding: 10px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }
    button:hover {
        background: #45a049;
    }
</style>
</head>
<body>

<form method="POST">
    <h2>?? Dodaj now± monetê</h2>

    <label>Pañstwo:</label>
    <input type="text" name="country" placeholder="np. Polska" required>

    <label>Nazwa monety:</label>
    <input type="text" name="coin" placeholder="np. 2 z³ W³adys³aw Jagie³³o" required>

    <label>Rok bicia:</label>
    <input type="number" name="year" placeholder="np. 2023" min="1000" max="2100" required>

    <button type="submit">? Dodaj do bazy</button>
</form>

</body>
</html>
