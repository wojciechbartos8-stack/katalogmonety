<?php

session_start();

/*
Zarówno administrator, jak i zwyk³y zalogowany u¿ytkownik
mog¹ dodawaæ monety.
*/

if (!isset($_SESSION["user"])) {
    header("Location: index.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "db_connect.php";

mysqli_set_charset($conn, "utf8mb4");

/* =========================================
   FUNKCJA BEZPIECZNEGO WYŒWIETLANIA
========================================= */

function h($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

/* =========================================
   PANEL POWROTNY
========================================= */

$isAdmin = (
    isset($_SESSION["role"]) &&
    $_SESSION["role"] === "admin"
);

$panelPowrotny = $isAdmin
    ? "admin_panel.php"
    : "user_panel.php";

/* =========================================
   TOKEN ZABEZPIECZAJ¥CY FORMULARZ
========================================= */

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(
        random_bytes(32)
    );
}

$blad = "";

/* =========================================
   OBS£UGA FORMULARZA
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($csrfToken) ||
        !hash_equals(
            $_SESSION["csrf_token"],
            $csrfToken
        )
    ) {
        die("Nieprawid³owy token formularza.");
    }

    $idPanstwo = isset($_POST["id_panstwo"])
        ? (int) $_POST["id_panstwo"]
        : 0;

    $waluta = trim(
        $_POST["waluta"] ?? ""
    );

    $walutaObiegowa = trim(
        $_POST["waluta_obiegowa"] ?? ""
    );

    $walutaKolekcjonerska = trim(
        $_POST["waluta_kolekcjonerska"] ?? ""
    );

    $nominal = trim(
        $_POST["nominal"] ?? ""
    );

    $rokBiciaTekst = trim(
        $_POST["rok_bicia"] ?? ""
    );

    $historiaWaluty = trim(
        $_POST["historia_waluty"] ?? ""
    );

    $historiaJednostkiMonetarnej = trim(
        $_POST["historia_jednostki_monetarnej"] ?? ""
    );

    /* =========================================
       WALIDACJA PÓL
    ========================================= */

    if ($idPanstwo <= 0) {

        $blad = "Wybierz pañstwo.";

    } elseif ($waluta === "") {

        $blad = "Nazwa waluty jest wymagana.";

    } elseif ($walutaObiegowa === "") {

        $blad = "Informacja o walucie obiegowej jest wymagana.";

    } elseif ($walutaKolekcjonerska === "") {

        $blad = "Informacja o walucie kolekcjonerskiej jest wymagana.";

    } elseif ($nominal === "") {

        $blad = "Nomina³ jest wymagany.";

    } elseif (
        $rokBiciaTekst === "" ||
        filter_var(
            $rokBiciaTekst,
            FILTER_VALIDATE_INT
        ) === false
    ) {

        $blad = "Podaj prawid³owy rok bicia.";

    } elseif ($historiaWaluty === "") {

        $blad = "Historia waluty jest wymagana.";

    } elseif ($historiaJednostkiMonetarnej === "") {

        $blad = "Historia jednostki monetarnej jest wymagana.";

    } else {

        $rokBicia = (int) $rokBiciaTekst;

        /* =========================================
           SPRAWDZENIE PAÑSTWA
        ========================================= */

        $stmtPanstwo = mysqli_prepare(
            $conn,
            "SELECT id
             FROM panstwo
             WHERE id = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmtPanstwo,
            "i",
            $idPanstwo
        );

        mysqli_stmt_execute($stmtPanstwo);

        $wynikPanstwo = mysqli_stmt_get_result(
            $stmtPanstwo
        );

        if (mysqli_num_rows($wynikPanstwo) === 0) {

            $blad = "Wybrane pañstwo nie istnieje.";

            mysqli_stmt_close($stmtPanstwo);

        } else {

            mysqli_stmt_close($stmtPanstwo);

            /* =========================================
               OBS£UGA ZDJÊCIA
            ========================================= */

            $sciezkaZdjecia = "";
            $pelnaSciezkaZdjecia = "";

            if (
                isset($_FILES["zdjecie"]) &&
                $_FILES["zdjecie"]["error"]
                    !== UPLOAD_ERR_NO_FILE
            ) {

                if (
                    $_FILES["zdjecie"]["error"]
                    !== UPLOAD_ERR_OK
                ) {

                    $blad =
                        "Wyst¹pi³ b³¹d podczas przesy³ania zdjêcia.";

                } elseif (
                    $_FILES["zdjecie"]["size"]
                    > 5 * 1024 * 1024
                ) {

                    $blad =
                        "Zdjêcie mo¿e mieæ maksymalnie 5 MB.";

                } else {

                    $plikTymczasowy =
                        $_FILES["zdjecie"]["tmp_name"];

                    /*
                    Sprawdzenie prawdziwego typu pliku,
                    a nie tylko jego rozszerzenia.
                    */

                    $finfo = new finfo(
                        FILEINFO_MIME_TYPE
                    );

                    $typMime = $finfo->file(
                        $plikTymczasowy
                    );

                    $dozwoloneTypy = [
                        "image/jpeg" => "jpg",
                        "image/png" => "png",
                        "image/webp" => "webp"
                    ];

                    if (
                        !isset(
                            $dozwoloneTypy[$typMime]
                        )
                    ) {

                        $blad =
                            "Dozwolone formaty zdjêcia: JPG, PNG i WEBP.";

                    } else {

                        $katalogWzgledny =
                            "images/monety";

                        $katalogDocelowy =
                            __DIR__ .
                            DIRECTORY_SEPARATOR .
                            "images" .
                            DIRECTORY_SEPARATOR .
                            "monety";

                        /*
                        Utworzenie katalogu, je¿eli
                        jeszcze nie istnieje.
                        */

                        if (
                            !is_dir($katalogDocelowy) &&
                            !mkdir(
                                $katalogDocelowy,
                                0775,
                                true
                            ) &&
                            !is_dir($katalogDocelowy)
                        ) {

                            $blad =
                                "Nie uda³o siê utworzyæ katalogu na zdjêcia.";

                        } else {

                            $rozszerzenie =
                                $dozwoloneTypy[$typMime];

                            /*
                            Losowa nazwa zabezpiecza przed
                            nadpisywaniem plików.
                            */

                            $nazwaPliku =
                                "moneta_" .
                                date("Ymd_His") .
                                "_" .
                                bin2hex(
                                    random_bytes(5)
                                ) .
                                "." .
                                $rozszerzenie;

                            $pelnaSciezkaZdjecia =
                                $katalogDocelowy .
                                DIRECTORY_SEPARATOR .
                                $nazwaPliku;

                            /*
                            Taka œcie¿ka zostanie zapisana
                            w bazie danych.
                            */

                            $sciezkaZdjecia =
                                $katalogWzgledny .
                                "/" .
                                $nazwaPliku;

                            if (
                                !move_uploaded_file(
                                    $plikTymczasowy,
                                    $pelnaSciezkaZdjecia
                                )
                            ) {

                                $blad =
                                    "Nie uda³o siê zapisaæ zdjêcia.";

                                $sciezkaZdjecia = "";
                                $pelnaSciezkaZdjecia = "";
                            }
                        }
                    }
                }
            }

            /* =========================================
               DODAWANIE MONETY DO BAZY
            ========================================= */

            if ($blad === "") {

                try {

                    $stmtInsert = mysqli_prepare(
                        $conn,
                        "INSERT INTO coin
                        (
                            id_panstwo,
                            waluta,
                            `waluta obiegowa`,
                            `waluta kolekcjonerska`,
                            nominal,
                            rok_bicia,
                            historia_waluty,
                            `historia_jednostki _monetarnej`,
                            zdjecie
                        )
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                    );

                    mysqli_stmt_bind_param(
                        $stmtInsert,
                        "issssisss",
                        $idPanstwo,
                        $waluta,
                        $walutaObiegowa,
                        $walutaKolekcjonerska,
                        $nominal,
                        $rokBicia,
                        $historiaWaluty,
                        $historiaJednostkiMonetarnej,
                        $sciezkaZdjecia
                    );

                    mysqli_stmt_execute($stmtInsert);

                    $noweIdMonety =
                        mysqli_insert_id($conn);

                    mysqli_stmt_close($stmtInsert);

                    header(
                        "Location: " .
                        $panelPowrotny .
                        "?dodano_monete=" .
                        $noweIdMonety .
                        "#monety"
                    );

                    exit;

                } catch (Throwable $e) {

                    /*
                    Je¿eli zapis do bazy siê nie uda,
                    usuwamy wczeœniej przes³any plik.
                    */

                    if (
                        $pelnaSciezkaZdjecia !== "" &&
                        is_file($pelnaSciezkaZdjecia)
                    ) {
                        unlink($pelnaSciezkaZdjecia);
                    }

                    throw $e;
                }
            }
        }
    }
}

/* =========================================
   POBRANIE LISTY PAÑSTW
========================================= */

$panstwaResult = mysqli_query(
    $conn,
    "SELECT id, nazwa_panstwa
     FROM panstwo
     ORDER BY nazwa_panstwa ASC"
);

$liczbaPanstw = mysqli_num_rows(
    $panstwaResult
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
        Dodaj monetê – Katalog Monet Œwiata
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

        .site-header {
            padding: 24px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(
                135deg,
                #17243d,
                #244f79
            );
            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, 0.18);
        }

        .site-header h1 {
            margin: 0;
            font-size: clamp(
                1.8rem,
                5vw,
                2.7rem
            );
        }

        .site-header p {
            margin: 8px 0 0;
            color: #dce8f3;
        }

        .container {
            width: min(
                900px,
                calc(100% - 30px)
            );

            margin: 35px auto;
            padding: 30px;

            background: #ffffff;
            border-radius: 14px;

            box-shadow:
                0 4px 18px
                rgba(0, 0, 0, 0.12);
        }

        .form-title {
            margin: 0 0 28px;
            color: #1d3557;
            text-align: center;

            font-size: clamp(
                1.5rem,
                4vw,
                2.1rem
            );
        }

        .form-grid {
            display: grid;

            grid-template-columns:
                repeat(
                    2,
                    minmax(0, 1fr)
                );

            gap: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #244f79;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 12px 13px;
            font: inherit;
            color: #222222;
            background: #ffffff;
            border: 1px solid #bec7d0;
            border-radius: 7px;
        }

        input[type="file"] {
            padding: 10px;
            background: #f8fafc;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline:
                3px solid
                rgba(30, 136, 229, 0.2);

            border-color: #1e88e5;
        }

        textarea {
            min-height: 190px;
            resize: vertical;
            line-height: 1.6;
        }

        .help-text {
            display: block;
            margin-top: 6px;
            color: #68717a;
            font-size: 0.9rem;
        }

        .error {
            margin-bottom: 24px;
            padding: 14px 16px;
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f1b0b7;
            border-radius: 7px;
        }

        .warning {
            margin-bottom: 24px;
            padding: 14px 16px;
            color: #664d03;
            background: #fff3cd;
            border: 1px solid #ffecb5;
            border-radius: 7px;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }

        button,
        .back-button {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 48px;
            padding: 12px 21px;
            font: inherit;
            font-weight: 700;
            border: none;
            border-radius: 7px;
        }

        button {
            color: #ffffff;
            background: #2e7d32;
            cursor: pointer;
        }

        button:hover {
            background: #1b5e20;
        }

        button:disabled {
            color: #dddddd;
            background: #888888;
            cursor: not-allowed;
        }

        .back-button {
            color: #ffffff;
            text-decoration: none;
            background: #6c757d;
        }

        .back-button:hover {
            background: #545b62;
        }

        button:focus-visible,
        .back-button:focus-visible {
            outline: 3px solid #ffca28;
            outline-offset: 3px;
        }

        @media (max-width: 700px) {

            .container {
                width: calc(100% - 20px);
                margin: 20px auto;
                padding: 22px 16px;
                border-radius: 10px;
            }

            .form-grid {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .form-group.full {
                grid-column: auto;
            }

            textarea {
                min-height: 170px;
            }

            .buttons {
                flex-direction: column;
            }

            button,
            .back-button {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<header class="site-header">

    <h1>?? Katalog Monet Œwiata</h1>

    <p>
        Dodawanie nowej monety do katalogu
    </p>

</header>

<main class="container">

    <h2 class="form-title">
        ? Dodaj monetê
    </h2>

    <?php if ($blad !== ""): ?>

        <div
            class="error"
            role="alert"
        >
            <?= h($blad) ?>
        </div>

    <?php endif; ?>

    <?php if ($liczbaPanstw === 0): ?>

        <div
            class="warning"
            role="alert"
        >

            Przed dodaniem monety musisz najpierw
            dodaæ przynajmniej jedno pañstwo.

            <br><br>

            <a href="add_panstwo.php">
                PrzejdŸ do dodawania pañstwa
            </a>

        </div>

    <?php endif; ?>

    <form
        method="post"
        action="add_coin.php"
        enctype="multipart/form-data"
        autocomplete="off"
    >

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h(
                $_SESSION["csrf_token"]
            ) ?>"
        >

        <div class="form-grid">

            <!-- PAÑSTWO -->

            <div class="form-group full">

                <label for="id_panstwo">
                    Pañstwo
                </label>

                <select
                    id="id_panstwo"
                    name="id_panstwo"
                    required
                >

                    <option value="">
                        Wybierz pañstwo
                    </option>

                    <?php while (
                        $panstwo = mysqli_fetch_assoc(
                            $panstwaResult
                        )
                    ): ?>

                        <option
                            value="<?= (int)
                                $panstwo["id"]
                            ?>"
                            <?= (
                                (int) (
                                    $_POST["id_panstwo"] ?? 0
                                )
                                ===
                                (int) $panstwo["id"]
                            )
                                ? "selected"
                                : ""
                            ?>
                        >
                            <?= h(
                                $panstwo["nazwa_panstwa"]
                            ) ?>
                        </option>

                    <?php endwhile; ?>

                </select>

                <small class="help-text">
                    Wybierz pañstwo, z którego pochodzi moneta.
                </small>

            </div>

            <!-- WALUTA -->

            <div class="form-group">

                <label for="waluta">
                    Waluta
                </label>

                <input
                    type="text"
                    id="waluta"
                    name="waluta"
                    maxlength="255"
                    required
                    placeholder="Na przyk³ad: z³oty"
                    value="<?= h(
                        $_POST["waluta"] ?? ""
                    ) ?>"
                >

            </div>

            <!-- NOMINA£ -->

            <div class="form-group">

                <label for="nominal">
                    Nomina³
                </label>

                <input
                    type="text"
                    id="nominal"
                    name="nominal"
                    maxlength="255"
                    required
                    placeholder="Na przyk³ad: 5 z³otych"
                    value="<?= h(
                        $_POST["nominal"] ?? ""
                    ) ?>"
                >

            </div>

            <!-- WALUTA OBIEGOWA -->

            <div class="form-group">

                <label for="waluta_obiegowa">
                    Waluta obiegowa
                </label>

                <input
                    type="text"
                    id="waluta_obiegowa"
                    name="waluta_obiegowa"
                    maxlength="255"
                    required
                    placeholder="Na przyk³ad: tak"
                    value="<?= h(
                        $_POST["waluta_obiegowa"] ?? ""
                    ) ?>"
                >

            </div>

            <!-- WALUTA KOLEKCJONERSKA -->

            <div class="form-group">

                <label for="waluta_kolekcjonerska">
                    Waluta kolekcjonerska
                </label>

                <input
                    type="text"
                    id="waluta_kolekcjonerska"
                    name="waluta_kolekcjonerska"
                    maxlength="255"
                    required
                    placeholder="Na przyk³ad: nie"
                    value="<?= h(
                        $_POST[
                            "waluta_kolekcjonerska"
                        ] ?? ""
                    ) ?>"
                >

            </div>

            <!-- ROK BICIA -->

            <div class="form-group full">

                <label for="rok_bicia">
                    Rok bicia
                </label>

                <input
                    type="number"
                    id="rok_bicia"
                    name="rok_bicia"
                    step="1"
                    required
                    placeholder="Na przyk³ad: 2025"
                    value="<?= h(
                        $_POST["rok_bicia"] ?? ""
                    ) ?>"
                >

            </div>

            <!-- ZDJÊCIE -->

            <div class="form-group full">

                <label for="zdjecie">
                    Zdjêcie monety
                </label>

                <input
                    type="file"
                    id="zdjecie"
                    name="zdjecie"
                    accept="image/jpeg,image/png,image/webp"
                >

                <small class="help-text">
                    Dozwolone formaty: JPG, PNG i WEBP.
                    Maksymalny rozmiar zdjêcia: 5 MB.
                </small>

            </div>

            <!-- HISTORIA WALUTY -->

            <div class="form-group full">

                <label for="historia_waluty">
                    Historia waluty
                </label>

                <textarea
                    id="historia_waluty"
                    name="historia_waluty"
                    required
                    placeholder="Opisz pochodzenie oraz historiê waluty..."
                ><?= h(
                    $_POST["historia_waluty"] ?? ""
                ) ?></textarea>

            </div>

            <!-- HISTORIA JEDNOSTKI MONETARNEJ -->

            <div class="form-group full">

                <label
                    for="historia_jednostki_monetarnej"
                >
                    Historia jednostki monetarnej
                </label>

                <textarea
                    id="historia_jednostki_monetarnej"
                    name="historia_jednostki_monetarnej"
                    required
                    placeholder="Opisz historiê jednostki monetarnej..."
                ><?= h(
                    $_POST[
                        "historia_jednostki_monetarnej"
                    ] ?? ""
                ) ?></textarea>

            </div>

        </div>

        <div class="buttons">

            <button
                type="submit"
                <?= $liczbaPanstw === 0
                    ? "disabled"
                    : ""
                ?>
            >
                ?? Dodaj monetê
            </button>

            <a
                class="back-button"
                href="<?= h(
                    $panelPowrotny
                ) ?>#monety"
            >
                ‹ Powrót do panelu
            </a>

        </div>

    </form>

</main>

</body>
</html>

<?php

mysqli_free_result($panstwaResult);
mysqli_close($conn);

?>