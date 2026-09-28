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
| BEZPIECZNE WYŚWIETLANIE
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
| OBSŁUGA ŚCIEŻKI ZDJĘCIA
|--------------------------------------------------------------------------
*/

function urlZdjecia($sciezka)
{
    if (
        $sciezka === null ||
        trim((string)$sciezka) === ''
    ) {
        return null;
    }

    $sciezka = rawurldecode(
        (string)$sciezka
    );

    $sciezka = str_replace(
        '\\',
        '/',
        $sciezka
    );

    $sciezka = trim($sciezka);


    /*
    |--------------------------------------------------------------------------
    | USUNIĘCIE NADMIAROWEGO katalogmonety/
    |--------------------------------------------------------------------------
    */

    $sciezka = preg_replace(
        '~^/?katalogmonety/~i',
        '',
        $sciezka
    );


    $sciezka = ltrim(
        $sciezka,
        '/'
    );


    /*
    |--------------------------------------------------------------------------
    | ZABEZPIECZENIE ŚCIEŻKI
    |--------------------------------------------------------------------------
    */

    $fragmenty = explode(
        '/',
        $sciezka
    );

    $bezpieczneFragmenty = [];


    foreach ($fragmenty as $fragment) {

        if (
            $fragment === '' ||
            $fragment === '.'
        ) {
            continue;
        }


        if ($fragment === '..') {
            return null;
        }


        $bezpieczneFragmenty[] =
            rawurlencode($fragment);

    }


    if (empty($bezpieczneFragmenty)) {
        return null;
    }


    return '/katalogmonety/' .
        implode(
            '/',
            $bezpieczneFragmenty
        );
}


/*
|--------------------------------------------------------------------------
| WYSZUKIWANIE
|--------------------------------------------------------------------------
*/

$szukaj = trim(
    $_GET['szukaj'] ?? ''
);


/*
|--------------------------------------------------------------------------
| POBIERANIE MONET
|--------------------------------------------------------------------------
*/

$monety = [];


try {

    if ($szukaj !== '') {

        $fraza = '%' .
            $szukaj .
            '%';


        /*
        |--------------------------------------------------------------------------
        | WYSZUKIWANIE W AKTUALNYCH POLACH
        |--------------------------------------------------------------------------
        */

        $stmt = $conn->prepare(
            "
            SELECT
                c.`id`,
                c.`id_panstwo`,
                p.`id_kontynent`,
                p.`nazwa_panstwa`,
                k.`nazwa_kontynentu`,
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

            WHERE

                c.`nazwa waluty` LIKE ?

                OR c.`jednostka monetarna` LIKE ?

                OR c.`nominał` LIKE ?

                OR c.`podział jednostki monetarnej` LIKE ?

                OR c.`rodzaj monety` LIKE ?

                OR CAST(
                    c.`rok_bicia`
                    AS CHAR
                ) LIKE ?

                OR p.`nazwa_panstwa` LIKE ?

                OR k.`nazwa_kontynentu` LIKE ?

                OR CAST(
                    c.`id_panstwo`
                    AS CHAR
                ) LIKE ?

            ORDER BY
                c.`id` DESC
            "
        );


        $stmt->bind_param(
            'ssssssss',
            $fraza,
            $fraza,
            $fraza,
            $fraza,
            $fraza,
            $fraza,
            $fraza,
            $fraza
        );


        $stmt->execute();


        $result =
            $stmt->get_result();


        $monety =
            $result->fetch_all(
                MYSQLI_ASSOC
            );


        $stmt->close();


    } else {


        /*
        |--------------------------------------------------------------------------
        | WSZYSTKIE MONETY
        |--------------------------------------------------------------------------
        */

        $result = $conn->query(
            "
            SELECT
                c.`id`,
                c.`id_panstwo`,
                p.`id_kontynent`,
                p.`nazwa_panstwa`,
                k.`nazwa_kontynentu`,
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
            "
        );


        $monety =
            $result->fetch_all(
                MYSQLI_ASSOC
            );

    }


} catch (mysqli_sql_exception $e) {

    die(
        'Błąd SQL: ' .
        h($e->getMessage())
    );

}


$liczbaMonet =
    count($monety);

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
    Katalog monet
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


/*
|--------------------------------------------------------------------------
| BODY
|--------------------------------------------------------------------------
*/

body {

    margin: 0;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    color: #263238;

    background: #eef2f7;

}


/*
|--------------------------------------------------------------------------
| HEADER
|--------------------------------------------------------------------------
*/

header {

    color: #ffffff;

    background:
        linear-gradient(
            135deg,
            #0b2148,
            #1f3b73
        );

}


.header-inner {

    width: 100%;

    max-width: 1300px;

    margin: 0 auto;

    padding: 24px 20px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    flex-wrap: wrap;

}


.brand h1 {

    margin: 0;

    font-size: 29px;

}


.brand p {

    margin: 6px 0 0;

    color: #d9e3f3;

}


.header-buttons {

    display: flex;

    gap: 10px;

    flex-wrap: wrap;

}


/*
|--------------------------------------------------------------------------
| PRZYCISKI
|--------------------------------------------------------------------------
*/

.button {

    display: inline-flex;

    min-height: 42px;

    padding: 10px 17px;

    align-items: center;

    justify-content: center;

    border: none;

    border-radius: 7px;

    color: #ffffff;

    background: #1e88e5;

    font-size: 15px;

    font-weight: 700;

    text-decoration: none;

    cursor: pointer;

}


.button:hover {

    background: #1565c0;

}


.button-secondary {

    background: #546e7a;

}


.button-secondary:hover {

    background: #37474f;

}


/*
|--------------------------------------------------------------------------
| KONTENER
|--------------------------------------------------------------------------
*/

.container {

    width: 100%;

    max-width: 1300px;

    margin: 0 auto;

    padding: 30px 20px;

}


/*
|--------------------------------------------------------------------------
| WYSZUKIWARKA
|--------------------------------------------------------------------------
*/

.search-box {

    margin-bottom: 25px;

    padding: 20px;

    background: #ffffff;

    border-radius: 12px;

    box-shadow:
        0 5px 18px
        rgba(0, 0, 0, 0.08);

}


.search-form {

    display: flex;

    gap: 10px;

}


.search-form input {

    width: 100%;

    min-height: 44px;

    padding: 11px 14px;

    border:
        1px solid #c7d0da;

    border-radius: 7px;

    font-family: inherit;

    font-size: 16px;

}


.search-form input:focus {

    outline: none;

    border-color: #1e88e5;

    box-shadow:
        0 0 0 3px
        rgba(
            30,
            136,
            229,
            0.14
        );

}


.results-info {

    margin: 15px 0 0;

    color: #607d8b;

}


/*
|--------------------------------------------------------------------------
| SIATKA MONET
|--------------------------------------------------------------------------
*/

.coins-grid {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fill,
            minmax(
                320px,
                1fr
            )
        );

    gap: 22px;

}


/*
|--------------------------------------------------------------------------
| KARTA MONETY
|--------------------------------------------------------------------------
*/

.coin-card {

    overflow: hidden;

    background: #ffffff;

    border-radius: 13px;

    box-shadow:
        0 6px 20px
        rgba(
            0,
            0,
            0,
            0.09
        );

    transition:
        transform 0.2s,
        box-shadow 0.2s;

}


.coin-card:hover {

    transform:
        translateY(-3px);

    box-shadow:
        0 10px 25px
        rgba(
            0,
            0,
            0,
            0.13
        );

}


/*
|--------------------------------------------------------------------------
| ZDJĘCIE
|--------------------------------------------------------------------------
*/

.coin-photo {

    position: relative;

    width: 100%;

    height: 280px;

    overflow: hidden;

    background: #f1f4f7;

}


.coin-photo img {

    display: block;

    width: 100%;

    height: 100%;

    object-fit: contain;

    background: #ffffff;

}


.no-image,
.image-error {

    width: 100%;

    height: 100%;

    padding: 20px;

    align-items: center;

    justify-content: center;

    text-align: center;

}


.no-image {

    display: flex;

    color: #78909c;

}


.image-error {

    display: none;

    position: absolute;

    inset: 0;

    color: #b71c1c;

    background: #ffebee;

}


/*
|--------------------------------------------------------------------------
| TREŚĆ KARTY
|--------------------------------------------------------------------------
*/

.coin-content {

    padding: 20px;

}


.coin-title {

    margin:
        0 0 15px;

    color: #1f3b73;

    font-size: 23px;

}


/*
|--------------------------------------------------------------------------
| WIERSZE INFORMACJI
|--------------------------------------------------------------------------
*/

.coin-row {

    display: grid;

    grid-template-columns:
        155px 1fr;

    gap: 10px;

    padding: 9px 0;

    border-bottom:
        1px solid #edf0f2;

}


.coin-label {

    color: #607d8b;

    font-weight: 700;

}


.coin-value {

    overflow-wrap: anywhere;

}


/*
|--------------------------------------------------------------------------
| HISTORIA / INFORMACJE
|--------------------------------------------------------------------------
*/

.history {

    margin-top: 18px;

    padding-top: 15px;

    border-top:
        1px solid #e5e9ed;

}


.history h3 {

    margin:
        0 0 8px;

    color: #1f3b73;

    font-size: 17px;

}


.history p {

    margin:
        0 0 16px;

    color: #455a64;

    line-height: 1.6;

}


/*
|--------------------------------------------------------------------------
| BRAK WYNIKÓW
|--------------------------------------------------------------------------
*/

.empty {

    padding: 50px 20px;

    color: #607d8b;

    background: #ffffff;

    border-radius: 12px;

    text-align: center;

    box-shadow:
        0 5px 18px
        rgba(
            0,
            0,
            0,
            0.08
        );

}


.empty h2 {

    color: #1f3b73;

}


/*
|--------------------------------------------------------------------------
| STOPKA
|--------------------------------------------------------------------------
*/

footer {

    margin-top: 40px;

    padding: 20px;

    color: #d9e3f3;

    background: #0b2148;

    text-align: center;

}


/*
|--------------------------------------------------------------------------
| RESPONSYWNOŚĆ
|--------------------------------------------------------------------------
*/

@media (max-width: 700px) {

    .header-inner {

        align-items: stretch;

        flex-direction: column;

    }


    .header-buttons {

        flex-direction: column;

    }


    .button {

        width: 100%;

    }


    .container {

        padding:
            20px 12px;

    }


    .search-form {

        flex-direction: column;

    }


    .coins-grid {

        grid-template-columns: 1fr;

    }


    .coin-row {

        grid-template-columns: 1fr;

        gap: 4px;

    }

}

</style>

</head>


<body>


<header>

<div class="header-inner">


<div class="brand">

<h1>
    Katalog monet
</h1>

<p>
    Przeglądaj monety zapisane w kolekcji
</p>

</div>


<div class="header-buttons">


<a
    href="user_panel.php"
    class="button"
>
    Wszystkie monety
</a>


<a
    href="admin_panel.php"
    class="button"
>
    Panel administratora
</a>


<a
    href="logout.php"
    class="button button-secondary"
>
    Wyloguj
</a>


</div>

</div>

</header>


<main class="container">


<!-- ==========================================================
     WYSZUKIWARKA
========================================================== -->

<section class="search-box">


<form
    method="get"
    action="user_panel.php"
    class="search-form"
>


<input
    type="search"
    name="szukaj"
    value="<?= h($szukaj) ?>"
    placeholder="Szukaj po państwie, kontynencie, walucie, nominale, roku..."
>


<button
    type="submit"
    class="button"
>
    Szukaj
</button>


<?php if ($szukaj !== ''): ?>

<a
    href="user_panel.php"
    class="button button-secondary"
>
    Wyczyść
</a>

<?php endif; ?>


</form>


<p class="results-info">


<?php if ($szukaj !== ''): ?>

Znaleziono:

<strong>
    <?= $liczbaMonet ?>
</strong>

dla zapytania:

<strong>
    „<?= h($szukaj) ?>”
</strong>


<?php else: ?>

Liczba monet:

<strong>
    <?= $liczbaMonet ?>
</strong>


<?php endif; ?>


</p>


</section>


<!-- ==========================================================
     BRAK MONET
========================================================== -->

<?php if (empty($monety)): ?>


<section class="empty">


<h2>
    Nie znaleziono monet
</h2>


<p>
    W katalogu nie ma monet
    spełniających podane kryteria.
</p>


</section>


<?php else: ?>


<!-- ==========================================================
     MONETY
========================================================== -->

<section class="coins-grid">


<?php foreach ($monety as $moneta): ?>


<?php

$zdjecie = urlZdjecia(
    $moneta['zdjęcie'] ?? null
);

?>


<article class="coin-card">


<!-- ======================================================
     ZDJĘCIE
======================================================= -->

<div class="coin-photo">


<?php if ($zdjecie !== null): ?>


<img
    src="<?= h($zdjecie) ?>"
    alt="Zdjęcie monety <?= h(
        $moneta['nominał']
    ) ?>"
    loading="lazy"
    onerror="
        this.style.display='none';
        this.nextElementSibling.style.display='flex';
    "
>


<div class="image-error">

Nie udało się
wyświetlić zdjęcia.

</div>


<?php else: ?>


<div class="no-image">

Brak zdjęcia monety

</div>


<?php endif; ?>


</div>


<!-- ======================================================
     TREŚĆ
======================================================= -->

<div class="coin-content">


<h2 class="coin-title">

<?= h(
    $moneta['nominał']
) ?>

–

<?= h(
    $moneta['nazwa waluty']
) ?>

</h2>


<!-- PAŃSTWO -->

<div class="coin-row">


<div class="coin-label">

Państwo:

</div>


<div class="coin-value">

<strong>

<?= h(
    $moneta['nazwa_panstwa']
    ?? 'Brak państwa'
) ?>

</strong>


<br>


<span class="small">

ID państwa:
<?= h(
    $moneta['id_panstwo']
) ?>

</span>


</div>


</div>


<!-- KONTYNENT -->

<div class="coin-row">


<div class="coin-label">

Kontynent:

</div>


<div class="coin-value">

<?= h(
    $moneta['nazwa_kontynentu']
    ?? 'Brak kontynentu'
) ?>


<br>


<span class="small">

ID kontynentu:
<?= h(
    $moneta['id_kontynent']
) ?>

</span>


</div>


</div>


<!-- WALUTA -->

<div class="coin-row">


<div class="coin-label">

Nazwa waluty:

</div>


<div class="coin-value">

<?= h(
    $moneta['nazwa waluty']
) ?>

</div>


</div>


<!-- JEDNOSTKA -->

<div class="coin-row">


<div class="coin-label">

Jednostka monetarna:

</div>


<div class="coin-value">

<?= h(
    $moneta['jednostka monetarna']
) ?>

</div>


</div>


<!-- NOMINAŁ -->

<div class="coin-row">


<div class="coin-label">

Nominał:

</div>


<div class="coin-value">

<?= h(
    $moneta['nominał']
) ?>

</div>


</div>


<!-- PODZIAŁ -->

<div class="coin-row">


<div class="coin-label">

Podział jednostki:

</div>


<div class="coin-value">

<?= h(
    $moneta[
        'podział jednostki monetarnej'
    ]
) ?>

</div>


</div>


<!-- RODZAJ -->

<div class="coin-row">


<div class="coin-label">

Rodzaj monety:

</div>


<div class="coin-value">

<?= h(
    $moneta['rodzaj monety']
) ?>

</div>


</div>


<!-- ROK -->

<div class="coin-row">


<div class="coin-label">

Rok bicia:

</div>


<div class="coin-value">

<?= h(
    $moneta['rok_bicia']
) ?>

</div>


</div>


</div>


</article>


<?php endforeach; ?>


</section>


<?php endif; ?>


</main>


<footer>

Katalog monet

</footer>


</body>

</html>