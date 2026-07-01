<?php
// test_db_connect.php
// Prosty test dzia³ania pliku db_connect.php

require_once 'db_connect.php';

echo "<h2>Test po³±czenia z baz± danych</h2>";

try {
    $conn = get_db_connection();
    echo "<p style='color:green;'>? Po³±czenie z baz± powiod³o siê.</p>";

    // Spróbujmy sprawdziæ, jakie tabele s± w bazie:
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "<p>Znalezione tabele:</p><ul>";
        while ($row = $result->fetch_array()) {
            echo "<li>" . htmlspecialchars($row[0]) . "</li>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color:orange;'>Brak tabel lub b³±d w zapytaniu.</p>";
    }

    $conn->close();
} catch (Exception $e) {
    echo "<p style='color:red;'>? B³±d: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
