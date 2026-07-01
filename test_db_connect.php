<?php
include "db_connect.php";

echo "<h3>Test po³±czenia z baz±:</h3>";

$result = mysqli_query($conn, "SELECT DATABASE() AS db");
$row = mysqli_fetch_assoc($result);
echo "<p>? Po³±czono z baz±: <b>" . $row['db'] . "</b></p>";

echo "<h4>Tabele w tej bazie:</h4>";
$tables = mysqli_query($conn, "SHOW TABLES");

while ($t = mysqli_fetch_array($tables)) {
    echo "- " . $t[0] . "<br>";
}

echo "<h4>Podgl±d tabeli users:</h4>";
$q = mysqli_query($conn, "SELECT * FROM users LIMIT 5");
if (!$q) {
    die("<b>B³±d przy odczycie tabeli:</b> " . mysqli_error($conn));
}

while ($r = mysqli_fetch_assoc($q)) {
    echo "<pre>";
    print_r($r);
    echo "</pre>";
}
?>
