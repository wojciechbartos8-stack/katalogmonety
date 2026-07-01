<?php
session_start();
$user_role = $_SESSION['rola'] ?? 'guest';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>O mnie</title>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; }
#header { background:#333; color:white; padding:10px; display:flex; gap:15px; }
#header a { color:white; text-decoration:none; }
#content { padding:20px; }
</style>
</head>
<body>

<!-- Nag��wek -->
<div id="header">
  <a href="index.php">Strona główna</a>
  <a href="contact.php">Kontakt</a>
  <?php if($user_role==='guest'): ?>
    <a href="register.php">Rejestracja</a>
    <a href="login.php">Logowanie</a>
  <?php elseif($user_role==='user'): ?>
    <a href="user_panel.php">Panel Usera</a>
  <?php elseif($user_role==='admin'): ?>
    <a href="admin_panel.php">Panel Admina</a>
  <?php endif; ?>
</div>

<!-- G��wna tre�� -->
<div id="content">
<h1>O mnie</h1>
<p>Witaj! Jestem twórcą tego serwisu, którego celem jest wygodne katalogowanie monet z różnych krajów i kontynentów. Strona pozwala przeglądać kolekcje, a po rejestracji lub zalogowaniu także dodawać nowe państwa i monety.</p>

<p>Serwis jest prosty w obsłudze i ma na celu uporządkowanie Twojej kolekcji w jednym miejscu. Administrator ma pełne uprawnienia do zarządzania strukturą kontynentów, państw i monet, natomiast zwykli użytkownicy mogą dodawać nowe państwa i monety do katalogu.</p>
</div>

</body>
</html>
