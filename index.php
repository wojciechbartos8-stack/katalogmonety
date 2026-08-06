<?php
declare(strict_types=1);

header("Content-Type: text/html; charset=UTF-8");
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <!-- RESPONSYWNOŚĆ -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- PODSTAWOWE SEO -->
    <title>Katalog Monet Świata – kolekcja monet z różnych krajów</title>

    <meta
        name="description"
        content="Internetowy katalog monet świata. Przeglądaj monety z różnych krajów, kontynentów i epok historycznych."
    >

    <meta
        name="keywords"
        content="katalog monet, monety świata, kolekcjonowanie monet, numizmatyka, stare monety, monety kolekcjonerskie"
    >

    <meta name="author" content="Katalog Monet">
    <meta name="robots" content="index, follow">

    <link rel="canonical" href="https://twojadomena.pl/">

    <!-- OPEN GRAPH -->
    <meta property="og:type" content="website">
    <meta property="og:locale" content="pl_PL">
    <meta property="og:title" content="Katalog Monet Świata">
    <meta
        property="og:description"
        content="Poznaj kolekcję monet z różnych krajów, kontynentów i epok historycznych."
    >
    <meta property="og:url" content="https://twojadomena.pl/">
    <meta
        property="og:image"
        content="https://images.unsplash.com/photo-1605902711622-cfb43c4437d3?auto=format&fit=crop&w=1600&q=80"
    >

    <meta name="theme-color" content="#09111c">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "Katalog Monet Świata",
        "description": "Internetowy katalog monet z różnych krajów, kontynentów i epok historycznych.",
        "inLanguage": "pl-PL",
        "url": "https://twojadomena.pl/"
    }
    </script>

    <style>
        /* =========================================
           USTAWIENIA PODSTAWOWE
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

            font-family: Arial, Helvetica, sans-serif;
            color: #ffffff;

            background-color: #101820;
            background-image:
                linear-gradient(
                    rgba(0, 0, 0, 0.58),
                    rgba(0, 0, 0, 0.7)
                ),
                url("https://images.unsplash.com/photo-1605902711622-cfb43c4437d3?auto=format&fit=crop&w=1600&q=80");

            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
            background-attachment: fixed;
        }

        a {
            color: inherit;
        }

        h1,
        h2,
        h3,
        p,
        a,
        span {
            overflow-wrap: anywhere;
        }

        /* =========================================
           UKŁAD STRONY
        ========================================= */

        .page {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-height: 100svh;
        }

        /* =========================================
           NAGŁÓWEK I MENU
        ========================================= */

        .site-header {
            position: relative;
            z-index: 10;

            width: 100%;
            padding: 12px 20px;

            background: rgba(4, 10, 18, 0.88);
            border-bottom: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.25);

            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        .navigation {
            width: 100%;
            max-width: 1100px;
            margin: 0 auto;
        }

        .navigation-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            gap: 12px;

            margin: 0;
            padding: 0;

            list-style: none;
        }

        .navigation-link {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;

            min-height: 44px;
            padding: 10px 18px;

            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;

            background: linear-gradient(135deg, #1261a6, #1e88e5);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 8px;

            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);

            transition:
                transform 0.2s ease,
                background-color 0.2s ease,
                box-shadow 0.2s ease;
        }

        .navigation-link:hover {
            transform: translateY(-2px);
            background: linear-gradient(135deg, #1976d2, #42a5f5);
            box-shadow: 0 7px 16px rgba(0, 0, 0, 0.3);
        }

        .navigation-link:focus-visible {
            outline: 3px solid #ffd54f;
            outline-offset: 3px;
        }

        /* =========================================
           GŁÓWNA TREŚĆ
        ========================================= */

        .main-content {
            width: 100%;
            flex: 1;
            padding: 50px 20px 60px;
        }

        .content-wrapper {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero {
            width: 100%;
            max-width: 900px;
            margin: 0 auto 40px;
            padding: 45px 35px;

            text-align: center;

            background: rgba(0, 0, 0, 0.35);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 18px;

            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);

            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .hero-title {
            margin: 0 0 18px;

            font-size: clamp(2rem, 6vw, 3.5rem);
            line-height: 1.1;

            text-shadow: 0 3px 12px rgba(0, 0, 0, 0.9);
        }

        .hero-description {
            max-width: 680px;
            margin: 0 auto 30px;

            color: #f1f1f1;
            font-size: clamp(1rem, 2.5vw, 1.25rem);
            line-height: 1.7;

            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.9);
        }

        .hero-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 9px;

            min-height: 48px;
            padding: 12px 24px;

            color: #18202a;
            font-weight: 700;
            text-decoration: none;

            background: linear-gradient(135deg, #f2b705, #ffd54f);
            border-radius: 8px;

            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.3);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .hero-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 22px rgba(0, 0, 0, 0.4);
        }

        .hero-button:focus-visible {
            outline: 3px solid #ffffff;
            outline-offset: 4px;
        }

        /* =========================================
           SEKCJA KAFELKÓW
        ========================================= */

        .coins-section {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;

            padding: 32px 26px;

            background: rgba(6, 14, 24, 0.62);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 18px;
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.35);

            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
        }

        .section-title {
            margin: 0 0 12px;
            text-align: center;
            font-size: clamp(1.8rem, 4vw, 2.5rem);
            text-shadow: 0 3px 10px rgba(0, 0, 0, 0.8);
        }

        .section-description {
            max-width: 760px;
            margin: 0 auto 30px;
            text-align: center;
            color: #e7e7e7;
            line-height: 1.7;
        }

        .coins-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(100%, 230px), 1fr));
            gap: 22px;
        }

        .coin-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;

            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 16px;

            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.22);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease,
                border-color 0.2s ease;
        }

        .coin-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 28px rgba(0, 0, 0, 0.35);
            border-color: rgba(255, 213, 79, 0.4);
        }

        .coin-image-box {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 180px;
            padding: 24px 18px;
            background:
                radial-gradient(circle at center, #ffe082 0%, #f2b705 45%, #bb8500 100%);
        }

        .coin-circle {
            display: flex;
            justify-content: center;
            align-items: center;

            width: 110px;
            height: 110px;

            color: #6a4300;
            font-size: 46px;
            font-weight: 700;

            background:
                radial-gradient(circle at 30% 30%, #fff4b8, #ffd54f 55%, #c48a00 100%);
            border: 4px solid rgba(255, 255, 255, 0.35);
            border-radius: 50%;

            box-shadow:
                inset 0 3px 10px rgba(255, 255, 255, 0.45),
                0 8px 16px rgba(0, 0, 0, 0.28);
        }

        .coin-content {
            display: flex;
            flex-direction: column;
            flex: 1;
            padding: 20px 18px 18px;
        }

        .coin-country {
            display: inline-block;
            align-self: flex-start;
            margin-bottom: 12px;
            padding: 5px 10px;

            color: #18202a;
            font-size: 13px;
            font-weight: 700;

            background: #ffd54f;
            border-radius: 999px;
        }

        .coin-title {
            margin: 0 0 10px;
            font-size: 1.2rem;
            line-height: 1.3;
        }

        .coin-info {
            margin: 0 0 8px;
            color: #f1f1f1;
            font-size: 15px;
            line-height: 1.6;
        }

        .coin-info strong {
            color: #ffd54f;
        }

        .coin-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;

            margin-top: auto;
            min-height: 44px;
            padding: 11px 16px;

            color: #18202a;
            font-weight: 700;
            text-decoration: none;

            background: linear-gradient(135deg, #f2b705, #ffd54f);
            border-radius: 8px;

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .coin-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.28);
        }

        .see-more-box {
            margin-top: 30px;
            text-align: center;
        }

        .see-more-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;

            min-height: 48px;
            padding: 12px 24px;

            color: #ffffff;
            font-weight: 700;
            text-decoration: none;

            background: linear-gradient(135deg, #1261a6, #1e88e5);
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.18);

            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.25);

            transition:
                transform 0.2s ease,
                box-shadow 0.2s ease;
        }

        .see-more-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 9px 22px rgba(0, 0, 0, 0.35);
        }

        /* =========================================
           STOPKA
        ========================================= */

        .site-footer {
            position: relative;
            z-index: 10;

            width: 100%;
            padding: 14px 20px;

            color: #dddddd;
            font-size: 14px;
            line-height: 1.5;
            text-align: center;

            background: rgba(4, 10, 18, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.15);

            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
        }

        /* =========================================
           TABLETY
        ========================================= */

        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }

            .site-header {
                padding: 10px 15px;
            }

            .navigation-list {
                gap: 9px;
            }

            .navigation-link {
                padding: 10px 14px;
                font-size: 15px;
            }

            .main-content {
                padding: 35px 15px 40px;
            }

            .hero {
                padding: 35px 22px;
                border-radius: 14px;
                margin-bottom: 28px;
            }

            .coins-section {
                padding: 24px 18px;
                border-radius: 14px;
            }
        }

        /* =========================================
           TELEFONY
        ========================================= */

        @media (max-width: 520px) {
            .navigation-list {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                width: 100%;
            }

            .navigation-list li:last-child {
                grid-column: 1 / -1;
            }

            .navigation-link {
                width: 100%;
            }

            .hero {
                padding: 30px 18px;
            }

            .hero-description {
                margin-bottom: 24px;
                line-height: 1.6;
            }

            .hero-button,
            .see-more-button,
            .coin-button {
                width: 100%;
            }

            .coin-image-box {
                min-height: 150px;
            }

            .coin-circle {
                width: 95px;
                height: 95px;
                font-size: 38px;
            }

            .site-footer {
                font-size: 13px;
            }
        }

        /* =========================================
           BARDZO MAŁE EKRANY
        ========================================= */

        @media (max-width: 350px) {
            .navigation-list {
                grid-template-columns: 1fr;
            }

            .navigation-list li:last-child {
                grid-column: auto;
            }

            .site-header {
                padding-inline: 10px;
            }

            .main-content {
                padding-inline: 10px;
            }

            .hero {
                padding: 25px 14px;
            }

            .coins-section {
                padding: 20px 14px;
            }
        }

        /* =========================================
           OGRANICZENIE ANIMACJI
        ========================================= */

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition-duration: 0.01ms !important;
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
            }
        }
    </style>
</head>

<body>

<div class="page">

    <!-- NAGŁÓWEK STRONY -->
    <header class="site-header">
        <nav class="navigation" aria-label="Główne menu">
            <ul class="navigation-list">

                <li>
                    <a class="navigation-link" href="login.php">
                        <span aria-hidden="true">&#128273;</span>
                        <span>Logowanie</span>
                    </a>
                </li>

                <li>
                    <a class="navigation-link" href="register.php">
                        <span aria-hidden="true">&#128221;</span>
                        <span>Rejestracja</span>
                    </a>
                </li>

                <li>
                    <a class="navigation-link" href="kontynenty.php">
                        <span aria-hidden="true">&#128176;</span>
                        <span>Katalog monet</span>
                    </a>
                </li>

            </ul>
        </nav>
    </header>

    <!-- GŁÓWNA TREŚĆ -->
    <main class="main-content">
        <div class="content-wrapper">

            <!-- HERO -->
            <section class="hero" aria-labelledby="page-title">

                <h1 class="hero-title" id="page-title">
                    &#128176; Katalog Monet Świata
                </h1>

                <p class="hero-description">
                    Poznaj kolekcję monet pochodzących z różnych krajów,
                    kontynentów i epok historycznych. Odkrywaj ich historię,
                    pochodzenie oraz najważniejsze informacje numizmatyczne.
                </p>

                <a class="hero-button" href="kontynenty.php">
                    <span aria-hidden="true">&#8594;</span>
                    <span>Przejdź do katalogu</span>
                </a>

            </section>

            <!-- KAFELKI MONET -->
            <section class="coins-section" aria-labelledby="coins-title">

                <h2 class="section-title" id="coins-title">
                    Wybrane monety z kolekcji
                </h2>

                <p class="section-description">
                    Przeglądaj przykładowe monety z różnych krajów i odkrywaj
                    ich nominały, waluty oraz lata emisji.
                </p>

                <div class="coins-grid">

                    <article class="coin-card">
                        <div class="coin-image-box">
                            <div class="coin-circle">1€</div>
                        </div>

                        <div class="coin-content">
                            <span class="coin-country">Niemcy</span>
                            <h3 class="coin-title">Moneta obiegowa Euro</h3>
                            <p class="coin-info"><strong>Waluta:</strong> Euro</p>
                            <p class="coin-info"><strong>Nominał:</strong> 1 Euro</p>
                            <p class="coin-info"><strong>Rok bicia:</strong> 2018</p>

                            <a class="coin-button" href="kontynenty.php">
                                <span aria-hidden="true">&#8594;</span>
                                <span>Zobacz więcej</span>
                            </a>
                        </div>
                    </article>

                    <article class="coin-card">
                        <div class="coin-image-box">
                            <div class="coin-circle">2 zł</div>
                        </div>

                        <div class="coin-content">
                            <span class="coin-country">Polska</span>
                            <h3 class="coin-title">Moneta kolekcjonerska</h3>
                            <p class="coin-info"><strong>Waluta:</strong> Złoty</p>
                            <p class="coin-info"><strong>Nominał:</strong> 2 złote</p>
                            <p class="coin-info"><strong>Rok bicia:</strong> 2005</p>

                            <a class="coin-button" href="kontynenty.php">
                                <span aria-hidden="true">&#8594;</span>
                                <span>Zobacz więcej</span>
                            </a>
                        </div>
                    </article>

                    <article class="coin-card">
                        <div class="coin-image-box">
                            <div class="coin-circle">$1</div>
                        </div>

                        <div class="coin-content">
                            <span class="coin-country">USA</span>
                            <h3 class="coin-title">Moneta dolarowa</h3>
                            <p class="coin-info"><strong>Waluta:</strong> Dolar amerykański</p>
                            <p class="coin-info"><strong>Nominał:</strong> 1 Dollar</p>
                            <p class="coin-info"><strong>Rok bicia:</strong> 1999</p>

                            <a class="coin-button" href="kontynenty.php">
                                <span aria-hidden="true">&#8594;</span>
                                <span>Zobacz więcej</span>
                            </a>
                        </div>
                    </article>

                    <article class="coin-card">
                        <div class="coin-image-box">
                            <div class="coin-circle">L1</div>
                        </div>

                        <div class="coin-content">
                            <span class="coin-country">Wielka Brytania</span>
                            <h3 class="coin-title">Moneta funtowa</h3>
                            <p class="coin-info"><strong>Waluta:</strong> Funt szterling</p>
                            <p class="coin-info"><strong>Nominał:</strong> 1 Pound</p>
                            <p class="coin-info"><strong>Rok bicia:</strong> 2017</p>

                            <a class="coin-button" href="kontynenty.php">
                                <span aria-hidden="true">&#8594;</span>
                                <span>Zobacz więcej</span>
                            </a>
                        </div>
                    </article>

                    <article class="coin-card">
                        <div class="coin-image-box">
                            <div class="coin-circle">5Y</div>
                        </div>

                        <div class="coin-content">
                            <span class="coin-country">Japonia</span>
                            <h3 class="coin-title">Moneta japońska</h3>
                            <p class="coin-info"><strong>Waluta:</strong> Jen</p>
                            <p class="coin-info"><strong>Nominał:</strong> 5 Yen</p>
                            <p class="coin-info"><strong>Rok bicia:</strong> 2012</p>

                            <a class="coin-button" href="kontynenty.php">
                                <span aria-hidden="true">&#8594;</span>
                                <span>Zobacz więcej</span>
                            </a>
                        </div>
                    </article>

                    <article class="coin-card">
                        <div class="coin-image-box">
                            <div class="coin-circle">1C$</div>
                        </div>

                        <div class="coin-content">
                            <span class="coin-country">Kanada</span>
                            <h3 class="coin-title">Moneta kanadyjska</h3>
                            <p class="coin-info"><strong>Waluta:</strong> Dolar kanadyjski</p>
                            <p class="coin-info"><strong>Nominał:</strong> 1 Dollar</p>
                            <p class="coin-info"><strong>Rok bicia:</strong> 2014</p>

                            <a class="coin-button" href="kontynenty.php">
                                <span aria-hidden="true">&#8594;</span>
                                <span>Zobacz więcej</span>
                            </a>
                        </div>
                    </article>

                </div>

                <div class="see-more-box">
                    <a class="see-more-button" href="kontynenty.php">
                        <span aria-hidden="true">&#8594;</span>
                        <span>Zobacz cały katalog monet</span>
                    </a>
                </div>

            </section>

        </div>
    </main>

    <!-- STOPKA -->
    <footer class="site-footer">
        <span>
            &copy; <?= date("Y") ?>
            Katalog Monet Świata
        </span>
        <span aria-hidden="true"> | </span>
        <span>Wszystkie prawa zastrzeżone</span>
    </footer>

</div>

</body>
</html>