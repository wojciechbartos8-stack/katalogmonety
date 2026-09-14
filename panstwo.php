<?php
declare(strict_types=1);

require_once "db_connect.php";

header("Content-Type: text/html; charset=UTF-8");

if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log(
        "Nie udało się ustawić kodowania utf8mb4: " .
        mysqli_error($conn)
    );
}

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    http_response_code(400);
    die("Nieprawidłowy identyfikator państwa.");
}


/* =========================================================
   POBRANIE PAŃSTWA
   ========================================================= */

$stmtPanstwo = mysqli_prepare(
    $conn,
    "SELECT
        id,
        id_kontynent,
        nazwa_panstwa,
        historia
     FROM panstwo
     WHERE id = ?
     LIMIT 1"
);

if ($stmtPanstwo === false) {
    http_response_code(500);
    die("Wystąpił błąd podczas pobierania danych państwa.");
}

mysqli_stmt_bind_param(
    $stmtPanstwo,
    "i",
    $id
);

mysqli_stmt_execute($stmtPanstwo);

$wynikPanstwo = mysqli_stmt_get_result($stmtPanstwo);

if (
    !$wynikPanstwo ||
    mysqli_num_rows($wynikPanstwo) === 0
) {
    http_response_code(404);
    die("Nie znaleziono państwa.");
}

$panstwo = mysqli_fetch_assoc($wynikPanstwo);


/* =========================================================
   POBRANIE MONET
   ========================================================= */

$stmtMonety = mysqli_prepare(
    $conn,
    "SELECT *
     FROM coin
     WHERE id_panstwo = ?
     ORDER BY rok_bicia DESC, id DESC"
);

if ($stmtMonety === false) {
    http_response_code(500);
    die("Wystąpił błąd podczas pobierania monet.");
}

mysqli_stmt_bind_param(
    $stmtMonety,
    "i",
    $id
);

mysqli_stmt_execute($stmtMonety);

$monety = mysqli_stmt_get_result($stmtMonety);

if ($monety === false) {
    http_response_code(500);
    die("Wystąpił błąd podczas pobierania monet.");
}


/* =========================================================
   FUNKCJE
   ========================================================= */

function bezpiecznyTekst(mixed $wartosc): string
{
    return htmlspecialchars(
        (string) $wartosc,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}


function przygotujNazwePola(
    string $klucz,
    array $nazwyPol
): string {

    if (isset($nazwyPol[$klucz])) {
        return $nazwyPol[$klucz];
    }

    return ucfirst(
        str_replace("_", " ", $klucz)
    );
}


/* =========================================================
   DANE PAŃSTWA
   ========================================================= */

$idPanstwa = (int) (
    $panstwo["id"] ?? $id
);

$idKontynentu = (int) (
    $panstwo["id_kontynent"] ?? 0
);

$nazwaPanstwa = trim(
    (string) (
        $panstwo["nazwa_panstwa"] ?? ""
    )
);

$nazwaPanstwaHtml = bezpiecznyTekst(
    $nazwaPanstwa
);


/* =========================================================
   SEO
   ========================================================= */

$tytulStrony =
    $nazwaPanstwa .
    " – monety i historia państwa";

$opisStrony =
    "Poznaj historię państwa " .
    $nazwaPanstwa .
    " oraz zobacz katalog monet pochodzących z tego kraju.";

$tytulStronyHtml = bezpiecznyTekst(
    $tytulStrony
);

$opisStronyHtml = bezpiecznyTekst(
    $opisStrony
);


/* =========================================================
   NAZWY PÓL MONETY
   ========================================================= */

$nazwyPol = [

    "waluta" =>
        "Waluta",

    "waluta_obiegowa" =>
        "Waluta obiegowa",

    "waluta_kolekcjonerska" =>
        "Waluta kolekcjonerska",

    "nominal" =>
        "Nominał",

    "rok_bicia" =>
        "Rok bicia",

    "historia_waluty" =>
        "Historia waluty",

    "historia_jednostki_monetarnej" =>
        "Historia jednostki monetarnej",

    "mennica" =>
        "Mennica",

    "material" =>
        "Materiał",

    "masa" =>
        "Masa",

    "waga" =>
        "Waga",

    "srednica" =>
        "Średnica",

    "grubosc" =>
        "Grubość",

    "naklad" =>
        "Nakład",

    "stan_zachowania" =>
        "Stan zachowania",

    "opis" =>
        "Opis",

    "awers" =>
        "Awers",

    "rewers" =>
        "Rewers",

    "rant" =>
        "Rant",

    "wartosc" =>
        "Wartość",

    "cena" =>
        "Cena",

    "data_dodania" =>
        "Data dodania"
];

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
    <?= $tytulStronyHtml ?>
    | Katalog Monet Świata
</title>

<meta
    name="description"
    content="<?= $opisStronyHtml ?>"
>

<meta
    name="robots"
    content="index, follow"
>

<meta
    name="theme-color"
    content="#17243d"
>

<meta
    property="og:locale"
    content="pl_PL"
>

<meta
    property="og:type"
    content="website"
>

<meta
    property="og:title"
    content="<?= $tytulStronyHtml ?>"
>

<meta
    property="og:description"
    content="<?= $opisStronyHtml ?>"
>

<meta
    property="og:site_name"
    content="Katalog Monet Świata"
>


<style>

*,
*::before,
*::after {
    box-sizing: border-box;
}


html {
    scroll-behavior: smooth;
    -webkit-text-size-adjust: 100%;
    text-size-adjust: 100%;
}


body {
    min-height: 100vh;
    min-height: 100svh;

    margin: 0;

    overflow-x: hidden;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    color: #222222;

    background: #f2f4f7;

    line-height: 1.6;
}


img {
    max-width: 100%;
}


/* =========================================================
   NAGŁÓWEK
   ========================================================= */

.site-header {

    padding: 20px;

    color: #ffffff;

    text-align: center;

    background:
        linear-gradient(
            135deg,
            #17243d,
            #244f79
        );

    box-shadow:
        0 3px 10px
        rgba(0, 0, 0, 0.18);
}


.site-header__inner {

    width: min(1100px, 100%);

    margin: 0 auto;
}


.site-header__title {

    margin: 0;

    color: #ffffff;

    font-size:
        clamp(
            1.35rem,
            4vw,
            2rem
        );

    line-height: 1.25;
}


/* =========================================================
   GŁÓWNA CZĘŚĆ
   ========================================================= */

.main-content {

    width:
        min(
            1100px,
            calc(100% - 30px)
        );

    margin: 0 auto;

    padding:
        28px 0 50px;
}


.page-title {

    margin:
        8px 0 26px;

    color: #1d3557;

    font-size:
        clamp(
            2rem,
            6vw,
            3rem
        );

    line-height: 1.15;

    text-align: center;

    overflow-wrap: anywhere;
}


/* =========================================================
   INFORMACJA O PAŃSTWIE
   ========================================================= */

.country-info {

    display: flex;

    flex-wrap: wrap;

    justify-content: center;

    gap: 15px;

    margin:
        0 auto 30px;
}


.country-info-box {

    min-width: 220px;

    padding: 15px 20px;

    background: #ffffff;

    border:
        1px solid #e0e5ea;

    border-radius: 10px;

    box-shadow:
        0 3px 12px
        rgba(0, 0, 0, 0.08);

    text-align: center;
}


.country-info-label {

    display: block;

    margin-bottom: 5px;

    color: #5f6368;

    font-size: 0.9rem;

    font-weight: 700;
}


.country-info-value {

    color: #1d3557;

    font-size: 1.15rem;

    font-weight: 700;
}


/* =========================================================
   BLOKI
   ========================================================= */

.content-box {

    width: 100%;

    margin:
        0 auto 35px;

    padding: 26px;

    background: #ffffff;

    border:
        1px solid #e0e5ea;

    border-radius: 12px;

    box-shadow:
        0 3px 14px
        rgba(0, 0, 0, 0.1);
}


.section-title {

    margin:
        0 0 18px;

    color: #244f79;

    font-size:
        clamp(
            1.4rem,
            4vw,
            2rem
        );

    line-height: 1.25;
}


.history {

    color: #303030;

    font-size: 1.05rem;

    overflow-wrap: anywhere;
}


.empty-message {

    margin: 0;

    padding: 16px;

    color: #5f6368;

    font-style: italic;

    background: #f7f8fa;

    border-left:
        4px solid #9aa5b1;

    border-radius: 6px;
}


/* =========================================================
   MONETY
   ========================================================= */

.coins-section {

    margin-top: 38px;
}


.coins-title {

    margin:
        0 0 25px;

    color: #1d3557;

    font-size:
        clamp(
            1.7rem,
            5vw,
            2.4rem
        );

    line-height: 1.25;

    text-align: center;
}


.coins-list {

    display: grid;

    grid-template-columns:
        repeat(
            auto-fit,
            minmax(
                min(100%, 340px),
                1fr
            )
        );

    gap: 22px;
}


.coin {

    min-width: 0;

    overflow: hidden;

    padding: 22px;

    background: #ffffff;

    border:
        1px solid #e0e5ea;

    border-radius: 12px;

    box-shadow:
        0 3px 12px
        rgba(0, 0, 0, 0.1);
}


.coin__title {

    margin:
        0 0 18px;

    color: #244f79;

    font-size: 1.35rem;

    line-height: 1.3;

    overflow-wrap: anywhere;
}


/* =========================================================
   INFORMACJA PAŃSTWO PRZY MONECIE
   ========================================================= */

.coin-country {

    margin:
        0 0 18px;

    padding: 13px 15px;

    background: #eef4f9;

    border-left:
        4px solid #244f79;

    border-radius: 7px;
}


.coin-country-label {

    display: block;

    margin-bottom: 3px;

    color: #52606d;

    font-size: 0.9rem;

    font-weight: 700;
}


.coin-country-value {

    color: #1d3557;

    font-size: 1.1rem;

    font-weight: 700;
}


/* =========================================================
   ZDJĘCIE MONETY
   ========================================================= */

.coin-image-wrapper {

    display: flex;

    justify-content: center;

    align-items: center;

    width: 100%;

    margin:
        0 0 18px;

    padding: 15px;

    background: #f7f9fb;

    border:
        1px solid #e1e6eb;

    border-radius: 10px;
}


.coin-image {

    display: block;

    width: auto;

    height: auto;

    max-width: 100%;

    max-height: 350px;

    object-fit: contain;

    border:
        1px solid #dce3e8;

    border-radius: 10px;

    box-shadow:
        0 4px 14px
        rgba(0, 0, 0, 0.15);
}


/* =========================================================
   POLA MONETY
   ========================================================= */

.field {

    padding: 11px 0;

    border-bottom:
        1px solid #eeeeee;

    overflow-wrap: anywhere;
}


.field:last-child {

    padding-bottom: 0;

    border-bottom: none;
}


.field-name {

    display: block;

    margin-bottom: 4px;

    color: #3f4852;

    font-weight: 700;
}


.field-value {

    color: #222222;

    white-space: normal;

    overflow-wrap: anywhere;
}


/* =========================================================
   POWRÓT
   ========================================================= */

.back {

    display: flex;

    align-items: center;

    justify-content: center;

    width: fit-content;

    min-width: 190px;

    min-height: 48px;

    margin:
        38px auto 0;

    padding:
        12px 22px;

    color: #ffffff;

    font-weight: 700;

    text-align: center;

    text-decoration: none;

    background: #1e88e5;

    border-radius: 7px;
}


.back:hover {

    background: #1565c0;
}


/* =========================================================
   STOPKA
   ========================================================= */

.site-footer {

    padding:
        24px 15px;

    color: #dce8f5;

    text-align: center;

    background: #102a43;
}


.site-footer p {

    margin: 0;
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 768px) {

    .main-content {

        width:
            calc(100% - 24px);
    }


    .content-box,
    .coin {

        padding: 20px;
    }


    .coin-image {

        max-height: 310px;
    }
}


/* =========================================================
   TELEFON
   ========================================================= */

@media (max-width: 600px) {

    .site-header {

        padding:
            16px 12px;
    }


    .main-content {

        width:
            calc(100% - 16px);

        padding:
            20px 0 38px;
    }


    .page-title {

        margin-bottom: 20px;
    }


    .content-box,
    .coin {

        padding: 16px;

        border-radius: 9px;
    }


    .coins-list {

        grid-template-columns: 1fr;

        gap: 16px;
    }


    .coin-image-wrapper {

        padding: 10px;

        margin-bottom: 15px;
    }


    .coin-image {

        max-height: 280px;
    }


    .country-info {

        flex-direction: column;
    }


    .country-info-box {

        width: 100%;
    }


    .back {

        width: 100%;

        margin-top: 28px;
    }
}


/* =========================================================
   MAŁY TELEFON
   ========================================================= */

@media (max-width: 400px) {

    .main-content {

        width:
            calc(100% - 10px);
    }


    .content-box,
    .coin {

        padding: 13px;
    }


    .coin-image {

        max-height: 240px;
    }
}


/* =========================================================
   REDUKCJA ANIMACJI
   ========================================================= */

@media (prefers-reduced-motion: reduce) {

    html {

        scroll-behavior: auto;
    }


    * {

        transition: none !important;
    }
}

</style>

</head>


<body>


<header class="site-header">

    <div class="site-header__inner">

        <p class="site-header__title">
            Katalog Monet Świata
        </p>

    </div>

</header>


<main class="main-content">


    <!-- =====================================================
         NAZWA PAŃSTWA
         ===================================================== -->

    <h1 class="page-title">

        <?= $nazwaPanstwaHtml ?>

    </h1>


    <!-- =====================================================
         INFORMACJE O PAŃSTWIE
         ===================================================== -->

    <div class="country-info">


        <div class="country-info-box">

            <span class="country-info-label">
                ID państwa
            </span>

            <span class="country-info-value">

                <?= $idPanstwa ?>

                |

                <?= $nazwaPanstwaHtml ?>

            </span>

        </div>


        <div class="country-info-box">

            <span class="country-info-label">
                ID kontynentu
            </span>

            <span class="country-info-value">

                <?= $idKontynentu ?>

            </span>

        </div>


    </div>


    <!-- =====================================================
         HISTORIA PAŃSTWA
         ===================================================== -->

    <section
        class="content-box"
        aria-labelledby="historia-title"
    >

        <h2
            class="section-title"
            id="historia-title"
        >

            Historia państwa

        </h2>


        <div class="history">

            <?php

            $historia = trim(
                (string) (
                    $panstwo["historia"] ?? ""
                )
            );

            ?>


            <?php if ($historia !== ""): ?>

                <?= nl2br(
                    bezpiecznyTekst($historia)
                ) ?>

            <?php else: ?>

                <p class="empty-message">

                    Brak opisu historii tego państwa.

                </p>

            <?php endif; ?>

        </div>

    </section>


    <!-- =====================================================
         MONETY
         ===================================================== -->

    <section
        class="coins-section"
        aria-labelledby="monety-title"
    >

        <h2
            class="coins-title"
            id="monety-title"
        >

            Monety

        </h2>


        <?php if (mysqli_num_rows($monety) > 0): ?>


            <div class="coins-list">


                <?php while (
                    $moneta = mysqli_fetch_assoc($monety)
                ): ?>


                    <?php

                    if (
                        isset($moneta["nazwa"]) &&
                        trim(
                            (string)
                            $moneta["nazwa"]
                        ) !== ""
                    ) {

                        $tytulMonety =
                            trim(
                                (string)
                                $moneta["nazwa"]
                            );

                    } elseif (
                        isset($moneta["nominal"]) &&
                        trim(
                            (string)
                            $moneta["nominal"]
                        ) !== ""
                    ) {

                        $tytulMonety =
                            "Moneta " .
                            trim(
                                (string)
                                $moneta["nominal"]
                            );

                    } else {

                        $tytulMonety =
                            "Moneta numer " .
                            (int)
                            $moneta["id"];
                    }

                    ?>


                    <article class="coin">


                        <h3 class="coin__title">

                            <?= bezpiecznyTekst(
                                $tytulMonety
                            ) ?>

                        </h3>


                        <!-- =================================================
                             PAŃSTWO MONETY
                             ================================================= -->

                        <div class="coin-country">

                            <span class="coin-country-label">

                                Państwo

                            </span>


                            <div class="coin-country-value">

                                <?= (int)
                                    $moneta["id_panstwo"]
                                ?>

                                |

                                <?= $nazwaPanstwaHtml ?>

                            </div>

                        </div>


                        <!-- =================================================
                             POZOSTAŁE POLA MONETY
                             ================================================= -->

                        <?php foreach (
                            $moneta as $klucz => $wartosc
                        ): ?>


                            <?php

                            /*
                             * Tych pól nie wyświetlamy
                             * drugi raz.
                             */

                            if (
                                $klucz === "id" ||
                                $klucz === "id_panstwo" ||
                                $klucz === "nazwa"
                            ) {

                                continue;
                            }


                            /* =============================================
                               ZDJĘCIE
                               ============================================= */

                            if ($klucz === "zdjecie") {


                                $sciezkaZdjecia =
                                    trim(
                                        (string)
                                        ($wartosc ?? "")
                                    );


                                if (
                                    $sciezkaZdjecia !== ""
                                ):

                                ?>

                                    <div
                                        class="coin-image-wrapper"
                                    >

                                        <img
                                            class="coin-image"
                                            src="<?= bezpiecznyTekst(
                                                $sciezkaZdjecia
                                            ) ?>"
                                            alt="Moneta z państwa <?= $nazwaPanstwaHtml ?>"
                                            loading="lazy"
                                            decoding="async"
                                        >

                                    </div>

                                <?php

                                endif;

                                continue;
                            }

                            ?>


                            <?php

                            $nazwaPola =
                                przygotujNazwePola(
                                    (string) $klucz,
                                    $nazwyPol
                                );


                            $wartoscTekstowa =
                                trim(
                                    (string)
                                    ($wartosc ?? "")
                                );

                            ?>


                            <div class="field">


                                <span class="field-name">

                                    <?= bezpiecznyTekst(
                                        $nazwaPola
                                    ) ?>:

                                </span>


                                <div class="field-value">


                                    <?php if (
                                        $wartoscTekstowa !== ""
                                    ): ?>

                                        <?= nl2br(
                                            bezpiecznyTekst(
                                                $wartoscTekstowa
                                            )
                                        ) ?>

                                    <?php else: ?>

                                        Brak danych

                                    <?php endif; ?>


                                </div>


                            </div>


                        <?php endforeach; ?>


                    </article>


                <?php endwhile; ?>


            </div>


        <?php else: ?>


            <div class="content-box">

                <p class="empty-message">

                    Nie dodano jeszcze monet
                    dla tego państwa.

                </p>

            </div>


        <?php endif; ?>


    </section>


    <!-- =====================================================
         POWRÓT
         ===================================================== -->

    <a
        class="back"
        href="kontynenty.php"
        aria-label="Powrót do listy kontynentów i państw"
    >

        &larr;
        Powrót do katalogu

    </a>


</main>


<footer class="site-footer">

    <p>

        &copy;
        <?= date("Y") ?>

        Katalog Monet Świata.
        Wszystkie prawa zastrzeżone.

    </p>

</footer>


</body>

</html>


<?php

mysqli_free_result(
    $wynikPanstwo
);

mysqli_free_result(
    $monety
);

mysqli_stmt_close(
    $stmtPanstwo
);

mysqli_stmt_close(
    $stmtMonety
);

mysqli_close(
    $conn
);

?>