<?php
session_start();
require_once "db_connect.php";

if (!isset($_SESSION['login']) || $_SESSION['rola'] != "admin") {
    header("Location: login.php");
    exit;
}

if (isset($_POST['dodaj'])) {

    $id_panstwo = intval($_POST['id_panstwo']);
    $waluta = trim($_POST['waluta']);
    $nominal = trim($_POST['nominal']);
    $rok = intval($_POST['rok_bicia']);

    $stmt = $conn->prepare("INSERT INTO coin (id_panstwo, waluta, `nominał`, rok_bicia) VALUES (?,?,?,?)");
    $stmt->bind_param("issi", $id_panstwo, $waluta, $nominal, $rok);
    $stmt->execute();
}

if (isset($_GET['usun'])) {
    $id = intval($_GET['usun']);
    $conn->query("DELETE FROM coin WHERE id=$id");
}

$panstwa = $conn->query("SELECT * FROM panstwo ORDER BY nazwa_panstwa");

$monety = $conn->query("
SELECT
coin.id,
coin.waluta,
coin.`nominał`,
coin.rok_bicia,
panstwo.nazwa_panstwa
FROM coin
LEFT JOIN panstwo
ON coin.id_panstwo=panstwo.id
ORDER BY panstwo.nazwa_panstwa, coin.waluta
");
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Panel Administratora</title>

<style>
body{font-family:Arial;background:#eee;margin:0;}
header{background:#333;color:#fff;padding:15px;text-align:center;}
.container{width:95%;margin:auto;}
.box{background:#fff;padding:20px;margin:20px 0;border-radius:8px;}
input,select{width:100%;padding:8px;margin:6px 0;}
button{padding:10px 20px;}
table{width:100%;border-collapse:collapse;}
th,td{border:1px solid #ccc;padding:8px;}
a{text-decoration:none;}
</style>

</head>

<body>

<header>
<h1>Panel Administratora</h1>

<p>
<a href="index.php" style="color:white;">🏠 Powrót do strony głównej</a> |
<a href="logout.php" style="color:white;">Wyloguj</a>
</p>

</header>

<div class="container">

<div class="box">

<h2>Dodaj monetę</h2>

<form method="post">

<label>Państwo</label>

<select name="id_panstwo">

<?php
while($p=$panstwa->fetch_assoc()){
echo "<option value='{$p['id']}'>{$p['nazwa_panstwa']}</option>";
}
?>

</select>

<label>Waluta</label>
<input type="text" name="waluta" required>

<label>Nominał</label>
<input type="text" name="nominal" required>

<label>Rok bicia</label>
<input type="number" name="rok_bicia" required>

<button name="dodaj">Dodaj monetę</button>

</form>

</div>

<div class="box">

<h2>Lista monet</h2>

<table>

<tr>
<th>ID</th>
<th>Państwo</th>
<th>Waluta</th>
<th>Nominał</th>
<th>Rok</th>
<th>Usuń</th>
</tr>
<?php
while($m = $monety->fetch_assoc()){
?>

<tr>

<td><?php echo $m['id']; ?></td>

<td><?php echo htmlspecialchars($m['nazwa_panstwa']); ?></td>

<td><?php echo htmlspecialchars($m['waluta']); ?></td>

<td><?php echo htmlspecialchars($m['nominał']); ?></td>

<td><?php echo $m['rok_bicia']; ?></td>

<td>
<a href="admin_panel.php?usun=<?php echo $m['id']; ?>"
onclick="return confirm('Usunąć monetę?')">
Usuń
</a>
</td>

</tr>

<?php
}
?>

</table>

</div>

</div>

</body>
</html>

<?php
$conn->close();
?>