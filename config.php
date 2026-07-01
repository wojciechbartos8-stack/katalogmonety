<?php
// config.php — ustawienia połączenia z Laragon MySQL

// Dane serwera
$host = '127.0.0.1';   // lub 'localhost'
$port = 3306;           // domyślny port MySQL Laragon

// Dane logowania
$user = 'root';         // Laragon domyślnie root
$pass = '';             // Laragon domyślnie brak hasła

// Nazwa bazy danych
$db = 'katalogmonety';

// Tworzenie połączenia
$mysqli = new mysqli($host, $user, $pass, $db, $port);

// Sprawdzenie połączenia
if ($mysqli->connect_errno) {
    die("Błąd połączenia z bazą danych: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error);
}

// Ustawienie kodowania UTF-8
$mysqli->set_charset("utf8mb4");

// Teraz można używać $mysqli do zapytań SQL w projekcie
?>
