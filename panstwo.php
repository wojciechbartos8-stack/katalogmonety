<?php

require_once "db_connect.php";

/*
|--------------------------------------------------------------------------
| Bezpieczne wyświetlanie tekstu
|--------------------------------------------------------------------------
*/

function bezpiecznyTekst($tekst)
{
    return htmlspecialchars(
        (string)$tekst,
        ENT_QUOTES,
        'UTF-8'
    );
}


/*
|--------------------------------------------------------------------------
| ID państwa
|--------------------------------------------------------------------------
*/

$idPanstwa = isset($_GET["id"])
    ? (int)$_GET["id"]
    : 0;

if ($idPanstwa <= 0) {

    die("Nieprawidłowe ID państwa.");

}


/*
|--------------------------------------------------------------------------
| Dane państwa
|--------------------------------------------------------------------------
*/

$sqlPanstwo = "
    SELECT
        id,
        id_kontynent,
        nazwa_panstwa,
        historia
    FROM panstwo
    WHERE id = ?
    LIMIT 1
";


$stmtPanstwo = mysqli_prepare(
    $conn,
    $sqlPanstwo
);


if (!$stmtPanstwo) {

    die(
        "Błąd przygotowania zapytania państwa: "
        . mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $stmtPanstwo,
    "i",
    $idPanstwa
);


mysqli_stmt_execute(
    $stmtPanstwo
);


mysqli_stmt_bind_result(
    $stmtPanstwo,
    $idPanstwaDb,
    $idKontynentu,
    $nazwaPanstwa,
    $historia
);


if (!mysqli_stmt_fetch($stmtPanstwo)) {

    mysqli_stmt_close(
        $stmtPanstwo
    );

    die("Nie znaleziono państwa.");

}


mysqli_stmt_close(
    $stmtPanstwo
);


/*
|--------------------------------------------------------------------------
| Monety
|--------------------------------------------------------------------------
*/

$sqlMonety = "
    SELECT
        id,
        id_panstwo,
        `nazwa waluty`,
        `jednostka monetarna`,
        `nominał`,
        `podział jednostki monetarnej`,
        `zdjęcie`,
        `rodzaj monety`,
        rok_bicia
    FROM coin
    WHERE id_panstwo = ?
    ORDER BY rok_bicia DESC, id DESC
";


$stmtMonety = mysqli_prepare(
    $conn,
    $sqlMonety
);


if (!$stmtMonety) {

    die(
        "Błąd przygotowania zapytania monet: "
        . mysqli_error($conn)
    );

}


mysqli_stmt_bind_param(
    $stmtMonety,
    "i",
    $idPanstwa
);


if (!mysqli_stmt_execute($stmtMonety)) {

    die(
        "Błąd wykonania zapytania monet: "
        . mysqli_stmt_error($stmtMonety)
    );

}


/*
|--------------------------------------------------------------------------
| Zmienne dla pól tabeli coin
|--------------------------------------------------------------------------
*/

$idMonety = null;

$idPanstwaMonety = null;

$nazwaWaluty = null;

$jednostkaMonetarna = null;

$nominal = null;

$podzialJednostki = null;

$zdjecie = null;

$rodzajMonety = null;

$rokBicia = null;


/*
|--------------------------------------------------------------------------
| Powiązanie wyników
|--------------------------------------------------------------------------
*/

mysqli_stmt_bind_result(
    $stmtMonety,
    $idMonety,
    $idPanstwaMonety,
    $nazwaWaluty,
    $jednostkaMonetarna,
    $nominal,
    $podzialJednostki,
    $zdjecie,
    $rodzajMonety,
    $rokBicia
);


/*
|--------------------------------------------------------------------------
| Tablica monet
|--------------------------------------------------------------------------
*/

$monety = [];


while (mysqli_stmt_fetch($stmtMonety)) {

    $monety[] = [

        "id" => $idMonety,

        "id_panstwo" => $idPanstwaMonety,

        "nazwa waluty" => $nazwaWaluty,

        "jednostka monetarna" => $jednostkaMonetarna,

        "nominał" => $nominal,

        "podział jednostki monetarnej" =>
            $podzialJednostki,

        "zdjęcie" => $zdjecie,

        "rodzaj monety" => $rodzajMonety,

        "rok_bicia" => $rokBicia

    ];

}


mysqli_stmt_close(
    $stmtMonety
);

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

    <?= bezpiecznyTekst($nazwaPanstwa) ?>

    – katalog monet

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

    background: #f1f4f7;

    color: #222;

}


/*
|--------------------------------------------------------------------------
| NAGŁÓWEK
|--------------------------------------------------------------------------
*/

header {

    background: #0b2148;

    color: white;

    padding: 25px 20px;

    text-align: center;

}


header h1 {

    margin: 0;

    font-size: 32px;

}


header p {

    margin: 8px 0 0;

    font-size: 16px;

    opacity: 0.9;

}


/*
|--------------------------------------------------------------------------
| GŁÓWNY KONTENER
|--------------------------------------------------------------------------
*/

.container {

    width: 95%;

    max-width: 1000px;

    margin: 30px auto;

}


/*
|--------------------------------------------------------------------------
| INFORMACJE O PAŃSTWIE
|--------------------------------------------------------------------------
*/

.country-box {

    background: white;

    padding: 25px;

    border-radius: 12px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.10);

    margin-bottom: 30px;

}


.country-info {

    display: flex;

    gap: 15px;

    flex-wrap: wrap;

    margin-bottom: 20px;

}


.info-item {

    background: #f4f7fa;

    border: 1px solid #dde4ea;

    padding: 14px;

    border-radius: 8px;

    min-width: 200px;

}


.info-label {

    display: block;

    font-weight: bold;

    color: #0b2148;

    margin-bottom: 5px;

}


.history {

    line-height: 1.7;

    white-space: pre-line;

}


/*
|--------------------------------------------------------------------------
| TYTUŁ MONET
|--------------------------------------------------------------------------
*/

.coins-title {

    color: #0b2148;

    font-size: 28px;

    margin-bottom: 20px;

}


/*
|--------------------------------------------------------------------------
| KARTA MONETY
|--------------------------------------------------------------------------
*/

.coin-card {

    background: white;

    padding: 25px;

    border-radius: 12px;

    margin-bottom: 30px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.10);

}


/*
|--------------------------------------------------------------------------
| DANE MONETY – PIONOWO
|--------------------------------------------------------------------------
*/

.coin-data {

    display: flex;

    flex-direction: column;

    gap: 0;

}


.coin-field {

    display: flex;

    flex-direction: column;

    padding: 13px 0;

    border-bottom:
        1px solid #e1e6eb;

}


.coin-field:first-child {

    padding-top: 0;

}


.coin-field-label {

    font-weight: bold;

    color: #0b2148;

    margin-bottom: 5px;

}


.coin-field-value {

    color: #222;

    line-height: 1.5;

    overflow-wrap: anywhere;

}


/*
|--------------------------------------------------------------------------
| ZDJĘCIE
|--------------------------------------------------------------------------
*/

.coin-image-section {

    margin-top: 20px;

    padding-top: 13px;

}


.coin-image-label {

    display: block;

    font-weight: bold;

    color: #0b2148;

    margin-bottom: 10px;

}


.coin-image-wrapper {

    display: flex;

    justify-content: center;

    align-items: center;

    width: 100%;

    margin: 0 0 15px;

    padding: 15px;

    background: #f7f9fb;

    border: 1px solid #e1e6eb;

    border-radius: 10px;

    overflow: visible;

}


.coin-image {

    display: block;

    width: auto;

    height: auto;

    max-width: 100%;

    object-fit: contain;

    border:
        1px solid #dce3e8;

    border-radius: 10px;

    box-shadow:
        0 4px 14px rgba(0,0,0,0.15);

}


/*
|--------------------------------------------------------------------------
| OPIS MONETY POD ZDJĘCIEM
|--------------------------------------------------------------------------
*/

.coin-description {

    padding: 13px 0;

    border-bottom:
        1px solid #e1e6eb;

}


.coin-description-label {

    display: block;

    font-weight: bold;

    color: #0b2148;

    margin-bottom: 5px;

}


.coin-description-value {

    color: #222;

    line-height: 1.5;

    overflow-wrap: anywhere;

}


/*
|--------------------------------------------------------------------------
| BRAK ZDJĘCIA
|--------------------------------------------------------------------------
*/

.no-image {

    padding: 20px;

    background: #f4f4f4;

    border:
        1px dashed #aaa;

    border-radius: 8px;

    color: #666;

}


/*
|--------------------------------------------------------------------------
| BRAK MONET
|--------------------------------------------------------------------------
*/

.no-coins {

    background: white;

    padding: 25px;

    border-radius: 12px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.10);

    color: #666;

}


/*
|--------------------------------------------------------------------------
| STOPKA
|--------------------------------------------------------------------------
*/

footer {

    margin-top: 40px;

    padding: 20px;

    background: #0b2148;

    color: white;

    text-align: center;

}


/*
|--------------------------------------------------------------------------
| TELEFON
|--------------------------------------------------------------------------
*/

@media (max-width: 600px) {

    header h1 {

        font-size: 25px;

    }


    .container {

        width: 94%;

    }


    .coin-card,
    .country-box {

        padding: 18px;

    }


    .coin-image-wrapper {

        padding: 8px;

    }

}

</style>

</head>


<body>


<!-- =========================================================
     NAGŁÓWEK
     ========================================================= -->

<header>

    <h1>

        <?= bezpiecznyTekst($nazwaPanstwa) ?>

    </h1>

    <p>

        Katalog monet

    </p>

</header>



<div class="container">


    <!-- =========================================================
         INFORMACJE O PAŃSTWIE
         ========================================================= -->

    <section class="country-box">


        <div class="country-info">


            <div class="info-item">

                <span class="info-label">

                    ID państwa

                </span>

                <?= bezpiecznyTekst($idPanstwaDb) ?>

            </div>



            <div class="info-item">

                <span class="info-label">

                    ID kontynentu

                </span>

                <?= bezpiecznyTekst($idKontynentu) ?>

            </div>


        </div>



        <div class="history">

            <strong>

                Historia państwa:

            </strong>

            <br><br>

            <?= nl2br(
                bezpiecznyTekst($historia)
            ) ?>

        </div>


    </section>



    <!-- =========================================================
         MONETY
         ========================================================= -->

    <h2 class="coins-title">

        Monety państwa

    </h2>



    <?php if (count($monety) === 0): ?>


        <div class="no-coins">

            Brak monet przypisanych do tego państwa.

        </div>


    <?php else: ?>


        <?php foreach ($monety as $moneta): ?>


            <?php

            /*
            |--------------------------------------------------------------------------
            | ŚCIEŻKA ZDJĘCIA
            |--------------------------------------------------------------------------
            */

            $sciezkaZdjecia =
                trim(
                    (string)$moneta["zdjęcie"]
                );


            $sciezkaZdjecia =
                str_replace(
                    "\\",
                    "/",
                    $sciezkaZdjecia
                );


            /*
            |--------------------------------------------------------------------------
            | Usunięcie początku:
            |
            | www/katalogmonety/
            |--------------------------------------------------------------------------
            */

            if (
                strpos(
                    $sciezkaZdjecia,
                    "www/katalogmonety/"
                ) === 0
            ) {

                $sciezkaZdjecia =
                    substr(
                        $sciezkaZdjecia,
                        strlen(
                            "www/katalogmonety/"
                        )
                    );

            }


            /*
            |--------------------------------------------------------------------------
            | Usunięcie początku:
            |
            | katalogmonety/
            |--------------------------------------------------------------------------
            */

            if (
                strpos(
                    $sciezkaZdjecia,
                    "katalogmonety/"
                ) === 0
            ) {

                $sciezkaZdjecia =
                    substr(
                        $sciezkaZdjecia,
                        strlen(
                            "katalogmonety/"
                        )
                    );

            }


            $sciezkaZdjecia =
                ltrim(
                    $sciezkaZdjecia,
                    "/"
                );


            /*
            |--------------------------------------------------------------------------
            | OPIS MONETY
            |
            | Opis zawiera wyłącznie nominał.
            | Nie jest tutaj ponownie dodawana nazwa waluty
            | ani jednostka monetarna.
            |--------------------------------------------------------------------------
            */

            $opisMonety =
                trim(
                    (string)$moneta["nominał"]
                );

            ?>


            <!-- =================================================
                 KARTA MONETY
                 ================================================= -->

            <article class="coin-card">


                <div class="coin-data">


                    <!-- =================================================
                         NAZWA WALUTY
                         ================================================= -->

                    <div class="coin-field">

                        <span class="coin-field-label">

                            Nazwa waluty

                        </span>


                        <span class="coin-field-value">

                            <?= bezpiecznyTekst(
                                $moneta["nazwa waluty"]
                            ) ?>

                        </span>

                    </div>



                    <!-- =================================================
                         JEDNOSTKA MONETARNA
                         ================================================= -->

                    <div class="coin-field">

                        <span class="coin-field-label">

                            Jednostka monetarna

                        </span>


                        <span class="coin-field-value">

                            <?= bezpiecznyTekst(
                                $moneta["jednostka monetarna"]
                            ) ?>

                        </span>

                    </div>



                    <!-- =================================================
                         NOMINAŁ
                         ================================================= -->

                    <div class="coin-field">

                        <span class="coin-field-label">

                            Nominał

                        </span>


                        <span class="coin-field-value">

                            <?= bezpiecznyTekst(
                                $moneta["nominał"]
                            ) ?>

                        </span>

                    </div>



                    <!-- =================================================
                         PODZIAŁ JEDNOSTKI MONETARNEJ
                         ================================================= -->

                    <div class="coin-field">

                        <span class="coin-field-label">

                            Podział jednostki monetarnej

                        </span>


                        <span class="coin-field-value">

                            <?= bezpiecznyTekst(
                                $moneta[
                                    "podział jednostki monetarnej"
                                ]
                            ) ?>

                        </span>

                    </div>



                    <!-- =================================================
                         RODZAJ MONETY
                         ================================================= -->

                    <div class="coin-field">

                        <span class="coin-field-label">

                            Rodzaj monety

                        </span>


                        <span class="coin-field-value">

                            <?= bezpiecznyTekst(
                                $moneta["rodzaj monety"]
                            ) ?>

                        </span>

                    </div>



                    <!-- =================================================
                         ZDJĘCIE
                         ================================================= -->

                    <div class="coin-image-section">


                        <span class="coin-image-label">

                            Zdjęcie

                        </span>



                        <?php if ($sciezkaZdjecia !== ""): ?>


                            <div class="coin-image-wrapper">


                                <img
                                    src="<?= bezpiecznyTekst(
                                        $sciezkaZdjecia
                                    ) ?>"
                                    alt="<?= bezpiecznyTekst(
                                        $opisMonety
                                    ) ?>"
                                    class="coin-image"
                                >


                            </div>


                        <?php else: ?>


                            <div class="no-image">

                                Brak zdjęcia monety.

                            </div>


                        <?php endif; ?>



                        <!-- =================================================
                             OPIS MONETY POD ZDJĘCIEM
                             ================================================= -->

                        <div class="coin-description">


                            <span class="coin-description-label">

                                Opis monety

                            </span>


                            <span class="coin-description-value">

                                <?= bezpiecznyTekst(
                                    $opisMonety
                                ) ?>

                            </span>


                        </div>


                    </div>



                    <!-- =================================================
                         ROK BICIA
                         ================================================= -->

                    <div class="coin-field">

                        <span class="coin-field-label">

                            Rok bicia

                        </span>


                        <span class="coin-field-value">

                            <?= bezpiecznyTekst(
                                $moneta["rok_bicia"]
                            ) ?>

                        </span>

                    </div>


                </div>


            </article>


        <?php endforeach; ?>


    <?php endif; ?>


</div>



<!-- =========================================================
     STOPKA
     ========================================================= -->

<footer>

    Katalog monet

</footer>


</body>

</html>
