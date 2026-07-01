<?php
session_start();
$user_role = $_SESSION['rola'] ?? 'guest';
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Kontakt</title>
<style>
body { font-family: Arial, sans-serif; margin:0; padding:0; }
#header { background:#333; color:white; padding:10px; display:flex; gap:15px; }
#header a { color:white; text-decoration:none; }
#content { padding:20px; }
input, textarea { width:100%; padding:8px; margin:5px 0; }
button { padding:8px 15px; margin-top:10px; cursor:pointer; }
</style>
</head>
<body>

<!-- Nag��wek -->
<div id="header">
  <a href="index.php">Strona główna</a>
  <a href="about.php">O mnie</a>
  <?php if($user_role==='guest'): ?>
    <a href="register.php">Rejestracja</a>
    <a href="login.php">Logowanie</a>
  <?php elseif($user_role==='user'): ?>
    <a href="user_panel.php">Panel Usera</a>
  <?php elseif($user_role==='admin'): ?>
    <a href="admin_panel.php">Panel Admina</a>
  <?php endif; ?>
</div>

<!-- Treść -->
<div id="content">
<h1>Kontakt</h1>
<p>Masz pytania lub sugestie? Skorzystaj z poni�szego formularza:</p>

<form method="post" action="contact_submit.php">
  <label>Imi�/Nazwa:</label>
  <input type="text" name="name" required>

  <label>Email:</label>
  <input type="email" name="email" required>

  <label>Treść wiadomości:</label>
  <textarea name="message" rows="5" required></textarea>

  <button type="submit">Wyślij wiadomość</button>
</form>
</div>

</body>
</html>
