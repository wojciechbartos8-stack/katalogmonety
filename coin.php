<?php

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "db_connect.php";

mysqli_set_charset($conn, "utf8mb4");

/* =========================================
   SPRAWDZENIE ID MONETY
========================================= */

$id = isset($_GET["id"])
    ? (int) $_GET["id"]
    : 0;

if ($id <= 0) {
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
     WHERE id = ?"
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

    die("Nie znaleziono monety.");
}

/* =========================================
   FUNKCJA BEZPIECZNEGO WYŚWIETLANIA
========================================= */

function e($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

/* =========================================
   POLA MONETY
========================================= */

$polaMonety = [
    "waluta" => "Waluta",
    "waluta obiegowa" => "Waluta obiegowa",
    "waluta kolekcjonerska" => "Waluta kolekcjonerska",
    "nominal" => "Nominał",
    "rok_bicia" => "Rok bicia",
    "historia_waluty" => "Historia waluty",
    "historia_jednostki _monetarnej" =>
        "Historia jednostki monetarnej"
];

/* =========================================
   ŚCIEŻKA DO ZDJĘCIA
========================================= */

$zdjecie = trim(
    (string) ($c["zdjecie"] ?? "")
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
        Moneta #<?= (int) $c["id"] ?> – Katalog Monet Świata
    </title>

    <meta
        name="description"
        content="Szczegółowe informacje o monecie numer <?= (int) $c["id"] ?>: waluta, nominał, rok bicia oraz historia jednostki monetarnej."
    >

    <style>

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #222222;
            background: #f2f4f7;
            line-height: 1.6;
        }

        .site-header {
            padding: 18px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(
                135deg,
                #17243d,
                #244f79
            );
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.2);
        }

        .site-header h2 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(1.3rem, 4vw, 2rem);
        }

        main {
            width: min(900px, calc(100% - 30px));
            margin: 30px auto;
        }

        .container {
            padding: 30px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
        }

        h1 {
            margin: 0 0 28px;
            color: #1d3557;
            text-align: center;
            font-size: clamp(1.8rem, 5vw, 2.7rem);
        }

        /* =========================================
           ZDJĘCIE MONETY
        ========================================= */

        .coin-image-wrapper {
            display: flex;
            justify-content: center;
            margin: 0 0 30px;
        }

        .coin-image {
            display: block;
            width: auto;
            max-width: 100%;
            max-height: 520px;
            object-fit: contain;
            border-radius: 12px;
            border: 1px solid #d8dee5;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.18);
        }

        .no-image {
            margin-bottom: 25px;
            padding: 18px;
            color: #666666;
            text-align: center;
            font-style: italic;
            background: #f7f7f7;
            border: 1px dashed #c8c8c8;
            border-radius: 8px;
        }

        /* =========================================
           POLA MONETY
        ========================================= */

        .field {
            padding: 15px 0;
            border-bottom: 1px solid #e5e7eb;
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
        }

        .field-value {
            color: #333333;
            font-size: 1rem;
            overflow-wrap: anywhere;
        }

        .long-text {
            padding: 18px;
            background: #f8fafc;
            border-left: 4px solid #1e88e5;
            border-radius: 6px;
        }

        .empty {
            color: #777777;
            font-style: italic;
        }

        .back {
            display: block;
            width: fit-content;
            min-width: 180px;
            margin: 25px auto 0;
            padding: 12px 22px;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            background: #1e88e5;
            border-radius: 7px;
            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .back:hover {
            background: #1565c0;
            transform: translateY(-2px);
        }

        .back:focus-visible {
            outline: 3px solid #ffca28;
            outline-offset: 3px;
        }

        @media (max-width: 600px) {

            main {
                width: calc(100% - 20px);
                margin: 18px auto;
            }

            .container {
                padding: 20px 16px;
                border-radius: 10px;
            }

            .coin-image {
                max-height: 380px;
            }

            .field {
                padding: 13px 0;
            }

            .long-text {
                padding: 14px;
            }

            .back {
                width: 100%;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration: 0.01ms !important;
            }
        }

    </style>

</head>

<body>

<header class="site-header">

    <h2>?? Katalog Monet Świata</h2>

</header>

<main>

    <article class="container">

        <h1>
            ?? Moneta ID: <?= (int) $c["id"] ?>
        </h1>

        <!-- ZDJĘCIE MONETY -->

        <?php if ($zdjecie !== ""): ?>

            <div class="coin-image-wrapper">

                <img
                    class="coin-image"
                    src="<?= e($zdjecie) ?>"
                    alt="Zdjęcie monety ID <?= (int) $c["id"] ?>"
                    loading="lazy"
                >

            </div>

        <?php else: ?>

            <div class="no-image">
                Brak zdjęcia monety.
            </div>

        <?php endif; ?>

        <!-- INFORMACJE O MONECIE -->

        <?php foreach (
            $polaMonety as $nazwaKolumny => $etykieta
        ): ?>

            <?php

            $wartosc = $c[$nazwaKolumny] ?? "";

            $czyDlugiTekst = in_array(
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
                    class="field-value <?= $czyDlugiTekst
                        ? "long-text"
                        : "" ?>"
                >

                    <?php if (
                        $wartosc !== null &&
                        trim((string) $wartosc) !== ""
                    ): ?>

                        <?= nl2br(e($wartosc)) ?>

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
        ‹ Powrót
    </a>

</main>

</body>

</html>

<?php

mysqli_stmt_close($stmt);
mysqli_close($conn);

?>