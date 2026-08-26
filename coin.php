<?php

declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "db_connect.php";

/* =========================================
   KODOWANIE UTF-8
========================================= */

header("Content-Type: text/html; charset=UTF-8");

mysqli_set_charset($conn, "utf8mb4");

/* =========================================
   SPRAWDZENIE ID MONETY
========================================= */

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id <= 0) {
    http_response_code(400);
    die("Nieprawidłowy identyfikator monety.");
}

/* =========================================
   POBRANIE MONETY
========================================= */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        id_panstwo,
        waluta,
        `waluta obiegowa`,
        `waluta kolekcjonerska`,
        nominal,
        rok_bicia,
        historia_waluty,
        `historia_jednostki _monetarnej`,
        zdjecie
     FROM coin
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $id
);

mysqli_stmt_execute($stmt);

$res = mysqli_stmt_get_result($stmt);

$c = mysqli_fetch_assoc($res);

if (!$c) {

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

    http_response_code(404);

    die("Nie znaleziono monety.");
}

/* =========================================
   FUNKCJA BEZPIECZNEGO WYŚWIETLANIA
========================================= */

function e(mixed $value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}

/* =========================================
   POLA MONETY
========================================= */

$polaMonety = [

    "waluta" =>
        "Waluta",

    "waluta obiegowa" =>
        "Waluta obiegowa",

    "waluta kolekcjonerska" =>
        "Waluta kolekcjonerska",

    "nominal" =>
        "Nominał",

    "rok_bicia" =>
        "Rok bicia",

    "historia_waluty" =>
        "Historia waluty",

    "historia_jednostki _monetarnej" =>
        "Historia jednostki monetarnej"
];

/* =========================================
   ŚCIEŻKA DO ZDJĘCIA
========================================= */

$zdjecie = trim(
    (string) ($c["zdjecie"] ?? "")
);

/* =========================================
   DANE SEO
========================================= */

$nominal = trim(
    (string) ($c["nominal"] ?? "")
);

$waluta = trim(
    (string) ($c["waluta"] ?? "")
);

$rokBicia = trim(
    (string) ($c["rok_bicia"] ?? "")
);

$tytulMonety = "Moneta";

if ($nominal !== "") {
    $tytulMonety .= " " . $nominal;
}

if ($waluta !== "") {
    $tytulMonety .= " " . $waluta;
}

if ($rokBicia !== "") {
    $tytulMonety .= " z " . $rokBicia . " roku";
}

$metaDescription =
    "Szczegółowe informacje o monecie";

if ($nominal !== "") {
    $metaDescription .= " o nominale " . $nominal;
}

if ($waluta !== "") {
    $metaDescription .= " " . $waluta;
}

if ($rokBicia !== "") {
    $metaDescription .= " z " . $rokBicia . " roku";
}

$metaDescription .=
    ". Informacje o walucie, nominale, roku bicia i historii jednostki monetarnej.";

?>
<!DOCTYPE html>

<html lang="pl">

<head>

    <meta charset="UTF-8">

    <meta
        http-equiv="Content-Type"
        content="text/html; charset=UTF-8"
    >

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?= e($tytulMonety) ?> – Katalog Monet Świata
    </title>

    <meta
        name="description"
        content="<?= e($metaDescription) ?>"
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
        content="article"
    >

    <meta
        property="og:title"
        content="<?= e($tytulMonety) ?> – Katalog Monet Świata"
    >

    <meta
        property="og:description"
        content="<?= e($metaDescription) ?>"
    >

    <meta
        property="og:site_name"
        content="Katalog Monet Świata"
    >

    <style>

        /* =========================================
           RESET
        ========================================= */

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

        /* =========================================
           NAGŁÓWEK
        ========================================= */

        .site-header {
            width: 100%;

            padding: 18px 20px;

            color: #ffffff;

            text-align: center;

            background:
                linear-gradient(
                    135deg,
                    #17243d,
                    #244f79
                );

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.2);
        }

        .site-header h2 {
            margin: 0;

            color: #ffffff;

            font-size:
                clamp(
                    1.3rem,
                    4vw,
                    2rem
                );

            line-height: 1.25;

            overflow-wrap: anywhere;
        }

        /* =========================================
           GŁÓWNA TREŚĆ
        ========================================= */

        main {
            width:
                min(
                    1100px,
                    calc(100% - 30px)
                );

            margin:
                0
                auto
                45px;

            padding:
                28px
                0
                50px;
        }

        .container {
            width: 100%;

            padding: 30px;

            overflow: hidden;

            background: #ffffff;

            border: 1px solid #e1e5e9;

            border-radius: 14px;

            box-shadow:
                0 4px 18px
                rgba(0, 0, 0, 0.12);
        }

        h1 {
            margin:
                0
                0
                28px;

            color: #1d3557;

            text-align: center;

            font-size:
                clamp(
                    1.8rem,
                    5vw,
                    2.7rem
                );

            line-height: 1.2;

            overflow-wrap: anywhere;
        }

        /* =========================================
           ZDJĘCIE MONETY
        ========================================= */

        .coin-image-wrapper {
            display: flex;

            justify-content: flex-start;

            align-items: center;

            width: 100%;

            margin:
                0
                0
                30px;
        }

        .coin-image {
            display: block;

            width: auto;

            height: auto;

            max-width: 100%;

            max-height: 520px;

            object-fit: contain;

            border:
                1px solid
                #d8dee5;

            border-radius: 12px;

            box-shadow:
                0 5px 18px
                rgba(0, 0, 0, 0.18);
        }

        .no-image {
            width: 100%;

            margin-bottom: 25px;

            padding: 18px;

            color: #666666;

            text-align: center;

            font-style: italic;

            background: #f7f7f7;

            border:
                1px dashed
                #c8c8c8;

            border-radius: 8px;
        }

        /* =========================================
           POLA MONETY
        ========================================= */

        .field {
            width: 100%;

            padding: 15px 0;

            border-bottom:
                1px solid
                #e5e7eb;

            overflow-wrap: anywhere;
        }

        .field:last-child {
            border-bottom: none;
        }

        .field-name {
            display: block;

            margin-bottom: 5px;

            color: #244f79;

            font-size: 1rem;

            font-weight: 700;

            overflow-wrap: anywhere;
        }

        .field-value {
            width: 100%;

            color: #333333;

            font-size: 1rem;

            overflow-wrap: anywhere;

            word-break: normal;
        }

        .long-text {
            margin-top: 8px;

            padding: 18px;

            background: #f8fafc;

            border-left:
                4px solid
                #1e88e5;

            border-radius: 6px;

            overflow-wrap: anywhere;
        }

        .empty {
            color: #777777;

            font-style: italic;
        }

        /* =========================================
           PRZYCISK POWROTU
        ========================================= */

        .back {
            display: flex;

            justify-content: center;

            align-items: center;

            width: fit-content;

            min-width: 180px;

            min-height: 48px;

            margin:
                25px
                auto
                0;

            padding:
                12px
                22px;

            color: #ffffff;

            font-weight: 700;

            text-align: center;

            text-decoration: none;

            background: #1e88e5;

            border-radius: 7px;

            transition:
                background-color 0.2s ease,
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .back:hover {
            background: #1565c0;

            transform:
                translateY(-2px);

            box-shadow:
                0 6px 15px
                rgba(0, 0, 0, 0.18);
        }

        .back:focus-visible {
            outline:
                3px solid
                #ffca28;

            outline-offset: 3px;
        }

        /* =========================================
           TABLETY
        ========================================= */

        @media (max-width: 768px) {

            main {
                width:
                    calc(
                        100% - 24px
                    );

                margin:
                    0
                    auto
                    35px;

                padding:
                    22px
                    0
                    35px;
            }

            .container {
                padding:
                    24px
                    20px;
            }

            .coin-image {
                max-height: 450px;
            }

            .long-text {
                padding: 16px;
            }
        }

        /* =========================================
           TELEFONY
        ========================================= */

        @media (max-width: 600px) {

            .site-header {
                padding:
                    15px
                    12px;
            }

            main {
                width:
                    calc(
                        100% - 16px
                    );

                margin:
                    0
                    auto
                    30px;

                padding:
                    16px
                    0
                    30px;
            }

            .container {
                padding:
                    20px
                    15px;

                border-radius: 10px;
            }

            h1 {
                margin-bottom: 22px;
            }

            .coin-image-wrapper {
                justify-content: flex-start;

                margin-bottom: 22px;
            }

            .coin-image {
                width: auto;

                max-width: 100%;

                max-height: 380px;

                border-radius: 9px;
            }

            .field {
                padding:
                    13px
                    0;
            }

            .field-name,
            .field-value {
                font-size: 0.97rem;
            }

            .long-text {
                padding: 14px;

                border-left-width: 3px;
            }

            .back {
                width: 100%;

                margin-top: 22px;
            }
        }

        /* =========================================
           MAŁE TELEFONY
        ========================================= */

        @media (max-width: 400px) {

            main {
                width:
                    calc(
                        100% - 10px
                    );
            }

            .container {
                padding:
                    17px
                    12px;
            }

            .site-header h2 {
                font-size: 1.2rem;
            }

            h1 {
                font-size: 1.65rem;
            }

            .coin-image {
                max-height: 310px;
            }

            .long-text {
                padding: 12px;
            }
        }

        /* =========================================
           BARDZO MAŁE EKRANY
        ========================================= */

        @media (max-width: 330px) {

            .container {
                padding:
                    14px
                    10px;
            }

            h1 {
                font-size: 1.5rem;
            }

            .field-name,
            .field-value {
                font-size: 0.93rem;
            }
        }

        /* =========================================
           OGRANICZENIE ANIMACJI
        ========================================= */

        @media (
            prefers-reduced-motion: reduce
        ) {

            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition: none !important;
            }
        }

    </style>

</head>

<body>

<header class="site-header">

    <h2>
        &#128176; Katalog Monet Świata
    </h2>

</header>

<main>

    <article class="container">

        <h1>
            Moneta ID:
            <?= (int) $c["id"] ?>
        </h1>

        <!-- =====================================
             ZDJĘCIE MONETY
        ====================================== -->

        <?php if ($zdjecie !== ""): ?>

            <div class="coin-image-wrapper">

                <img
                    class="coin-image"
                    src="<?= e($zdjecie) ?>"
                    alt="Zdjęcie monety <?= e($tytulMonety) ?>"
                    loading="lazy"
                    decoding="async"
                >

            </div>

        <?php else: ?>

            <div class="no-image">

                Brak zdjęcia monety.

            </div>

        <?php endif; ?>

        <!-- =====================================
             INFORMACJE O MONECIE
        ====================================== -->

        <?php foreach (
            $polaMonety as $nazwaKolumny => $etykieta
        ): ?>

            <?php

            $wartosc =
                $c[$nazwaKolumny] ?? "";

            $czyDlugiTekst =
                in_array(
                    $nazwaKolumny,
                    [
                        "historia_waluty",
                        "historia_jednostki _monetarnej"
                    ],
                    true
                );

            ?>

            <div class="field">

                <span class="field-name">

                    <?= e($etykieta) ?>:

                </span>

                <div
                    class="
                        field-value
                        <?= $czyDlugiTekst
                            ? "long-text"
                            : ""
                        ?>
                    "
                >

                    <?php if (
                        $wartosc !== null &&
                        trim(
                            (string) $wartosc
                        ) !== ""
                    ): ?>

                        <?= nl2br(
                            e($wartosc)
                        ) ?>

                    <?php else: ?>

                        <span class="empty">

                            Brak danych

                        </span>

                    <?php endif; ?>

                </div>

            </div>

        <?php endforeach; ?>

    </article>

    <a
        class="back"
        href="javascript:history.back()"
    >
        &larr; Powrót
    </a>

</main>

</body>

</html>

<?php

mysqli_free_result($res);

mysqli_stmt_close($stmt);

mysqli_close($conn);

?>