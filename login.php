<?php
session_start();
require_once 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $login = $_POST['login'] ?? '';
    $haslo = $_POST['haslo'] ?? '';

    // pobranie usera
    $stmt = $conn->prepare("SELECT login, password, rola FROM users WHERE login = ?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $user = $result->fetch_assoc()) {

        // sprawdzenie hasła
        if ($user['password'] === $haslo) {

            $_SESSION['login'] = $user['login'];
            $_SESSION['rola'] = $user['rola'];

            // 🔥 PRZEKIEROWANIE PO ROLI
            if ($user['rola'] === 'admin') {
                header("Location: admin_panel.php");
            } else {
                header("Location: user_panel.php");
            }
            exit;

        } else {
            $error = "Nieprawidłowy login lub hasło";
        }

    } else {
        $error = "Nieprawidłowy login lub hasło";
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Logowanie</title>
</head>
<body>

<h2>Logowanie</h2>

<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="post">
    <label>Login:</label><br>
    <input type="text" name="login" required><br><br>

    <label>Hasło:</label><br>
    <input type="password" name="haslo" required><br><br>

    <button type="submit">Zaloguj</button>
</form>

</body>
</html>