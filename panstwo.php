<?php
declare(strict_types=1);

require_once "db_connect.php";

/*
 * Ustawienie kodowania odpowiedzi i połączenia z bazą.
 */
header("Content-Type: text/html; charset=UTF-8");

if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log(
        "Nie udało się ustawić kodowania utf8mb4: " .
        mysqli_error($conn)
    );
}

/*
 * Pobranie identyfikatora państwa.
 */
$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    http_response_code(400);
    die("Nieprawidłowy identyfikator państwa.");
}

/* =========================================
   POBRANIE PAŃSTWA
========================================= */

$stmtPanstwo = mysqli_prepare(
    $conn,
    "SELECT id, nazwa_panstwa, historia
     FROM panstwo
     WHERE id = ?
     LIMIT 1"
);

if ($stmtPanstwo === false) {
    error_log(
        "Błąd przygotowania zapytania o państwo: " .
        mysqli_error($conn)
    );

    http_response_code(500);
    die("Wystąpił błąd podczas pobierania danych państwa.");
}

mysqli_stmt_bind_param($stmtPanstwo, "i", $id);
mysqli_stmt_execute($stmtPanstwo);

$wynikPanstwo = mysqli_stmt_get_result($stmtPanstwo);

if ($wynikPanstwo === false) {
    http_response_code(500);
    die("Wystąpił błąd podczas pobierania danych państwa.");
}

if (mysqli_num_rows($wynikPanstwo) === 0) {
    http_response_code(404);
    die("Nie znaleziono państwa.");
}

$panstwo = mysqli_fetch_assoc($wynikPanstwo);

/* =========================================
   POBRANIE MONET PAŃSTWA
========================================= */

$stmtMonety = mysqli_prepare(
    $conn,
    "SELECT *
     FROM coin
     WHERE id_panstwo = ?
     ORDER BY rok_bicia DESC, id DESC"
);

if ($stmtMonety === false) {
    error_log(
        "Błąd przygotowania zapytania o monety: " .
        mysqli_error($conn)
    );

    http_response_code(500);
    die("Wystąpił błąd podczas pobierania monet.");
}

mysqli_stmt_bind_param($stmtMonety, "i", $id);
mysqli_stmt_execute($stmtMonety);

$monety = mysqli_stmt_get_result($stmtMonety);

if ($monety === false) {
    http_response_code(500);
    die("Wystąpił błąd podczas pobierania monet.");
}

/* =========================================
   DANE DO SEO
========================================= */

$nazwaPanstwa = trim((string) ($panstwo["nazwa_panstwa"] ?? ""));

$nazwaPanstwaHtml = htmlspecialchars(
    $nazwaPanstwa,
    ENT_QUOTES | ENT_SUBSTITUTE,
    "UTF-8"
);

$tytulStrony = $nazwaPanstwa . " – monety i historia państwa";

$opisStrony =
    "Poznaj historię państwa " .
    $nazwaPanstwa .
    " oraz zobacz katalog monet pochodzących z tego kraju.";

$tytulStronyHtml = htmlspecialchars(
    $tytulStrony,
    ENT_QUOTES | ENT_SUBSTITUTE,
    "UTF-8"
);

$opisStronyHtml = htmlspecialchars(
    $opisStrony,
    ENT_QUOTES | ENT_SUBSTITUTE,
    "UTF-8"
);

/*
 * Przyjazne nazwy kolumn.
 * Możesz dopisać kolejne nazwy zgodnie z kolumnami tabeli coin.
 */
$nazwyPol = [
    "nazwa"            => "Nazwa monety",
    "nominal"          => "Nominał",
    "waluta"           => "Waluta",
    "rok_bicia"        => "Rok bicia",
    "mennica"          => "Mennica",
    "material"         => "Materiał",
    "masa"             => "Masa",
    "waga"             => "Waga",
    "srednica"         => "Średnica",
    "grubosc"          => "Grubość",
    "naklad"           => "Nakład",
    "stan_zachowania"  => "Stan zachowania",
    "opis"             => "Opis",
    "awers"            => "Awers",
    "rewers"           => "Rewers",
    "rant"             => "Rant",
    "wartosc"          => "Wartość",
    "cena"             => "Cena",
    "data_dodania"     => "Data dodania"
];

/*
 * Funkcja tworząca czytelną nazwę pola.
 */
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

/*
 * Funkcja bezpiecznego wyświetlania tekstu.
 */
function bezpiecznyTekst(
    mixed $wartosc
): string {
    return htmlspecialchars(
        (string) $wartosc,
        ENT_QUOTES | ENT_SUBSTITUTE,
        "UTF-8"
    );
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title><?= $tytulStronyHtml ?> | Katalog Monet Świata</title>

    <meta
        name="description"
        content="<?= $opisStronyHtml ?>"
    >

    <meta
        name="keywords"
        content="monety <?= $nazwaPanstwaHtml ?>, monety świata, katalog monet, numizmatyka, historia <?= $nazwaPanstwaHtml ?>"
    >

    <meta name="author" content="Katalog Monet Świata">
    <meta name="robots" content="index, follow">

    <meta name="theme-color" content="#17243d">

    <meta property="og:locale" content="pl_PL">
    <meta property="og:type" content="article">

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

    <script type="application/ld+json">
    <?= json_encode(
        [
            "@context" => "https://schema.org",
            "@type" => "CollectionPage",
            "name" => $tytulStrony,
            "description" => $opisStrony,
            "inLanguage" => "pl-PL",
            "isPartOf" => [
                "@type" => "WebSite",
                "name" => "Katalog Monet Świata"
            ],
            "about" => [
                "@type" => "Country",
                "name" => $nazwaPanstwa
            ]
        ],
        JSON_UNESCAPED_UNICODE |
        JSON_UNESCAPED_SLASHES |
        JSON_HEX_TAG |
        JSON_HEX_AMP |
        JSON_HEX_APOS |
        JSON_HEX_QUOT
    ); ?>
    </script>

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
            color: #222222;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.6;
            background: #f2f4f7;
        }

        a {
            color: inherit;
        }

        .site-header {
            padding: 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(
                135deg,
                #17243d,
                #244f79
            );
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
        }

        .site-header__inner {
            width: min(1100px, 100%);
            margin: 0 auto;
        }

        .site-header__title {
            margin: 0;
            color: #ffffff;
            font-size: clamp(1.35rem, 4vw, 2rem);
            line-height: 1.25;
        }

        .main-content {
            width: min(1100px, calc(100% - 30px));
            margin: 0 auto;
            padding: 28px 0 50px;
        }

        .page-title {
            margin: 8px 0 26px;
            color: #1d3557;
            font-size: clamp(2rem, 6vw, 3rem);
            line-height: 1.15;
            text-align: center;
            overflow-wrap: anywhere;
        }

        .content-box {
            width: 100%;
            margin: 0 auto 35px;
            padding: 26px;
            background: #ffffff;
            border: 1px solid #e0e5ea;
            border-radius: 12px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            margin: 0 0 18px;
            color: #244f79;
            font-size: clamp(1.4rem, 4vw, 2rem);
            line-height: 1.25;
        }

        .history {
            color: #303030;
            font-size: 1.05rem;
            overflow-wrap: anywhere;
        }

        .history p {
            margin: 0 0 15px;
        }

        .history p:last-child {
            margin-bottom: 0;
        }

        .empty-message {
            margin: 0;
            padding: 16px;
            color: #5f6368;
            font-style: italic;
            background: #f7f8fa;
            border-left: 4px solid #9aa5b1;
            border-radius: 6px;
        }

        .coins-section {
            margin-top: 38px;
        }

        .coins-title {
            margin: 0 0 25px;
            color: #1d3557;
            font-size: clamp(1.7rem, 5vw, 2.4rem);
            line-height: 1.25;
            text-align: center;
        }

        .coins-list {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(min(100%, 340px), 1fr)
            );
            gap: 22px;
        }

        .coin {
            min-width: 0;
            padding: 22px;
            background: #ffffff;
            border: 1px solid #e0e5ea;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.1);
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .coin:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 20px rgba(0, 0, 0, 0.14);
        }

        .coin__title {
            margin: 0 0 18px;
            color: #244f79;
            font-size: 1.35rem;
            line-height: 1.3;
            overflow-wrap: anywhere;
        }

        .field {
            padding: 11px 0;
            border-bottom: 1px solid #eeeeee;
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
        }

        .back {
            display: flex;
            align-items: center;
            justify-content: center;
            width: fit-content;
            min-width: 190px;
            min-height: 48px;
            margin: 38px auto 0;
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

        .site-footer {
            padding: 24px 15px;
            color: #dce8f5;
            text-align: center;
            background: #102a43;
        }

        .site-footer p {
            margin: 0;
        }

        @media (max-width: 700px) {

            .site-header {
                padding: 17px 15px;
            }

            .main-content {
                width: min(100% - 20px, 1100px);
                padding: 20px 0 38px;
            }

            .page-title {
                margin-bottom: 20px;
            }

            .content-box,
            .coin {
                padding: 18px;
                border-radius: 9px;
            }

            .coins-section {
                margin-top: 30px;
            }

            .coins-list {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .coin:hover {
                transform: none;
            }

            .back {
                width: 100%;
                margin-top: 28px;
            }
        }

        @media (max-width: 420px) {

            .main-content {
                width: min(100% - 14px, 1100px);
            }

            .content-box,
            .coin {
                padding: 15px;
            }

            .page-title {
                font-size: 1.85rem;
            }

            .section-title {
                font-size: 1.35rem;
            }

            .coin__title {
                font-size: 1.2rem;
            }
        }

        @media (prefers-reduced-motion: reduce) {

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

    <div class="site-header__inner">

        <p class="site-header__title">
            Katalog Monet Świata
        </p>

    </div>

</header>

<main class="main-content">

    <h1 class="page-title">
        <?= $nazwaPanstwaHtml ?>
    </h1>

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
                (string) ($panstwo["historia"] ?? "")
            );
            ?>

            <?php if ($historia !== ""): ?>

                <p>
                    <?= nl2br(
                        bezpiecznyTekst($historia)
                    ) ?>
                </p>

            <?php else: ?>

                <p class="empty-message">
                    Brak opisu historii tego państwa.
                </p>

            <?php endif; ?>

        </div>

    </section>

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

                <?php while ($moneta = mysqli_fetch_assoc($monety)): ?>

                    <?php
                    $tytulMonety = "";

                    if (
                        isset($moneta["nazwa"]) &&
                        trim((string) $moneta["nazwa"]) !== ""
                    ) {
                        $tytulMonety = trim(
                            (string) $moneta["nazwa"]
                        );
                    } elseif (
                        isset($moneta["nominal"]) &&
                        trim((string) $moneta["nominal"]) !== ""
                    ) {
                        $tytulMonety =
                            "Moneta " .
                            trim((string) $moneta["nominal"]);
                    } else {
                        $tytulMonety =
                            "Moneta numer " .
                            (int) $moneta["id"];
                    }
                    ?>

                    <article class="coin">

                        <h3 class="coin__title">
                            <?= bezpiecznyTekst($tytulMonety) ?>
                        </h3>

                        <?php foreach ($moneta as $klucz => $wartosc): ?>

                            <?php
                            if (
                                $klucz === "id" ||
                                $klucz === "id_panstwo" ||
                                $klucz === "nazwa"
                            ) {
                                continue;
                            }

                            $nazwaPola = przygotujNazwePola(
                                (string) $klucz,
                                $nazwyPol
                            );

                            $wartoscTekstowa = trim(
                                (string) ($wartosc ?? "")
                            );
                            ?>

                            <div class="field">

                                <span class="field-name">
                                    <?= bezpiecznyTekst($nazwaPola) ?>:
                                </span>

                                <div class="field-value">

                                    <?php if ($wartoscTekstowa !== ""): ?>

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
                    Nie dodano jeszcze monet dla tego państwa.
                </p>

            </div>

        <?php endif; ?>

    </section>

    <a
        class="back"
        href="kontynenty.php"
        aria-label="Powrót do listy kontynentów i państw"
    >
        &larr; Powrót do katalogu
    </a>

</main>

<footer class="site-footer">

    <p>
        &copy; <?= date("Y") ?>
        Katalog Monet Świata. Wszystkie prawa zastrzeżone.
    </p>

</footer>

</body>
</html>

<?php

mysqli_free_result($wynikPanstwo);
mysqli_free_result($monety);

mysqli_stmt_close($stmtPanstwo);
mysqli_stmt_close($stmtMonety);

mysqli_close($conn);

?>