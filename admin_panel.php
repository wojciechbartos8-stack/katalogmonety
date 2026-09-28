<?php

session_start();

/*
|--------------------------------------------------------------------------
| ZABEZPIECZENIE PANELU
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| POŁĄCZENIE Z BAZĄ DANYCH
|--------------------------------------------------------------------------
*/

mysqli_report(
    MYSQLI_REPORT_ERROR |
    MYSQLI_REPORT_STRICT
);

try {

    $conn = new mysqli(
        'localhost',
        'root',
        'mysql',
        'katalogmonety'
    );

    $conn->set_charset('utf8mb4');

} catch (mysqli_sql_exception $e) {

    die('Nie udało się połączyć z bazą danych.');

}


/*
|--------------------------------------------------------------------------
| FUNKCJA BEZPIECZNEGO WYŚWIETLANIA
|--------------------------------------------------------------------------
*/

function h($tekst)
{
    return htmlspecialchars(
        (string)($tekst ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| ZMIENNE
|--------------------------------------------------------------------------
*/

$komunikat = '';
$blad = '';


/*
|--------------------------------------------------------------------------
| DODAWANIE PAŃSTWA
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['dodaj_panstwo'])) {

    $idKontynent = (int)($_POST['id_kontynent'] ?? 0);

    $nazwaPanstwa = trim(
        $_POST['nazwa_panstwa'] ?? ''
    );

    $historia = trim(
        $_POST['historia'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | WALIDACJA
    |--------------------------------------------------------------------------
    */

    if ($idKontynent <= 0) {

        $blad = 'Wybierz kontynent.';

    } elseif ($nazwaPanstwa === '') {

        $blad = 'Podaj nazwę państwa.';

    } elseif (mb_strlen($historia) > 255) {

        $blad = 'Historia może mieć maksymalnie 255 znaków.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | SPRAWDZENIE KONTYNENTU
        |--------------------------------------------------------------------------
        */

        $sqlSprawdzKontynent = "
            SELECT `id`
            FROM `kontynenty`
            WHERE `id` = ?
            LIMIT 1
        ";

        $stmt = $conn->prepare(
            $sqlSprawdzKontynent
        );

        $stmt->bind_param(
            'i',
            $idKontynent
        );

        $stmt->execute();

        $wynik = $stmt->get_result();

        if ($wynik->num_rows === 0) {

            $blad = 'Wybrany kontynent nie istnieje.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | DODANIE PAŃSTWA
            |--------------------------------------------------------------------------
            */

            $sqlDodajPanstwo = "
                INSERT INTO `panstwo`
                (
                    `id_kontynent`,
                    `nazwa_panstwa`,
                    `historia`
                )
                VALUES (?, ?, ?)
            ";

            $stmt = $conn->prepare(
                $sqlDodajPanstwo
            );

            $stmt->bind_param(
                'iss',
                $idKontynent,
                $nazwaPanstwa,
                $historia
            );

            $stmt->execute();

            $komunikat =
                'Państwo zostało dodane.';

        }

        $stmt->close();

    }

}


/*
|--------------------------------------------------------------------------
| DODAWANIE MONETY
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['dodaj_monete'])) {

    $idPanstwo = (int)($_POST['id_panstwo'] ?? 0);

    $nazwaWaluty = trim(
        $_POST['nazwa_waluty'] ?? ''
    );

    $jednostkaMonetarna = trim(
        $_POST['jednostka_monetarna'] ?? ''
    );

    $nominal = trim(
        $_POST['nominal'] ?? ''
    );

    $podzialJednostki = trim(
        $_POST['podzial_jednostki'] ?? ''
    );

    $zdjecie = trim(
        $_POST['zdjecie'] ?? ''
    );

    $rodzajMonety = trim(
        $_POST['rodzaj_monety'] ?? ''
    );

    $rokBicia = trim(
        $_POST['rok_bicia'] ?? ''
    );


    /*
    |--------------------------------------------------------------------------
    | WALIDACJA MONETY
    |--------------------------------------------------------------------------
    */

    if ($idPanstwo <= 0) {

        $blad = 'Wybierz państwo.';

    } elseif ($nazwaWaluty === '') {

        $blad = 'Podaj nazwę waluty.';

    } elseif ($jednostkaMonetarna === '') {

        $blad = 'Podaj jednostkę monetarną.';

    } elseif ($nominal === '') {

        $blad = 'Podaj nominał.';

    } elseif ($rokBicia === '') {

        $blad = 'Podaj rok bicia.';

    } else {

        /*
        |--------------------------------------------------------------------------
        | SPRAWDZENIE PAŃSTWA
        |--------------------------------------------------------------------------
        */

        $sqlSprawdzPanstwo = "
            SELECT `id`
            FROM `panstwo`
            WHERE `id` = ?
            LIMIT 1
        ";

        $stmt = $conn->prepare(
            $sqlSprawdzPanstwo
        );

        $stmt->bind_param(
            'i',
            $idPanstwo
        );

        $stmt->execute();

        $wynik = $stmt->get_result();

        if ($wynik->num_rows === 0) {

            $blad = 'Wybrane państwo nie istnieje.';

        } else {

            /*
            |--------------------------------------------------------------------------
            | DODANIE MONETY
            |--------------------------------------------------------------------------
            */

            $sqlDodajMonete = "
                INSERT INTO `coin`
                (
                    `id_panstwo`,
                    `nazwa waluty`,
                    `jednostka monetarna`,
                    `nominał`,
                    `podział jednostki monetarnej`,
                    `zdjęcie`,
                    `rodzaj monety`,
                    `rok_bicia`
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ";

            $stmt = $conn->prepare(
                $sqlDodajMonete
            );

            $stmt->bind_param(
                'isssssss',
                $idPanstwo,
                $nazwaWaluty,
                $jednostkaMonetarna,
                $nominal,
                $podzialJednostki,
                $zdjecie,
                $rodzajMonety,
                $rokBicia
            );

            $stmt->execute();

            $komunikat =
                'Moneta została dodana.';

        }

        $stmt->close();

    }

}


/*
|--------------------------------------------------------------------------
| POBIERANIE KONTYNENTÓW
|--------------------------------------------------------------------------
*/

$kontynenty = [];

$sqlKontynenty = "
    SELECT
        `id`,
        `nazwa_kontynentu`
    FROM `kontynenty`
    ORDER BY `nazwa_kontynentu` ASC
";

$resultKontynenty = $conn->query(
    $sqlKontynenty
);

while ($wiersz = $resultKontynenty->fetch_assoc()) {

    $kontynenty[] = $wiersz;

}


/*
|--------------------------------------------------------------------------
| POBIERANIE PAŃSTW
|--------------------------------------------------------------------------
*/

$panstwa = [];

$sqlPanstwa = "
    SELECT
        p.`id`,
        p.`id_kontynent`,
        k.`nazwa_kontynentu`,
        p.`nazwa_panstwa`,
        p.`historia`
    FROM `panstwo` AS p
    LEFT JOIN `kontynenty` AS k
        ON k.`id` = p.`id_kontynent`
    ORDER BY
        p.`nazwa_panstwa` ASC
";

$resultPanstwa = $conn->query(
    $sqlPanstwa
);

while ($wiersz = $resultPanstwa->fetch_assoc()) {

    $panstwa[] = $wiersz;

}


/*
|--------------------------------------------------------------------------
| POBIERANIE MONET
|--------------------------------------------------------------------------
*/

$monety = [];

$sqlMonety = "
    SELECT
        c.`id`,
        c.`id_panstwo`,
        p.`id_kontynent`,
        k.`nazwa_kontynentu`,
        p.`nazwa_panstwa`,
        c.`nazwa waluty`,
        c.`jednostka monetarna`,
        c.`nominał`,
        c.`podział jednostki monetarnej`,
        c.`zdjęcie`,
        c.`rodzaj monety`,
        c.`rok_bicia`
    FROM `coin` AS c
    LEFT JOIN `panstwo` AS p
        ON p.`id` = c.`id_panstwo`
    LEFT JOIN `kontynenty` AS k
        ON k.`id` = p.`id_kontynent`
    ORDER BY
        c.`id` DESC
";

$resultMonety = $conn->query(
    $sqlMonety
);

while ($wiersz = $resultMonety->fetch_assoc()) {

    $monety[] = $wiersz;

}


/*
|--------------------------------------------------------------------------
| STATYSTYKI
|--------------------------------------------------------------------------
*/

$liczbaKontynentow = 0;
$liczbaPanstw = 0;
$liczbaMonet = 0;


$result = $conn->query(
    "SELECT COUNT(*) AS liczba FROM `kontynenty`"
);

$row = $result->fetch_assoc();

$liczbaKontynentow = (int)$row['liczba'];


$result = $conn->query(
    "SELECT COUNT(*) AS liczba FROM `panstwo`"
);

$row = $result->fetch_assoc();

$liczbaPanstw = (int)$row['liczba'];


$result = $conn->query(
    "SELECT COUNT(*) AS liczba FROM `coin`"
);

$row = $result->fetch_assoc();

$liczbaMonet = (int)$row['liczba'];

?>

<!DOCTYPE html>

<html lang="pl">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    Panel administratora — Katalog Monet
</title>


<style>

/*
|--------------------------------------------------------------------------
| RESET
|--------------------------------------------------------------------------
*/

* {
    box-sizing: border-box;
}


body {

    margin: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #eef2f7;

    color: #222;

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

header {

    background: #0b2148;

    color: white;

    padding: 20px;

    box-shadow:
        0 2px 8px
        rgba(0, 0, 0, 0.2);

}


.header-inner {

    max-width: 1400px;

    margin: auto;

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;

    flex-wrap: wrap;

}


header h1 {

    margin: 0;

    font-size: 25px;

}


.header-links {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;

}


.header-links a {

    display: inline-block;

    color: white;

    text-decoration: none;

    background: #1f3b73;

    padding: 10px 15px;

    border-radius: 6px;

}


.header-links a:hover {

    background: #2d579e;

}


/*
|--------------------------------------------------------------------------
| GŁÓWNY KONTENER
|--------------------------------------------------------------------------
*/

.container {

    max-width: 1400px;

    margin: 30px auto;

    padding: 0 20px;

}


/*
|--------------------------------------------------------------------------
| KOMUNIKATY
|--------------------------------------------------------------------------
*/

.success {

    background: #dff5e5;

    color: #146c2e;

    border: 1px solid #9bd3a9;

    padding: 15px;

    border-radius: 8px;

    margin-bottom: 20px;

}


.error {

    background: #fde2e2;

    color: #9b1c1c;

    border: 1px solid #e3a1a1;

    padding: 15px;

    border-radius: 8px;

    margin-bottom: 20px;

}


/*
|--------------------------------------------------------------------------
| STATYSTYKI
|--------------------------------------------------------------------------
*/

.stats {

    display: grid;

    grid-template-columns:
        repeat(
            3,
            minmax(180px, 1fr)
        );

    gap: 20px;

    margin-bottom: 30px;

}


.stat {

    background: white;

    border-radius: 10px;

    padding: 22px;

    box-shadow:
        0 2px 10px
        rgba(0, 0, 0, 0.08);

}


.stat-number {

    display: block;

    font-size: 32px;

    font-weight: bold;

    color: #0b3d91;

    margin-bottom: 5px;

}


.stat-title {

    color: #666;

}


/*
|--------------------------------------------------------------------------
| SEKCJE
|--------------------------------------------------------------------------
*/

.section {

    background: white;

    border-radius: 10px;

    padding: 25px;

    margin-bottom: 30px;

    box-shadow:
        0 2px 10px
        rgba(0, 0, 0, 0.08);

}


.section h2 {

    margin-top: 0;

    color: #0b2148;

    border-bottom:
        2px solid #e5eaf1;

    padding-bottom: 12px;

}


/*
|--------------------------------------------------------------------------
| FORMULARZE
|--------------------------------------------------------------------------
*/

.form-grid {

    display: grid;

    grid-template-columns:
        repeat(
            2,
            minmax(250px, 1fr)
        );

    gap: 20px;

}


.form-group {

    display: flex;

    flex-direction: column;

}


.form-group.full {

    grid-column: 1 / -1;

}


label {

    font-weight: bold;

    margin-bottom: 7px;

    color: #333;

}


input,
select,
textarea {

    width: 100%;

    padding: 11px 12px;

    border:
        1px solid #cbd5e1;

    border-radius: 6px;

    font-size: 15px;

    font-family: inherit;

    background: white;

}


input:focus,
select:focus,
textarea:focus {

    outline: none;

    border-color: #1e88e5;

    box-shadow:
        0 0 0 2px
        rgba(30, 136, 229, 0.12);

}


textarea {

    min-height: 110px;

    resize: vertical;

}


button {

    border: none;

    background: #1e88e5;

    color: white;

    padding: 12px 20px;

    border-radius: 6px;

    cursor: pointer;

    font-size: 15px;

    font-weight: bold;

}


button:hover {

    background: #1565c0;

}


.form-button {

    margin-top: 5px;

}


/*
|--------------------------------------------------------------------------
| TABELA
|--------------------------------------------------------------------------
*/

.table-wrapper {

    width: 100%;

    overflow-x: auto;

}


table {

    width: 100%;

    border-collapse: collapse;

    min-width: 900px;

}


th {

    background: #0b2148;

    color: white;

    text-align: left;

    padding: 11px;

    white-space: nowrap;

}


td {

    padding: 10px;

    border-bottom:
        1px solid #e1e5eb;

    vertical-align: top;

}


tr:hover td {

    background: #f5f8fc;

}


.small {

    font-size: 12px;

    color: #666;

}


/*
|--------------------------------------------------------------------------
| ZDJĘCIA MONET
|--------------------------------------------------------------------------
*/

.coin-image {

    width: 70px;

    height: 70px;

    object-fit: contain;

    border:
        1px solid #ddd;

    border-radius: 6px;

    background: #fafafa;

}


/*
|--------------------------------------------------------------------------
| STOPKA
|--------------------------------------------------------------------------
*/

footer {

    background: #0b2148;

    color: white;

    text-align: center;

    padding: 20px;

    margin-top: 40px;

}


/*
|--------------------------------------------------------------------------
| RESPONSYWNOŚĆ
|--------------------------------------------------------------------------
*/

@media (max-width: 800px) {

    .stats {

        grid-template-columns: 1fr;

    }


    .form-grid {

        grid-template-columns: 1fr;

    }


    .form-group.full {

        grid-column: auto;

    }


    header h1 {

        font-size: 21px;

    }

}

</style>

</head>


<body>


<header>

<div class="header-inner">

<h1>
    Panel administratora — Katalog Monet
</h1>

<div class="header-links">

<a href="index.php">
    Strona główna
</a>

<a href="logout.php">
    Wyloguj
</a>

</div>

</div>

</header>


<main class="container">


<?php if ($komunikat !== ''): ?>

<div class="success">

<?= h($komunikat) ?>

</div>

<?php endif; ?>


<?php if ($blad !== ''): ?>

<div class="error">

<?= h($blad) ?>

</div>

<?php endif; ?>


<!-- ==========================================================
     STATYSTYKI
========================================================== -->

<div class="stats">


<div class="stat">

<span class="stat-number">
    <?= $liczbaKontynentow ?>
</span>

<span class="stat-title">
    Kontynenty
</span>

</div>


<div class="stat">

<span class="stat-number">
    <?= $liczbaPanstw ?>
</span>

<span class="stat-title">
    Państwa
</span>

</div>


<div class="stat">

<span class="stat-number">
    <?= $liczbaMonet ?>
</span>

<span class="stat-title">
    Monety
</span>

</div>


</div>


<!-- ==========================================================
     DODAJ PAŃSTWO
========================================================== -->

<section class="section">

<h2>
    Dodaj państwo
</h2>


<form
    method="post"
    action=""
>


<div class="form-grid">


<div class="form-group">

<label for="id_kontynent">
    Kontynent
</label>

<select
    name="id_kontynent"
    id="id_kontynent"
    required
>

<option value="">
    -- wybierz kontynent --
</option>


<?php foreach ($kontynenty as $kontynent): ?>

<option
    value="<?= h($kontynent['id']) ?>"
>

<?= h($kontynent['id']) ?>
—
<?= h($kontynent['nazwa_kontynentu']) ?>

</option>

<?php endforeach; ?>

</select>

</div>


<div class="form-group">

<label for="nazwa_panstwa">
    Nazwa państwa
</label>

<input
    type="text"
    name="nazwa_panstwa"
    id="nazwa_panstwa"
    maxlength="255"
    required
>

</div>


<div class="form-group full">

<label for="historia">
    Historia
</label>

<textarea
    name="historia"
    id="historia"
    maxlength="255"
    placeholder="Historia państwa — maksymalnie 255 znaków"
></textarea>

</div>


<div class="form-group full">

<button
    type="submit"
    name="dodaj_panstwo"
    class="form-button"
>
    + Dodaj państwo
</button>

</div>


</div>

</form>

</section>


<!-- ==========================================================
     DODAJ MONETĘ
========================================================== -->

<section class="section">

<h2>
    Dodaj monetę
</h2>


<form
    method="post"
    action=""
>


<div class="form-grid">


<div class="form-group full">

<label for="id_panstwo">
    Państwo
</label>

<select
    name="id_panstwo"
    id="id_panstwo"
    required
>

<option value="">
    -- wybierz państwo --
</option>


<?php foreach ($panstwa as $panstwo): ?>

<option
    value="<?= h($panstwo['id']) ?>"
>

<?= h($panstwo['id']) ?>
—
<?= h($panstwo['nazwa_panstwa']) ?>
—
<?= h($panstwo['nazwa_kontynentu'] ?? 'Brak kontynentu') ?>

</option>

<?php endforeach; ?>

</select>

</div>


<div class="form-group">

<label for="nazwa_waluty">
    Nazwa waluty
</label>

<input
    type="text"
    name="nazwa_waluty"
    id="nazwa_waluty"
    required
>

</div>


<div class="form-group">

<label for="jednostka_monetarna">
    Jednostka monetarna
</label>

<input
    type="text"
    name="jednostka_monetarna"
    id="jednostka_monetarna"
    required
>

</div>


<div class="form-group">

<label for="nominal">
    Nominał
</label>

<input
    type="text"
    name="nominal"
    id="nominal"
    required
>

</div>


<div class="form-group">

<label for="podzial_jednostki">
    Podział jednostki monetarnej
</label>

<input
    type="text"
    name="podzial_jednostki"
    id="podzial_jednostki"
>

</div>


<div class="form-group">

<label for="zdjecie">
    Zdjęcie
</label>

<input
    type="text"
    name="zdjecie"
    id="zdjecie"
    placeholder="np. images/monety/polska1.jpg"
>

</div>


<div class="form-group">

<label for="rodzaj_monety">
    Rodzaj monety
</label>

<input
    type="text"
    name="rodzaj_monety"
    id="rodzaj_monety"
    placeholder="np. obiegowa, kolekcjonerska"
>

</div>


<div class="form-group">

<label for="rok_bicia">
    Rok bicia
</label>

<input
    type="text"
    name="rok_bicia"
    id="rok_bicia"
    required
    placeholder="np. 1998"
>

</div>


<div class="form-group full">

<button
    type="submit"
    name="dodaj_monete"
    class="form-button"
>
    + Dodaj monetę
</button>

</div>


</div>

</form>

</section>


<!-- ==========================================================
     LISTA PAŃSTW
========================================================== -->

<section class="section">

<h2>
    Państwa w bazie
</h2>


<div class="table-wrapper">

<table>

<thead>

<tr>

<th>
    ID
</th>

<th>
    Kontynent
</th>

<th>
    ID kontynentu
</th>

<th>
    Państwo
</th>

<th>
    Historia
</th>

</tr>

</thead>


<tbody>


<?php if (count($panstwa) === 0): ?>

<tr>

<td colspan="5">
    Brak państw w bazie.
</td>

</tr>

<?php else: ?>


<?php foreach ($panstwa as $panstwo): ?>

<tr>

<td>
    <?= h($panstwo['id']) ?>
</td>


<td>

<?= h(
    $panstwo['nazwa_kontynentu']
    ?? 'Brak'
) ?>

</td>


<td>
    <?= h($panstwo['id_kontynent']) ?>
</td>


<td>

<strong>
    <?= h($panstwo['nazwa_panstwa']) ?>
</strong>

</td>


<td>

<?= h($panstwo['historia']) ?>

</td>

</tr>

<?php endforeach; ?>


<?php endif; ?>


</tbody>

</table>

</div>

</section>


<!-- ==========================================================
     LISTA MONET
========================================================== -->

<section class="section">

<h2>
    Monety w bazie
</h2>


<div class="table-wrapper">

<table>

<thead>

<tr>

<th>
    ID
</th>

<th>
    Państwo
</th>

<th>
    Kontynent
</th>

<th>
    Waluta
</th>

<th>
    Jednostka
</th>

<th>
    Nominał
</th>

<th>
    Podział
</th>

<th>
    Zdjęcie
</th>

<th>
    Rodzaj
</th>

<th>
    Rok
</th>

</tr>

</thead>


<tbody>


<?php if (count($monety) === 0): ?>

<tr>

<td colspan="10">
    Brak monet w bazie.
</td>

</tr>

<?php else: ?>


<?php foreach ($monety as $moneta): ?>

<tr>


<td>

<?= h($moneta['id']) ?>

</td>


<td>

<strong>

<?= h(
    $moneta['nazwa_panstwa']
    ?? 'Brak państwa'
) ?>

</strong>

<br>

<span class="small">

ID państwa:
<?= h($moneta['id_panstwo']) ?>

</span>

</td>


<td>

<?= h(
    $moneta['nazwa_kontynentu']
    ?? 'Brak kontynentu'
) ?>

<br>

<span class="small">

ID kontynentu:
<?= h($moneta['id_kontynent']) ?>

</span>

</td>


<td>

<?= h(
    $moneta['nazwa waluty']
) ?>

</td>


<td>

<?= h(
    $moneta['jednostka monetarna']
) ?>

</td>


<td>

<?= h(
    $moneta['nominał']
) ?>

</td>


<td>

<?= h(
    $moneta['podział jednostki monetarnej']
) ?>

</td>


<td>


<?php

$zdjecie = trim(
    (string)(
        $moneta['zdjęcie']
        ?? ''
    )
);

?>


<?php if ($zdjecie !== ''): ?>

<img
    src="<?= h($zdjecie) ?>"
    alt="<?= h(
        $moneta['nominał']
        . ' '
        . $moneta['nazwa waluty']
    ) ?>"
    class="coin-image"
>


<br>

<span class="small">

<?= h($zdjecie) ?>

</span>

<?php else: ?>

<span class="small">
    Brak zdjęcia
</span>

<?php endif; ?>


</td>


<td>

<?= h(
    $moneta['rodzaj monety']
) ?>

</td>


<td>

<?= h(
    $moneta['rok_bicia']
) ?>

</td>


</tr>

<?php endforeach; ?>


<?php endif; ?>


</tbody>

</table>

</div>

</section>


</main>


<footer>

Katalog Monet — Panel administratora

</footer>


</body>

</html>