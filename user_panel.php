<?php

session_start();

/* =========================================
   SPRAWDZENIE LOGOWANIA
========================================= */

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

/*
Administrator korzysta z panelu administratora.
*/

if (
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
) {
    header("Location: admin_panel.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "db_connect.php";

mysqli_set_charset($conn, "utf8mb4");

/* =========================================
   FUNKCJA BEZPIECZNEGO WYŚWIETLANIA
========================================= */

function h($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

$username = $_SESSION["user"] ?? "Użytkownik";

/* =========================================
   POBRANIE MONET

   Zdjęcie jest zapisane w kolumnie:
   zdjecie
========================================= */

$monetyResult = mysqli_query(
    $conn,
    "SELECT
        coin.id,
        coin.id_panstwo,
        coin.waluta,
        coin.nominal,
        coin.rok_bicia,
        coin.zdjecie,
        panstwo.nazwa_panstwa
     FROM coin
     LEFT JOIN panstwo
        ON panstwo.id = coin.id_panstwo
     ORDER BY coin.id DESC"
);

$monety = [];

while ($row = mysqli_fetch_assoc($monetyResult)) {
    $monety[] = $row;
}

mysqli_free_result($monetyResult);

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
        Panel użytkownika – Katalog Monet Świata
    </title>

    <meta
        name="robots"
        content="noindex, nofollow"
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
            background: #eef2f6;
            line-height: 1.6;
        }

        /* =========================================
           NAGŁÓWEK
        ========================================= */

        .site-header {
            padding: 25px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(
                135deg,
                #101b2d,
                #244f79
            );
            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.2);
        }

        .site-header h1 {
            margin: 0;
            font-size: clamp(
                1.8rem,
                5vw,
                2.8rem
            );
        }

        .site-header p {
            margin: 8px 0 0;
            color: #d9e6f2;
        }

        .role-badge {
            display: inline-block;
            margin-top: 12px;
            padding: 6px 13px;
            color: #ffffff;
            font-size: 0.9rem;
            font-weight: 700;
            background:
                rgba(255, 255, 255, 0.15);
            border:
                1px solid
                rgba(255, 255, 255, 0.25);
            border-radius: 20px;
        }

        /* =========================================
           MENU
        ========================================= */

        .top-navigation {
            position: sticky;
            top: 0;
            z-index: 20;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
            padding: 12px 20px;
            background: #17243d;
            box-shadow:
                0 3px 10px
                rgba(0, 0, 0, 0.18);
        }

        .top-navigation a {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            min-height: 44px;
            padding: 10px 16px;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            background: #1e88e5;
            border-radius: 7px;
            transition:
                background-color 0.2s ease,
                transform 0.2s ease;
        }

        .top-navigation a:hover {
            background: #1565c0;
            transform: translateY(-2px);
        }

        .top-navigation .add {
            background: #2e7d32;
        }

        .top-navigation .add:hover {
            background: #1b5e20;
        }

        .top-navigation .logout {
            background: #c62828;
        }

        .top-navigation .logout:hover {
            background: #9f1f1f;
        }

        .top-navigation a:focus-visible {
            outline: 3px solid #ffd54f;
            outline-offset: 3px;
        }

        /* =========================================
           GŁÓWNA TREŚĆ
        ========================================= */

        main {
            width: min(
                1200px,
                calc(100% - 30px)
            );
            margin: 35px auto 50px;
        }

        /* =========================================
           KOMUNIKATY
        ========================================= */

        .message {
            margin-bottom: 25px;
            padding: 15px 18px;
            color: #155724;
            background: #d4edda;
            border: 1px solid #b7dfc1;
            border-radius: 8px;
        }

        /* =========================================
           POWITANIE
        ========================================= */

        .welcome {
            margin-bottom: 25px;
            padding: 24px;
            background: #ffffff;
            border-radius: 13px;
            box-shadow:
                0 4px 16px
                rgba(0, 0, 0, 0.1);
        }

        .welcome h2 {
            margin: 0 0 8px;
            color: #1d3557;
            font-size: clamp(
                1.4rem,
                4vw,
                2rem
            );
        }

        .welcome p {
            margin: 0;
            color: #555555;
        }

        /* =========================================
           KARTY PANELU
        ========================================= */

        .panel-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fit,
                    minmax(
                        min(100%, 250px),
                        1fr
                    )
                );
            gap: 22px;
        }

        .panel-card {
            display: flex;
            flex-direction: column;
            padding: 24px;
            background: #ffffff;
            border-radius: 13px;
            box-shadow:
                0 4px 16px
                rgba(0, 0, 0, 0.1);
        }

        .panel-card h2 {
            margin: 0 0 10px;
            color: #1d3557;
            font-size: 1.4rem;
        }

        .panel-card p {
            flex-grow: 1;
            margin: 0 0 20px;
            color: #5c6670;
        }

        .panel-link {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            width: 100%;
            min-height: 48px;
            padding: 12px 16px;
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

        .panel-link:hover {
            background: #1565c0;
            transform: translateY(-2px);
            box-shadow:
                0 5px 12px
                rgba(0, 0, 0, 0.18);
        }

        .panel-link.green {
            background: #2e7d32;
        }

        .panel-link.green:hover {
            background: #1b5e20;
        }

        .panel-link.logout {
            background: #c62828;
        }

        .panel-link.logout:hover {
            background: #9f1f1f;
        }

        .panel-link:focus-visible {
            outline: 3px solid #ffca28;
            outline-offset: 3px;
        }

        /* =========================================
           LISTA MONET
        ========================================= */

        .coins-section {
            margin-top: 30px;
            padding: 25px;
            background: #ffffff;
            border-radius: 13px;
            box-shadow:
                0 4px 16px
                rgba(0, 0, 0, 0.1);
            scroll-margin-top: 90px;
        }

        .coins-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 22px;
        }

        .coins-header h2 {
            margin: 0;
            color: #1d3557;
            font-size: clamp(
                1.5rem,
                4vw,
                2rem
            );
        }

        .coin-count {
            display: inline-block;
            padding: 7px 12px;
            color: #244f79;
            font-weight: 700;
            background: #eaf2f9;
            border-radius: 20px;
        }

        .coins-grid {
            display: grid;
            grid-template-columns:
                repeat(
                    auto-fill,
                    minmax(
                        min(100%, 240px),
                        1fr
                    )
                );
            gap: 22px;
        }

        .coin-card {
            display: flex;
            flex-direction: column;
            overflow: hidden;
            background: #f8fafc;
            border: 1px solid #dce3e9;
            border-radius: 11px;
            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .coin-card:hover {
            transform: translateY(-3px);
            box-shadow:
                0 7px 18px
                rgba(0, 0, 0, 0.13);
        }

        .coin-image-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 220px;
            padding: 12px;
            background: #e9eef3;
            border-bottom: 1px solid #dce3e9;
        }

        .coin-image {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            border-radius: 7px;
        }

        .no-image {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            height: 100%;
            padding: 15px;
            color: #68717a;
            text-align: center;
            font-style: italic;
            background: #f3f5f7;
            border: 1px dashed #aeb8c1;
            border-radius: 7px;
        }

        .coin-content {
            display: flex;
            flex-direction: column;
            flex-grow: 1;
            padding: 18px;
        }

        .coin-content h3 {
            margin: 0 0 12px;
            color: #1d3557;
            font-size: 1.25rem;
        }

        .coin-data {
            margin: 0 0 7px;
            color: #555f68;
        }

        .coin-data strong {
            color: #244f79;
        }

        .coin-details {
            display: block;
            margin-top: auto;
            padding: 10px 14px;
            color: #ffffff;
            font-weight: 700;
            text-align: center;
            text-decoration: none;
            background: #1e88e5;
            border-radius: 7px;
        }

        .coin-details:hover {
            background: #1565c0;
        }

        .empty-message {
            padding: 25px;
            color: #666666;
            text-align: center;
            background: #f7f7f7;
            border: 1px dashed #c5cbd1;
            border-radius: 8px;
        }

        /* =========================================
           INFORMACJA O UPRAWNIENIACH
        ========================================= */

        .permissions {
            margin-top: 25px;
            padding: 20px;
            color: #664d03;
            background: #fff3cd;
            border: 1px solid #ffecb5;
            border-radius: 10px;
        }

        .permissions h2 {
            margin: 0 0 8px;
            font-size: 1.2rem;
        }

        .permissions p {
            margin: 0;
        }

        /* =========================================
           STOPKA
        ========================================= */

        .site-footer {
            padding: 18px 20px;
            color: #d7e0e8;
            text-align: center;
            background: #17243d;
        }

        /* =========================================
           RESPONSYWNOŚĆ
        ========================================= */

        @media (max-width: 650px) {

            main {
                width: calc(100% - 20px);
                margin: 20px auto 35px;
            }

            .welcome,
            .panel-card,
            .coins-section {
                padding: 19px 16px;
            }

            .top-navigation {
                padding: 10px;
            }

            .top-navigation a {
                flex: 1 1 150px;
            }

            .panel-grid,
            .coins-grid {
                grid-template-columns: 1fr;
            }

            .coin-image-wrapper {
                height: 270px;
            }
        }

        @media (max-width: 420px) {

            .top-navigation a {
                flex-basis: 100%;
                width: 100%;
            }

            .site-header {
                padding: 22px 14px;
            }

            .coins-header {
                align-items: stretch;
            }

            .coin-count {
                text-align: center;
            }
        }

        @media (prefers-reduced-motion: reduce) {

            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration:
                    0.01ms !important;
            }
        }

    </style>

</head>

<body>

<header class="site-header">

    <h1>👤 Panel użytkownika</h1>

    <p>
        Zalogowany użytkownik:
        <strong><?= h($username) ?></strong>
    </p>

    <span class="role-badge">
        Konto użytkownika
    </span>

</header>

<nav
    class="top-navigation"
    aria-label="Menu użytkownika"
>

    <a href="kontynenty.php">
        🌍 Katalog
    </a>

    <a href="#monety">
        🪙 Monety
    </a>

    <a
        class="add"
        href="add_panstwo.php"
    >
        ➕ Dodaj państwo
    </a>

    <a
        class="add"
        href="add_coin.php"
    >
        ➕ Dodaj monetę
    </a>

    <a
        class="logout"
        href="logout.php"
    >
        🚪 Wyloguj
    </a>

</nav>

<main>

    <?php if (isset($_GET["dodano_panstwo"])): ?>

        <div
            class="message"
            role="status"
        >
            Państwo zostało prawidłowo dodane.
        </div>

    <?php endif; ?>

    <?php if (isset($_GET["dodano_monete"])): ?>

        <div
            class="message"
            role="status"
        >
            Moneta została prawidłowo dodana.
        </div>

    <?php endif; ?>

    <section class="welcome">

        <h2>
            Witaj, <?= h($username) ?>!
        </h2>

        <p>
            Możesz przeglądać katalog oraz dodawać nowe
            państwa i monety. Nie możesz edytować ani
            usuwać istniejących danych.
        </p>

    </section>

    <div class="panel-grid">

        <article class="panel-card">

            <h2>🌍 Katalog monet</h2>

            <p>
                Przeglądaj kontynenty, państwa oraz wszystkie
                monety znajdujące się w katalogu.
            </p>

            <a
                class="panel-link"
                href="kontynenty.php"
            >
                Otwórz katalog
            </a>

        </article>

        <article class="panel-card">

            <h2>🏳️ Dodaj państwo</h2>

            <p>
                Dodaj nowe państwo, przypisz je do kontynentu
                i uzupełnij opis jego historii.
            </p>

            <a
                class="panel-link green"
                href="add_panstwo.php"
            >
                Dodaj państwo
            </a>

        </article>

        <article class="panel-card">

            <h2>🪙 Dodaj monetę</h2>

            <p>
                Dodaj monetę wraz ze zdjęciem, walutą,
                nominałem, rokiem bicia i opisem.
            </p>

            <a
                class="panel-link green"
                href="add_coin.php"
            >
                Dodaj monetę
            </a>

        </article>

        <article class="panel-card">

            <h2>🚪 Zakończ sesję</h2>

            <p>
                Bezpiecznie wyloguj się ze swojego konta
                po zakończeniu pracy.
            </p>

            <a
                class="panel-link logout"
                href="logout.php"
            >
                Wyloguj
            </a>

        </article>

    </div>

    <!-- =====================================
         MONETY ZE ZDJĘCIAMI
    ====================================== -->

    <section
        class="coins-section"
        id="monety"
    >

        <div class="coins-header">

            <h2>🪙 Monety w katalogu</h2>

            <span class="coin-count">
                Liczba monet: <?= count($monety) ?>
            </span>

        </div>

        <?php if (count($monety) > 0): ?>

            <div class="coins-grid">

                <?php foreach ($monety as $moneta): ?>

                    <?php

                    $zdjecie = trim(
                        (string) (
                            $moneta["zdjecie"] ?? ""
                        )
                    );

                    ?>

                    <article class="coin-card">

                        <div class="coin-image-wrapper">

                            <?php if ($zdjecie !== ""): ?>

                                <img
                                    class="coin-image"
                                    src="<?= h($zdjecie) ?>"
                                    alt="Zdjęcie monety <?= h(
                                        $moneta["nominal"]
                                        ?? $moneta["id"]
                                    ) ?>"
                                    loading="lazy"
                                >

                            <?php else: ?>

                                <div class="no-image">
                                    Brak zdjęcia monety
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="coin-content">

                            <h3>
                                <?= h(
                                    $moneta["waluta"]
                                    ?? "Moneta"
                                ) ?>
                            </h3>

                            <p class="coin-data">

                                <strong>Państwo:</strong>

                                <?= h(
                                    $moneta["nazwa_panstwa"]
                                    ?? "Brak danych"
                                ) ?>

                            </p>

                            <p class="coin-data">

                                <strong>Nominał:</strong>

                                <?= h(
                                    $moneta["nominal"]
                                    ?? "Brak danych"
                                ) ?>

                            </p>

                            <p class="coin-data">

                                <strong>Rok bicia:</strong>

                                <?= h(
                                    $moneta["rok_bicia"]
                                    ?? "Brak danych"
                                ) ?>

                            </p>

                            <a
                                class="coin-details"
                                href="coin.php?id=<?= (int)
                                    $moneta["id"]
                                ?>"
                            >
                                Zobacz szczegóły
                            </a>

                        </div>

                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty-message">
                Nie dodano jeszcze żadnych monet.
            </div>

        <?php endif; ?>

    </section>

    <section class="permissions">

        <h2>🔒 Uprawnienia użytkownika</h2>

        <p>
            Konto użytkownika może przeglądać katalog oraz
            dodawać państwa i monety wraz ze zdjęciami.
            Modyfikowanie i usuwanie istniejących danych
            jest dostępne wyłącznie dla administratora.
        </p>

    </section>

</main>

<footer class="site-footer">

    &copy; <?= date("Y") ?>
    Katalog Monet Świata

</footer>

</body>

</html>

<?php

mysqli_close($conn);

?>
