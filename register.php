<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $password = $_POST['password'] ?? '';
    $rola = 'user'; // rejestracja zawsze jako user
    $wojewodztwo = $_POST['wojewodztwo'] ?? '';
    $miasto = $_POST['miasto'] ?? '';

    // Sprawdzenie czy login już istnieje
    $stmt = $conn->prepare("SELECT id FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $error = "Taki login już istnieje!";
    } else {

        // Wstawienie nowego użytkownika
        $stmt = $conn->prepare("
            INSERT INTO users (login, password, rola, wojewodztwo, miasto)
            VALUES (?, ?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "sssss",
            $login,
            $password,
            $rola,
            $wojewodztwo,
            $miasto
        );

        if ($stmt->execute()) {
            $_SESSION['login'] = $login;
            $_SESSION['rola'] = $rola;

            header("Location: user_panel.php");
            exit;
        } else {
            $error = "Błąd rejestracji!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Rejestracja użytkownika</title>
</head>
<body>

<h2>Rejestracja</h2>

<?php
if (isset($error)) {
    echo "<p style='color:red;'>$error</p>";
}
?>

<form method="post">

    Login:
    <input type="text" name="login" required>
    <br><br>

    Hasło:
    <input type="password" name="password" required>
    <br><br>

    Województwo (opcjonalne):
    <select name="wojewodztwo">
        <option value="">-- wybierz --</option>
        <option value="Dolnośląskie">Dolnośląskie</option>
        <option value="Mazowieckie">Mazowieckie</option>
        <option value="Zachodniopomorskie">Zachodniopomorskie</option>
    </select>
    <br><br>

    Miasto (opcjonalne):
    <input type="text" name="miasto">
    <br><br>

    <button type="submit">Zarejestruj</button>

</form>

<p><a href="index.php">Powrót do strony głównej</a></p>

</body>
</html>