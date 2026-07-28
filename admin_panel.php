<?php

session_start();

if (
    !isset($_SESSION["user"]) ||
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "admin"
) {
    header("Location: index.php");
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "db_connect.php";

mysqli_set_charset($conn, "utf8mb4");

/* =========================================
   FUNKCJE POMOCNICZE
========================================= */

function h($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        "UTF-8"
    );
}

function skrocTekst(string $tekst, int $limit = 140): string
{
    $tekst = trim($tekst);

    if ($tekst === "") {
        return "Brak danych";
    }

    if (function_exists("mb_strlen") && function_exists("mb_substr")) {
        return mb_strlen($tekst, "UTF-8") > $limit
            ? mb_substr($tekst, 0, $limit, "UTF-8") . "..."
            : $tekst;
    }

    return strlen($tekst) > $limit
        ? substr($tekst, 0, $limit) . "..."
        : $tekst;
}

/* =========================================
   TOKEN CSRF
========================================= */

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

/* =========================================
   ZAPIS MODYFIKACJI
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["csrf_token"], $csrfToken)
    ) {
        die("Nieprawidłowy token formularza.");
    }

    $action = $_POST["action"] ?? "";

    /* -----------------------------------------
       MODYFIKOWANIE PAŃSTWA
    ----------------------------------------- */

    if ($action === "update_panstwo") {

        $idPanstwa = (int) ($_POST["id"] ?? 0);
        $nazwaPanstwa = trim($_POST["nazwa_panstwa"] ?? "");
        $historia = trim($_POST["historia"] ?? "");

        if ($idPanstwa <= 0) {
            die("Nieprawidłowy identyfikator państwa.");
        }

        if ($nazwaPanstwa === "") {
            die("Nazwa państwa jest wymagana.");
        }

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE panstwo
             SET nazwa_panstwa = ?, historia = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $nazwaPanstwa,
            $historia,
            $idPanstwa
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: admin_panel.php?success=panstwo#panstwa");
        exit;
    }

    /* -----------------------------------------
       MODYFIKOWANIE MONETY
    ----------------------------------------- */

    if ($action === "update_coin") {

        $idMonety = (int) ($_POST["id"] ?? 0);
        $idPanstwa = (int) ($_POST["id_panstwo"] ?? 0);

        $waluta = trim($_POST["waluta"] ?? "");
        $walutaObiegowa = trim($_POST["waluta_obiegowa"] ?? "");
        $walutaKolekcjonerska = trim(
            $_POST["waluta_kolekcjonerska"] ?? ""
        );
        $nominal = trim($_POST["nominal"] ?? "");
        $rokBiciaTekst = trim($_POST["rok_bicia"] ?? "");
        $historiaWaluty = trim($_POST["historia_waluty"] ?? "");
        $historiaJednostki = trim(
            $_POST["historia_jednostki_monetarnej"] ?? ""
        );

        if ($idMonety <= 0) {
            die("Nieprawidłowy identyfikator monety.");
        }

        if ($idPanstwa <= 0) {
            die("Wybierz państwo.");
        }

        if ($waluta === "") {
            die("Nazwa waluty jest wymagana.");
        }

        if ($nominal === "") {
            die("Nominał jest wymagany.");
        }

        if (
            $rokBiciaTekst === "" ||
            filter_var($rokBiciaTekst, FILTER_VALIDATE_INT) === false
        ) {
            die("Podaj prawidłowy rok bicia.");
        }

        $rokBicia = (int) $rokBiciaTekst;

        $stmt = mysqli_prepare(
            $conn,
            "UPDATE coin
             SET
                id_panstwo = ?,
                waluta = ?,
                `waluta obiegowa` = ?,
                `waluta kolekcjonerska` = ?,
                nominal = ?,
                rok_bicia = ?,
                historia_waluty = ?,
                `historia_jednostki _monetarnej` = ?
             WHERE id = ?"
        );

        mysqli_stmt_bind_param(
            $stmt,
            "issssissi",
            $idPanstwa,
            $waluta,
            $walutaObiegowa,
            $walutaKolekcjonerska,
            $nominal,
            $rokBicia,
            $historiaWaluty,
            $historiaJednostki,
            $idMonety
        );

        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: admin_panel.php?success=coin#monety");
        exit;
    }
}

/* =========================================
   POBRANIE PAŃSTW
========================================= */

$panstwaResult = mysqli_query(
    $conn,
    "SELECT id, nazwa_panstwa, historia
     FROM panstwo
     ORDER BY nazwa_panstwa ASC"
);

$panstwa = [];

while ($row = mysqli_fetch_assoc($panstwaResult)) {
    $panstwa[] = $row;
}

mysqli_free_result($panstwaResult);

/* =========================================
   POBRANIE MONET

   Kolumna w bazie nazywa się: nominal
========================================= */

$monetyResult = mysqli_query(
    $conn,
    "SELECT
        coin.id,
        coin.id_panstwo,
        coin.waluta,
        coin.`waluta obiegowa` AS waluta_obiegowa,
        coin.`waluta kolekcjonerska`
            AS waluta_kolekcjonerska,
        coin.nominal,
        coin.rok_bicia,
        coin.historia_waluty,
        coin.`historia_jednostki _monetarnej`
            AS historia_jednostki_monetarnej,
        panstwo.nazwa_panstwa
     FROM coin
     LEFT JOIN panstwo
        ON panstwo.id = coin.id_panstwo
     ORDER BY coin.rok_bicia DESC, coin.id DESC"
);

$monety = [];

while ($row = mysqli_fetch_assoc($monetyResult)) {
    $monety[] = $row;
}

mysqli_free_result($monetyResult);

/* =========================================
   WYBRANE PAŃSTWO DO EDYCJI
========================================= */

$edytowanePanstwo = null;

$idEdycjiPanstwa = isset($_GET["edit_panstwo"])
    ? (int) $_GET["edit_panstwo"]
    : 0;

if ($idEdycjiPanstwa > 0) {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT id, nazwa_panstwa, historia
         FROM panstwo
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $idEdycjiPanstwa);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $edytowanePanstwo = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}

/* =========================================
   WYBRANA MONETA DO EDYCJI

   Kolumna w bazie nazywa się: nominal
========================================= */

$edytowanaMoneta = null;

$idEdycjiMonety = isset($_GET["edit_coin"])
    ? (int) $_GET["edit_coin"]
    : 0;

if ($idEdycjiMonety > 0) {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT
            id,
            id_panstwo,
            waluta,
            `waluta obiegowa` AS waluta_obiegowa,
            `waluta kolekcjonerska`
                AS waluta_kolekcjonerska,
            nominal,
            rok_bicia,
            historia_waluty,
            `historia_jednostki _monetarnej`
                AS historia_jednostki_monetarnej
         FROM coin
         WHERE id = ?"
    );

    mysqli_stmt_bind_param($stmt, "i", $idEdycjiMonety);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $edytowanaMoneta = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}

$username = $_SESSION["user"] ?? "Administrator";

?>
<!DOCTYPE html>
<html lang="pl">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel administratora – Katalog Monet</title>

    <meta name="robots" content="noindex, nofollow">

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
            font-family: Arial, Helvetica, sans-serif;
            color: #222222;
            background: #eef2f6;
            line-height: 1.6;
        }

        .site-header {
            padding: 22px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(135deg, #101b2d, #244f79);
        }

        .site-header h1 {
            margin: 0;
            font-size: clamp(1.8rem, 5vw, 2.8rem);
        }

        .site-header p {
            margin: 8px 0 0;
            color: #d9e6f2;
        }

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
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
        }

        .top-navigation a {
            padding: 10px 15px;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            background: #1e88e5;
            border-radius: 7px;
        }

        .top-navigation a:hover {
            background: #1565c0;
        }

        .top-navigation .logout {
            background: #c62828;
        }

        .top-navigation .logout:hover {
            background: #9f1f1f;
        }

        main {
            width: min(1200px, calc(100% - 30px));
            margin: 30px auto 50px;
        }

        .message {
            margin-bottom: 25px;
            padding: 15px 18px;
            color: #155724;
            background: #d4edda;
            border: 1px solid #b7dfc1;
            border-radius: 8px;
        }

        .section {
            margin-bottom: 35px;
            padding: 25px;
            background: #ffffff;
            border-radius: 13px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            scroll-margin-top: 90px;
        }

        .section-header {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 22px;
        }

        .section-header h2 {
            margin: 0;
            color: #1d3557;
            font-size: clamp(1.5rem, 4vw, 2rem);
        }

        .add-button {
            display: inline-block;
            padding: 11px 17px;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            background: #2e7d32;
            border-radius: 7px;
        }

        .add-button:hover {
            background: #1b5e20;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
            border: 1px solid #e3e7eb;
            border-radius: 9px;
        }

        table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px;
            text-align: left;
            vertical-align: top;
            border-bottom: 1px solid #e8ebee;
        }

        th {
            color: #ffffff;
            background: #244f79;
        }

        tbody tr:hover {
            background: #f7f9fc;
        }

        .preview {
            max-width: 420px;
            color: #555555;
        }

        .edit-button {
            display: inline-block;
            padding: 8px 13px;
            color: #ffffff;
            font-weight: 700;
            text-decoration: none;
            background: #ef6c00;
            border-radius: 6px;
            white-space: nowrap;
        }

        .edit-button:hover {
            background: #d65f00;
        }

        .form-card {
            margin-bottom: 30px;
            padding: 24px;
            background: #f7f9fc;
            border: 1px solid #dce3e9;
            border-radius: 10px;
        }

        .form-card h3 {
            margin: 0 0 20px;
            color: #244f79;
            font-size: 1.4rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        label {
            display: block;
            margin-bottom: 6px;
            color: #244f79;
            font-weight: 700;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 11px 12px;
            font: inherit;
            color: #222222;
            background: #ffffff;
            border: 1px solid #bcc5ce;
            border-radius: 7px;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: 3px solid rgba(30, 136, 229, 0.2);
            border-color: #1e88e5;
        }

        textarea {
            min-height: 150px;
            resize: vertical;
            line-height: 1.6;
        }

        .large-textarea {
            min-height: 230px;
        }

        .form-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        button,
        .cancel-button {
            display: inline-block;
            padding: 11px 19px;
            font: inherit;
            font-weight: 700;
            border: none;
            border-radius: 7px;
            cursor: pointer;
        }

        button {
            color: #ffffff;
            background: #2e7d32;
        }

        button:hover {
            background: #1b5e20;
        }

        .cancel-button {
            color: #ffffff;
            text-decoration: none;
            background: #6c757d;
        }

        .cancel-button:hover {
            background: #545b62;
        }

        .empty-message {
            padding: 20px;
            color: #666666;
            text-align: center;
            background: #f7f7f7;
            border-radius: 8px;
        }

        @media (max-width: 750px) {

            main {
                width: calc(100% - 20px);
                margin-top: 20px;
            }

            .section {
                padding: 18px 14px;
            }

            .form-card {
                padding: 18px 14px;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-group.full {
                grid-column: auto;
            }

            .top-navigation a {
                flex: 1 1 140px;
                text-align: center;
            }
        }

        @media (max-width: 480px) {

            .section-header {
                align-items: stretch;
            }

            .add-button,
            button,
            .cancel-button {
                width: 100%;
                text-align: center;
            }
        }

    </style>

</head>

<body>

<header class="site-header">

    <h1>⚙️ Panel administratora</h1>

    <p>
        Zalogowany użytkownik:
        <strong><?= h($username) ?></strong>
    </p>

</header>

<nav class="top-navigation" aria-label="Menu administratora">

    <a href="#panstwa">
        🌍 Państwa
    </a>

    <a href="#monety">
        🪙 Monety
    </a>

    <a href="kontynenty.php">
        📚 Katalog
    </a>

    <a class="logout" href="logout.php">
        🚪 Wyloguj
    </a>

</nav>

<main>

    <?php if (
        isset($_GET["success"]) &&
        $_GET["success"] === "panstwo"
    ): ?>

        <div class="message">
            Państwo zostało prawidłowo zmodyfikowane.
        </div>

    <?php endif; ?>

    <?php if (
        isset($_GET["success"]) &&
        $_GET["success"] === "coin"
    ): ?>

        <div class="message">
            Moneta została prawidłowo zmodyfikowana.
        </div>

    <?php endif; ?>

    <?php if (isset($_GET["dodano_monete"])): ?>

        <div class="message">
            Moneta została prawidłowo dodana.
        </div>

    <?php endif; ?>

    <?php if (isset($_GET["dodano_panstwo"])): ?>

        <div class="message">
            Państwo zostało prawidłowo dodane.
        </div>

    <?php endif; ?>

    <!-- PAŃSTWA -->

    <section class="section" id="panstwa">

        <div class="section-header">

            <h2>🌍 Zarządzanie państwami</h2>

            <a class="add-button" href="add_panstwo.php">
                ➕ Dodaj państwo
            </a>

        </div>

        <?php if ($edytowanePanstwo): ?>

            <div class="form-card">

                <h3>
                    Edytowanie państwa:
                    <?= h($edytowanePanstwo["nazwa_panstwa"]) ?>
                </h3>

                <form method="post" action="admin_panel.php">

                    <input
                        type="hidden"
                        name="action"
                        value="update_panstwo"
                    >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $edytowanePanstwo["id"] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= h($_SESSION["csrf_token"]) ?>"
                    >

                    <div class="form-group">

                        <label for="nazwa_panstwa">
                            Nazwa państwa
                        </label>

                        <input
                            type="text"
                            id="nazwa_panstwa"
                            name="nazwa_panstwa"
                            maxlength="255"
                            required
                            value="<?= h(
                                $edytowanePanstwo["nazwa_panstwa"]
                            ) ?>"
                        >

                    </div>

                    <div class="form-group">

                        <label for="historia">
                            Historia państwa
                        </label>

                        <textarea
                            class="large-textarea"
                            id="historia"
                            name="historia"
                            placeholder="Wpisz historię państwa..."
                        ><?= h(
                            $edytowanePanstwo["historia"] ?? ""
                        ) ?></textarea>

                    </div>

                    <div class="form-buttons">

                        <button type="submit">
                            💾 Zapisz państwo
                        </button>

                        <a
                            class="cancel-button"
                            href="admin_panel.php#panstwa"
                        >
                            Anuluj
                        </a>

                    </div>

                </form>

            </div>

        <?php endif; ?>

        <?php if (count($panstwa) > 0): ?>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nazwa państwa</th>
                            <th>Historia</th>
                            <th>Operacje</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($panstwa as $panstwo): ?>

                            <tr>

                                <td>
                                    <?= (int) $panstwo["id"] ?>
                                </td>

                                <td>
                                    <?= h($panstwo["nazwa_panstwa"]) ?>
                                </td>

                                <td class="preview">
                                    <?= h(
                                        skrocTekst(
                                            (string) (
                                                $panstwo["historia"] ?? ""
                                            )
                                        )
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        class="edit-button"
                                        href="admin_panel.php?edit_panstwo=<?= (int) $panstwo["id"] ?>#panstwa"
                                    >
                                        ✏️ Edytuj
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty-message">
                Nie dodano jeszcze żadnego państwa.
            </div>

        <?php endif; ?>

    </section>

    <!-- MONETY -->

    <section class="section" id="monety">

        <div class="section-header">

            <h2>🪙 Zarządzanie monetami</h2>

            <a class="add-button" href="add_coin.php">
                ➕ Dodaj monetę
            </a>

        </div>

        <?php if ($edytowanaMoneta): ?>

            <div class="form-card">

                <h3>
                    Edytowanie monety ID:
                    <?= (int) $edytowanaMoneta["id"] ?>
                </h3>

                <form method="post" action="admin_panel.php">

                    <input
                        type="hidden"
                        name="action"
                        value="update_coin"
                    >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $edytowanaMoneta["id"] ?>"
                    >

                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= h($_SESSION["csrf_token"]) ?>"
                    >

                    <div class="form-grid">

                        <div class="form-group">

                            <label for="id_panstwo">
                                Państwo
                            </label>

                            <select
                                id="id_panstwo"
                                name="id_panstwo"
                                required
                            >

                                <option value="">
                                    Wybierz państwo
                                </option>

                                <?php foreach ($panstwa as $panstwo): ?>

                                    <option
                                        value="<?= (int) $panstwo["id"] ?>"
                                        <?= (
                                            (int) $edytowanaMoneta["id_panstwo"]
                                            ===
                                            (int) $panstwo["id"]
                                        ) ? "selected" : "" ?>
                                    >
                                        <?= h(
                                            $panstwo["nazwa_panstwa"]
                                        ) ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                        <div class="form-group">

                            <label for="rok_bicia">
                                Rok bicia
                            </label>

                            <input
                                type="number"
                                id="rok_bicia"
                                name="rok_bicia"
                                step="1"
                                required
                                value="<?= h(
                                    $edytowanaMoneta["rok_bicia"]
                                ) ?>"
                            >

                        </div>

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
                                value="<?= h(
                                    $edytowanaMoneta["waluta"]
                                ) ?>"
                            >

                        </div>

                        <div class="form-group">

                            <label for="nominal">
                                Nominał
                            </label>

                            <input
                                type="text"
                                id="nominal"
                                name="nominal"
                                maxlength="255"
                                required
                                value="<?= h(
                                    $edytowanaMoneta["nominal"]
                                ) ?>"
                            >

                        </div>

                        <div class="form-group">

                            <label for="waluta_obiegowa">
                                Waluta obiegowa
                            </label>

                            <input
                                type="text"
                                id="waluta_obiegowa"
                                name="waluta_obiegowa"
                                maxlength="255"
                                value="<?= h(
                                    $edytowanaMoneta["waluta_obiegowa"]
                                ) ?>"
                            >

                        </div>

                        <div class="form-group">

                            <label for="waluta_kolekcjonerska">
                                Waluta kolekcjonerska
                            </label>

                            <input
                                type="text"
                                id="waluta_kolekcjonerska"
                                name="waluta_kolekcjonerska"
                                maxlength="255"
                                value="<?= h(
                                    $edytowanaMoneta[
                                        "waluta_kolekcjonerska"
                                    ]
                                ) ?>"
                            >

                        </div>

                        <div class="form-group full">

                            <label for="historia_waluty">
                                Historia waluty
                            </label>

                            <textarea
                                id="historia_waluty"
                                name="historia_waluty"
                                placeholder="Wpisz historię waluty..."
                            ><?= h(
                                $edytowanaMoneta["historia_waluty"]
                            ) ?></textarea>

                        </div>

                        <div class="form-group full">

                            <label for="historia_jednostki_monetarnej">
                                Historia jednostki monetarnej
                            </label>

                            <textarea
                                id="historia_jednostki_monetarnej"
                                name="historia_jednostki_monetarnej"
                                placeholder="Wpisz historię jednostki monetarnej..."
                            ><?= h(
                                $edytowanaMoneta[
                                    "historia_jednostki_monetarnej"
                                ]
                            ) ?></textarea>

                        </div>

                    </div>

                    <div class="form-buttons">

                        <button type="submit">
                            💾 Zapisz monetę
                        </button>

                        <a
                            class="cancel-button"
                            href="admin_panel.php#monety"
                        >
                            Anuluj
                        </a>

                    </div>

                </form>

            </div>

        <?php endif; ?>

        <?php if (count($monety) > 0): ?>

            <div class="table-wrapper">

                <table>

                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Państwo</th>
                            <th>Waluta</th>
                            <th>Nominał</th>
                            <th>Rok bicia</th>
                            <th>Operacje</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php foreach ($monety as $moneta): ?>

                            <tr>

                                <td>
                                    <?= (int) $moneta["id"] ?>
                                </td>

                                <td>
                                    <?= h(
                                        $moneta["nazwa_panstwa"]
                                        ?? "Brak państwa"
                                    ) ?>
                                </td>

                                <td>
                                    <?= h(
                                        $moneta["waluta"]
                                        ?? "Brak danych"
                                    ) ?>
                                </td>

                                <td>
                                    <?= h(
                                        $moneta["nominal"]
                                        ?? "Brak danych"
                                    ) ?>
                                </td>

                                <td>
                                    <?= h(
                                        $moneta["rok_bicia"]
                                        ?? "Brak danych"
                                    ) ?>
                                </td>

                                <td>

                                    <a
                                        class="edit-button"
                                        href="admin_panel.php?edit_coin=<?= (int) $moneta["id"] ?>#monety"
                                    >
                                        ✏️ Edytuj
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php else: ?>

            <div class="empty-message">
                Nie dodano jeszcze żadnych monet.
            </div>

        <?php endif; ?>

    </section>

</main>

</body>
</html>

<?php

mysqli_close($conn);

?>
