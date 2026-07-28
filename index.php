<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">

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

    <!--
        Zmień adres na właściwy adres swojej strony.
        Przykład:
        https://twojadomena.pl/
    -->
    <link rel="canonical" href="https://twojadomena.pl/">

    <!-- OPEN GRAPH – UDOSTĘPNIANIE W MEDIACH SPOŁECZNOŚCIOWYCH -->
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

    <!-- KOLOR PASKA PRZEGLĄDARKI MOBILNEJ -->
    <meta name="theme-color" content="#09111c">

    <!-- DANE STRUKTURALNE SEO -->
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
            display: flex;
            flex: 1;
            justify-content: center;
            align-items: center;

            width: 100%;
            padding: 50px 20px;
        }

        .hero {
            width: 100%;
            max-width: 900px;
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
                padding: 35px 15px;
            }

            .hero {
                padding: 35px 22px;
                border-radius: 14px;
            }
        }

        /* =========================================
           TELEFONY
        ========================================= */

        @media (max-width: 520px) {
            .navigation-list {
                display: grid;
                grid-template-columns: 1fr;
                width: 100%;
            }

            .navigation-link {
                width: 100%;
            }

            .main-content {
                align-items: flex-start;
                padding-top: 30px;
                padding-bottom: 30px;
            }

            .hero {
                padding: 30px 18px;
            }

            .hero-description {
                margin-bottom: 24px;
                line-height: 1.6;
            }

            .hero-button {
                width: 100%;
            }

            .site-footer {
                font-size: 13px;
            }
        }

        /* =========================================
           BARDZO MAŁE EKRANY
        ========================================= */

        @media (max-width: 350px) {
            .site-header {
                padding-inline: 10px;
            }

            .main-content {
                padding-inline: 10px;
            }

            .hero {
                padding: 25px 14px;
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
                        <span aria-hidden="true">??</span>
                        <span>Logowanie</span>
                    </a>
                </li>

                <li>
                    <a class="navigation-link" href="register.php">
                        <span aria-hidden="true">??</span>
                        <span>Rejestracja</span>
                    </a>
                </li>

                <li>
                    <a class="navigation-link" href="kontynenty.php">
                        <span aria-hidden="true">??</span>
                        <span>Katalog monet</span>
                    </a>
                </li>

            </ul>
        </nav>
    </header>

    <!-- GŁÓWNA TREŚĆ -->
    <main class="main-content">

        <section class="hero" aria-labelledby="page-title">

            <h1 class="hero-title" id="page-title">
                ?? Katalog Monet Świata
            </h1>

            <p class="hero-description">
                Poznaj kolekcję monet pochodzących z różnych krajów,
                kontynentów i epok historycznych. Odkrywaj ich historię,
                pochodzenie oraz najważniejsze informacje numizmatyczne.
            </p>

            <a class="hero-button" href="kontynenty.php">
                <span aria-hidden="true">??</span>
                <span>Przejdź do katalogu</span>
            </a>

        </section>

    </main>

    <!-- STOPKA -->
    <footer class="site-footer">
        <span>
            &copy; <span id="year"></span>
            Katalog Monet Świata
        </span>
        <span aria-hidden="true"> | </span>
        <span>Wszystkie prawa zastrzeżone</span>
    </footer>

</div>

<script>
    "use strict";

    const yearElement = document.getElementById("year");

    if (yearElement) {
        yearElement.textContent = new Date().getFullYear();
    }
</script>

</body>
</html>