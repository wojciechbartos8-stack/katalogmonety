<?php
require_once "db_connect.php";

// kontynenty
$kontynenty = mysqli_query($conn, "SELECT * FROM kontynenty ORDER BY nazwa_kontynentu ASC");

// państwa (wszystkie na raz, żeby nie robić zapytań w pętli)
$panstwa = mysqli_query($conn, "SELECT * FROM panstwo ORDER BY nazwa_panstwa ASC");

$panstwa_tab = [];
while ($p = mysqli_fetch_assoc($panstwa)) {
    $panstwa_tab[$p['id_kontynent']][] = $p;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
<meta charset="UTF-8">
<title>Katalog Monet - Kontynenty</title>

<style>
body{
    font-family: Arial;
    background:#f4f4f4;
}

.kontynent{
    margin:15px auto;
    width:80%;
    background:white;
    border-radius:8px;
    overflow:hidden;
    box-shadow:0 2px 6px rgba(0,0,0,0.1);
}

.header{
    padding:15px;
    background:#1e88e5;
    color:white;
    font-size:20px;
    cursor:pointer;
    user-select:none;
}

.panstwa{
    display:none;
    padding:10px;
    background:#fafafa;
}

.panstwa a{
    display:block;
    padding:10px;
    text-decoration:none;
    color:#333;
    border-bottom:1px solid #ddd;
}

.panstwa a:hover{
    background:#e3f2fd;
}
</style>

</head>
<body>

<h1 style="text-align:center;">?? Katalog Monet</h1>

<?php while($k = mysqli_fetch_assoc($kontynenty)) { ?>

<div class="kontynent">

    <div class="header" onclick="toggle(<?= $k['id'] ?>)">
        ? <?= $k['nazwa_kontynentu'] ?>
    </div>

    <div class="panstwa" id="kont<?= $k['id'] ?>">

        <?php
        if(isset($panstwa_tab[$k['id']])) {
            foreach($panstwa_tab[$k['id']] as $p) {
                echo '<a href="panstwo.php?id='.$p['id'].'">'.$p['nazwa_panstwa'].'</a>';
            }
        } else {
            echo "<p>Brak państw</p>";
        }
        ?>

    </div>

</div>

<?php } ?>

<script>
function toggle(id){
    let el = document.getElementById("kont"+id);

    if(el.style.display === "block"){
        el.style.display = "none";
    } else {
        el.style.display = "block";
    }
}
</script>

</body>
</html>