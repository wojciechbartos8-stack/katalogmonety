<?php
session_start();

// czyszczenie sesji
$_SESSION = [];

// niszczenie sesji
session_destroy();

// przekierowanie
header("Location: index.php?logout=1");
exit();
?>