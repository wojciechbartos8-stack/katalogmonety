<?php
require_once "db_connect.php";

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;


// pobranie państwa
$panstwo = mysqli_query($conn, "SELECT * FROM panstwo WHERE id=$id");

if(mysqli_num_rows($panstwo)==0){
    die("Nie znaleziono państwa");
}

$p = mysqli_fetch_assoc($panstwo);


// pobranie monet
$monety = mysqli_query(
    $conn,
    "SELECT * FROM coin 
     WHERE id_panstwo=$id 
     ORDER BY rok_bicia DESC"
);

?>

<!DOCTYPE html>
<html lang="pl">

<head>

<meta charset="UTF-8">

<title>
<?= htmlspecialchars($p['nazwa_panstwa']) ?>
</title>


<style>

body{
    font-family:Arial;
    background:#f2f2f2;
    margin:0;
}


header{

    background:#222;
    color:white;
    padding:15px;
    text-align:center;

}


h1{

    text-align:center;
    margin:25px;

}



.box{

    width:80%;
    margin:20px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,.2);

}



.coin{

    width:80%;
    margin:15px auto;
    background:white;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 6px rgba(0,0,0,.15);

}



.field{

    padding:5px;
    border-bottom:1px solid #eee;

}



h2{

    color:#333;

}


.back{

    display:block;
    width:150px;
    margin:20px auto;
    padding:10px;
    text-align:center;
    background:#1e88e5;
    color:white;
    text-decoration:none;
    border-radius:6px;

}


</style>


</head>


<body>


<header>

<h2>
?? Katalog Monet Świata
</h2>

</header>



<h1>

??? <?= htmlspecialchars($p['nazwa_panstwa']) ?>

</h1>



<!-- HISTORIA PAŃSTWA -->

<div class="box">


<h2>
?? Historia państwa
</h2>


<?php

if(isset($p['historia']) && $p['historia']!=""){

    echo nl2br(htmlspecialchars($p['historia']));

}
else{

    echo "Brak opisu historii państwa.";

}

?>


</div>





<h1>
?? Monety
</h1>



<?php

while($row=mysqli_fetch_assoc($monety))

{


?>



<div class="coin">


<h2>
?? Moneta ID: <?= $row['id'] ?>
</h2>



<?php


foreach($row as $key=>$value)

{


// pomijamy techniczne pola

if(
$key=="id" ||
$key=="id_panstwo"
)

continue;



// nazwa pola bardziej czytelna

$nazwa = ucwords(
str_replace("_"," ",$key)
);



echo "

<div class='field'>

<b>
$nazwa:
</b>
<br>

".nl2br(htmlspecialchars($value))."

</div>

";


}



?>



</div>



<?php

}



?>



<a class="back" href="kontynenty.php">
? Powrót
</a>



</body>

</html>