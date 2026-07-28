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

$blad = "";
$nazwaPanstwa = "";
$historia = "";
$idKontynentu = "";

/* Token zabezpieczający formularz */

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

/* Obsługa formularza */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        die("Nieprawidłowy token formularza.");
    }

    $idKontynentu = trim($_POST["id_kontynent"] ?? "");
    $nazwaPanstwa = trim($_POST["nazwa_panstwa"] ?? "");
    $historia = trim($_POST["historia"] ?? "");

    if (
        $idKontynentu === "" ||
        filter_var($idKontynentu, FILTER_VALIDATE_INT) === false ||
        (int) $idKontynentu <= 0
    ) {
        $blad = "Podaj prawidłowe ID kontynentu.";
    } elseif ($nazwaPanstwa === "") {
        $blad = "Podaj nazwę państwa.";
    } elseif ($historia === "") {
        $blad = "Podaj historię państwa.";
    } else {

        $idKontynentu = (int) $idKontynentu;

        /* Sprawdzenie, czy państwo już istnieje */

        $stmtCheck = mysqli_prepare(
            $conn,
            "SELECT id
             FROM panstwo
             WHERE id_kontynent = ?
               AND nazwa_panstwa = ?
             LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $stmtCheck,
            "is",
            $idKontynentu,
            $nazwaPanstwa
        );

        mysqli_stmt_execute($stmtCheck);

        $wynikCheck = mysqli_stmt_get_result($stmtCheck);

        if (mysqli_num_rows($wynikCheck) > 0) {

            $blad = "Takie państwo już istnieje na tym kontynencie.";

            mysqli_stmt_close($stmtCheck);

        } else {

            mysqli_stmt_close($stmtCheck);

            /* Dodawanie państwa */

            $stmtInsert = mysqli_prepare(
                $conn,
                "INSERT INTO panstwo
                (
                    id_kontynent,
                    nazwa_panstwa,
                    historia
                )
                VALUES (?, ?, ?)"
            );

            mysqli_stmt_bind_param(
                $stmtInsert,
                "iss",
                $idKontynentu,
                $nazwaPanstwa,
                $historia
            );

            mysqli_stmt_execute($stmtInsert);

            $noweIdPanstwa = mysqli_insert_id($conn);

            mysqli_stmt_close($stmtInsert);
            mysqli_close($conn);

            header(
                "Location: panstwo.php?id=" .
                $noweIdPanstwa
            );
            exit;
        }
    }
}

function h($value): string
{
    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
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

    <title>Dodaj państwo – panel administratora</title>

    <meta name="robots" content="noindex, nofollow">

    <style>

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            background: #eef2f6;
            line-height: 1.6;
        }

        header {
            padding: 24px 20px;
            color: #fff;
            text-align: center;
            background: linear-gradient(
                135deg,
                #101b2d,
                #244f79
            );
        }

        header h1 {
            margin: 0;
            font-size: clamp(1.7rem, 5vw, 2.6rem);
        }

        header p {
            margin: 8px 0 0;
            color: #d9e6f2;
        }

        .container {
            width: min(760px, calc(100% - 30px));
            margin: 35px auto;
            padding: 30px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.12);
        }

        h2 {
            margin: 0 0 25px;
            color: #1d3557;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            color: #244f79;
            font-weight: 700;
        }

        input,
        textarea {
            width: 100%;
            padding: 12px 13px;
            font: inherit;
            border: 1px solid #bdc6cf;
            border-radius: 7px;
        }

        input:focus,
        textarea:focus {
            outline: 3px solid rgba(30, 136, 229, 0.2);
            border-color: #1e88e5;
        }

        textarea {
            min-height: 240px;
            resize: vertical;
            line-height: 1.6;
        }

        .error {
            margin-bottom: 22px;
            padding: 14px 16px;
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f1b0b7;
            border-radius: 7px;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 25px;
        }

        button,
        .back {
            display: inline-flex;
            justify-content: center;
            align-items: center;
            min-height: 46px;
            padding: 12px 20px;
            font: inherit;
            font-weight: 700;
            border: none;
            border-radius: 7px;
        }

        button {
            color: #fff;
            background: #2e7d32;
            cursor: pointer;
        }

        button:hover {
            background: #1b5e20;
        }

        .back {
            color: #fff;
            text-decoration: none;
            background: #6c757d;
        }

        .back:hover {
            background: #545b62;
        }

        @media (max-width: 600px) {

            .container {
                width: calc(100% - 20px);
                margin: 20px auto;
                padding: 20px 16px;
            }

            .buttons {
                flex-direction: column;
            }

            button,
            .back {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<header>

    <h1>?? Katalog Monet Świata</h1>

    <p>Panel administratora</p>

</header>

<main class="container">

    <h2>? Dodaj państwo</h2>

    <?php if ($blad !== ""): ?>

        <div class="error" role="alert">
            <?= h($blad) ?>
        </div>

    <?php endif; ?>

    <form method="post" action="add_panstwo.php">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= h($_SESSION["csrf_token"]) ?>"
        >

        <div class="form-group">

            <label for="id_kontynent">
                ID kontynentu
            </label>

            <input
                type="number"
                id="id_kontynent"
                name="id_kontynent"
                min="1"
                step="1"
                required
                value="<?= h($idKontynentu) ?>"
            >

        </div>

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
                placeholder="Na przykład: Polska"
                value="<?= h($nazwaPanstwa) ?>"
            >

        </div>

        <div class="form-group">

            <label for="historia">
                Historia państwa
            </label>

            <textarea
                id="historia"
                name="historia"
                required
                placeholder="Wpisz historię państwa..."
            ><?= h($historia) ?></textarea>

        </div>

        <div class="buttons">

            <button type="submit">
                ?? Dodaj państwo
            </button>

            <a
                class="back"
                href="admin_panel.php#panstwa"
            >
                ‹ Powrót do panelu
            </a>

        </div>

    </form>

</main>

</body>
</html>