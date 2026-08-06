<?php
declare(strict_types=1);

require_once "db_connect.php";

/*
 * Dodatkowe zabezpieczenie kodowania.
 * Najlepiej umieścić tę instrukcję również bezpośrednio w db_connect.php.
 */
if (!mysqli_set_charset($conn, "utf8mb4")) {
    error_log("Nie udało się ustawić kodowania utf8mb4: " . mysqli_error($conn));
}

/*
 * Pobieranie kontynentów.
 */
$kontynenty_result = mysqli_query(
    $conn,
    "SELECT id, nazwa_kontynentu
     FROM kontynenty
     ORDER BY nazwa_kontynentu ASC"
);

if ($kontynenty_result === false) {
    error_log("Błąd pobierania kontynentów: " . mysqli_error($conn));
    $kontynenty = [];
} else {
    $kontynenty = mysqli_fetch_all($kontynenty_result, MYSQLI_ASSOC);
}

/*
 * Pobieranie wszystkich państw jednym zapytaniem.
 */
$panstwa_result = mysqli_query(
    $conn,
    "SELECT id, id_kontynent, nazwa_panstwa
     FROM panstwo
     ORDER BY nazwa_panstwa ASC"
);

$panstwa_tab = [];

if ($panstwa_result === false) {
    error_log("Błąd pobierania państw: " . mysqli_error($conn));
} else {
    while ($panstwo = mysqli_fetch_assoc($panstwa_result)) {
        $id_kontynentu = (int)$panstwo["id_kontynent"];
        $panstwa_tab[$id_kontynentu][] = $panstwo;
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Katalog monet według kontynentów i państw</title>

    <meta
        name="description"
        content="Katalog monet z różnych kontynentów i państw. Wybierz kontynent, a następnie państwo, aby zobaczyć dostępne monety."
    >

    <meta
        name="keywords"
        content="katalog monet, monety świata, monety państw, numizmatyka, kolekcjonowanie monet"
    >

    <meta name="author" content="Katalog Monet">
    <meta name="robots" content="index, follow">

    <meta property="og:locale" content="pl_PL">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Katalog monet według kontynentów i państw">
    <meta
        property="og:description"
        content="Przeglądaj katalog monet według kontynentów i państw."
    >

    <meta name="theme-color" content="#1565c0">

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
            margin: 0;
            min-height: 100vh;
            font-family: Arial, Helvetica, sans-serif;
            color: #263238;
            background: #f3f6f9;
            line-height: 1.5;
        }

        a {
            color: inherit;
        }

        .page-header {
            padding: 48px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(135deg, #0d47a1, #1e88e5);
        }

        .page-header h1 {
            margin: 0 0 12px;
            font-size: clamp(30px, 5vw, 48px);
            line-height: 1.15;
        }

        .page-header p {
            max-width: 720px;
            margin: 0 auto;
            font-size: clamp(16px, 2vw, 19px);
            color: rgba(255, 255, 255, 0.92);
        }

        .container {
            width: min(1100px, calc(100% - 32px));
            margin: 0 auto;
        }

        .main-content {
            padding: 32px 0 50px;
        }

        .continents-list {
            display: grid;
            gap: 18px;
        }

        .continent {
            overflow: hidden;
            background: #ffffff;
            border: 1px solid #dce4ea;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(20, 50, 80, 0.08);
        }

        .continent summary {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            min-height: 64px;
            padding: 17px 54px 17px 22px;
            color: #ffffff;
            font-size: 20px;
            font-weight: 700;
            cursor: pointer;
            user-select: none;
            list-style: none;
            background: #1976d2;
            transition:
                background-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .continent summary::-webkit-details-marker {
            display: none;
        }

        .continent summary:hover {
            background: #1565c0;
        }

        .continent summary:focus-visible {
            outline: 3px solid #ffca28;
            outline-offset: -3px;
        }

        .continent summary::after {
            content: "";
            position: absolute;
            top: 50%;
            right: 23px;
            width: 10px;
            height: 10px;
            border-right: 3px solid #ffffff;
            border-bottom: 3px solid #ffffff;
            transform: translateY(-65%) rotate(45deg);
            transition: transform 0.2s ease;
        }

        .continent[open] summary::after {
            transform: translateY(-25%) rotate(225deg);
        }

        .country-count {
            font-size: 14px;
            font-weight: 400;
            color: rgba(255, 255, 255, 0.88);
        }

        .countries-content {
            padding: 12px 18px 18px;
            background: #ffffff;
        }

        .country-list {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .country-link {
            display: flex;
            align-items: center;
            min-height: 48px;
            padding: 12px 14px;
            color: #263238;
            text-decoration: none;
            background: #f5f8fa;
            border: 1px solid #dfe7ec;
            border-radius: 7px;
            transition:
                background-color 0.2s ease,
                border-color 0.2s ease,
                transform 0.2s ease;
        }

        .country-link:hover {
            color: #0d47a1;
            background: #e3f2fd;
            border-color: #90caf9;
            transform: translateY(-1px);
        }

        .country-link:focus-visible {
            outline: 3px solid #ffca28;
            outline-offset: 2px;
        }

        .empty-message,
        .error-message {
            margin: 0;
            padding: 20px;
            text-align: center;
            background: #ffffff;
            border-radius: 10px;
        }

        .error-message {
            color: #b71c1c;
            border: 1px solid #ffcdd2;
        }

        .empty-message {
            color: #546e7a;
        }

        .page-footer {
            padding: 24px 16px;
            color: #dce8f5;
            text-align: center;
            background: #102a43;
        }

        .page-footer p {
            margin: 0;
        }

        .visually-hidden {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        @media (max-width: 850px) {
            .country-list {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 560px) {
            .page-header {
                padding: 34px 16px;
            }

            .container {
                width: min(100% - 20px, 1100px);
            }

            .main-content {
                padding: 20px 0 35px;
            }

            .continents-list {
                gap: 12px;
            }

            .continent summary {
                min-height: 58px;
                padding: 14px 46px 14px 16px;
                font-size: 18px;
            }

            .continent summary::after {
                right: 18px;
            }

            .country-count {
                display: none;
            }

            .countries-content {
                padding: 10px;
            }

            .country-list {
                grid-template-columns: 1fr;
                gap: 8px;
            }

            .country-link {
                min-height: 46px;
                padding: 11px 12px;
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

<header class="page-header">
    <div class="container">
        <h1>Katalog monet</h1>

        <p>
            Wybierz kontynent, a następnie państwo, aby zobaczyć monety
            znajdujące się w katalogu.
        </p>
    </div>
</header>

<main class="main-content">
    <div class="container">

        <section aria-labelledby="lista-kontynentow">
            <h2 id="lista-kontynentow" class="visually-hidden">
                Kontynenty i państwa
            </h2>

            <?php if (empty($kontynenty)): ?>

                <p class="error-message">
                    Nie udało się pobrać listy kontynentów.
                </p>

            <?php else: ?>

                <div class="continents-list">

                    <?php foreach ($kontynenty as $kontynent): ?>

                        <?php
                        $id_kontynentu = (int)$kontynent["id"];

                        $nazwa_kontynentu = htmlspecialchars(
                            $kontynent["nazwa_kontynentu"],
                            ENT_QUOTES,
                            "UTF-8"
                        );

                        $liczba_panstw = isset($panstwa_tab[$id_kontynentu])
                            ? count($panstwa_tab[$id_kontynentu])
                            : 0;
                        ?>

                        <details class="continent">

                            <summary>
                                <span><?= $nazwa_kontynentu ?></span>

                                <span class="country-count">
                                    Liczba państw: <?= $liczba_panstw ?>
                                </span>
                            </summary>

                            <div class="countries-content">

                                <?php if ($liczba_panstw > 0): ?>

                                    <ul class="country-list">

                                        <?php foreach ($panstwa_tab[$id_kontynentu] as $panstwo): ?>

                                            <?php
                                            $id_panstwa = (int)$panstwo["id"];

                                            $nazwa_panstwa = htmlspecialchars(
                                                $panstwo["nazwa_panstwa"],
                                                ENT_QUOTES,
                                                "UTF-8"
                                            );
                                            ?>

                                            <li>
                                                <a
                                                    class="country-link"
                                                    href="panstwo.php?id=<?= $id_panstwa ?>"
                                                    title="Zobacz monety z państwa <?= $nazwa_panstwa ?>"
                                                >
                                                    <?= $nazwa_panstwa ?>
                                                </a>
                                            </li>

                                        <?php endforeach; ?>

                                    </ul>

                                <?php else: ?>

                                    <p class="empty-message">
                                        Brak państw przypisanych do tego kontynentu.
                                    </p>

                                <?php endif; ?>

                            </div>

                        </details>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>

    </div>
</main>

<footer class="page-footer">
    <p>
        &copy; <?= date("Y") ?> Katalog Monet. Wszystkie prawa zastrzeżone.
    </p>
</footer>

</body>
</html>