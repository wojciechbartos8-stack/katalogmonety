<?php

require_once "db_connect.php";

$id = isset($_GET["id"]) ? (int) $_GET["id"] : 0;

if ($id <= 0) {
    die("Nieprawidłowy identyfikator państwa.");
}

/* =========================================
   POBRANIE PAŃSTWA
========================================= */

$stmtPanstwo = mysqli_prepare(
    $conn,
    "SELECT id, nazwa_panstwa, historia
     FROM panstwo
     WHERE id = ?"
);

mysqli_stmt_bind_param($stmtPanstwo, "i", $id);
mysqli_stmt_execute($stmtPanstwo);

$wynikPanstwo = mysqli_stmt_get_result($stmtPanstwo);

if (mysqli_num_rows($wynikPanstwo) === 0) {
    die("Nie znaleziono państwa.");
}

$p = mysqli_fetch_assoc($wynikPanstwo);

/* =========================================
   POBRANIE MONET PAŃSTWA
========================================= */

$stmtMonety = mysqli_prepare(
    $conn,
    "SELECT *
     FROM coin
     WHERE id_panstwo = ?
     ORDER BY rok_bicia DESC"
);

mysqli_stmt_bind_param($stmtMonety, "i", $id);
mysqli_stmt_execute($stmtMonety);

$monety = mysqli_stmt_get_result($stmtMonety);

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
        <?= htmlspecialchars($p["nazwa_panstwa"], ENT_QUOTES, "UTF-8") ?>
        – Katalog Monet Świata
    </title>

    <meta
        name="description"
        content="Historia państwa <?= htmlspecialchars($p["nazwa_panstwa"], ENT_QUOTES, "UTF-8") ?> oraz katalog pochodzących z niego monet."
    >

    <style>

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f2f4f7;
            color: #222222;
            line-height: 1.6;
        }

        header {
            padding: 18px 20px;
            color: #ffffff;
            text-align: center;
            background: linear-gradient(135deg, #17243d, #244f79);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.18);
        }

        header h2 {
            margin: 0;
            color: #ffffff;
            font-size: clamp(1.35rem, 4vw, 2rem);
        }

        main {
            width: min(1100px, calc(100% - 30px));
            margin: 0 auto;
            padding: 25px 0 45px;
        }

        .page-title {
            margin: 10px 0 25px;
            color: #1d3557;
            font-size: clamp(2rem, 6vw, 3rem);
            text-align: center;
        }

        .section-title {
            margin: 0 0 18px;
            color: #244f79;
            font-size: clamp(1.4rem, 4vw, 2rem);
        }

        .box {
            width: 100%;
            margin: 0 auto 35px;
            padding: 25px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 14px rgba(0, 0, 0, 0.12);
        }

        .history {
            font-size: 1.05rem;
            overflow-wrap: anywhere;
        }

        .history p {
            margin-top: 0;
        }

        .empty-message {
            padding: 15px;
            color: #666666;
            font-style: italic;
            background: #f7f7f7;
            border-left: 4px solid #b0b0b0;
            border-radius: 5px;
        }

        .coins-title {
            margin: 40px 0 25px;
            color: #1d3557;
            text-align: center;
            font-size: clamp(1.7rem, 5vw, 2.4rem);
        }

        .coins-list {
            display: grid;
            grid-template-columns: repeat(
                auto-fit,
                minmax(min(100%, 360px), 1fr)
            );
            gap: 22px;
        }

        .coin {
            padding: 22px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0, 0, 0, 0.12);
        }

        .coin h3 {
            margin: 0 0 18px;
            color: #244f79;
            font-size: 1.35rem;
        }

        .field {
            padding: 10px 0;
            border-bottom: 1px solid #eeeeee;
            overflow-wrap: anywhere;
        }

        .field:last-child {
            border-bottom: none;
        }

        .field-name {
            display: block;
            margin-bottom: 4px;
            color: #444444;
            font-weight: 700;
        }

        .field-value {
            color: #222222;
        }

        .back {
            display: block;
            width: fit-content;
            min-width: 170px;
            margin: 35px auto 0;
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
                width: min(100% - 20px, 1100px);
                padding-top: 18px;
            }

            .box,
            .coin {
                padding: 18px;
                border-radius: 9px;
            }

            .page-title {
                margin-bottom: 18px;
            }

            .coins-list {
                grid-template-columns: 1fr;
            }

            .back {
                width: 100%;
            }
        }

    </style>

</head>

<body>

<header>
    <h2>?? Katalog Monet Świata</h2>
</header>

<main>

    <h1 class="page-title">
        ?? <?= htmlspecialchars(
            $p["nazwa_panstwa"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>
    </h1>

    <!-- HISTORIA PAŃSTWA -->

    <section class="box" aria-labelledby="historia-title">

        <h2 class="section-title" id="historia-title">
            ?? Historia państwa
        </h2>

        <div class="history">

            <?php if (!empty(trim($p["historia"] ?? ""))): ?>

                <?= nl2br(
                    htmlspecialchars(
                        $p["historia"],
                        ENT_QUOTES,
                        "UTF-8"
                    )
                ) ?>

            <?php else: ?>

                <p class="empty-message">
                    Brak opisu historii tego państwa.
                </p>

            <?php endif; ?>

        </div>

    </section>

    <!-- MONETY -->

    <section aria-labelledby="monety-title">

        <h2 class="coins-title" id="monety-title">
            ?? Monety
        </h2>

        <?php if (mysqli_num_rows($monety) > 0): ?>

            <div class="coins-list">

                <?php while ($row = mysqli_fetch_assoc($monety)): ?>

                    <article class="coin">

                        <h3>
                            ?? Moneta ID:
                            <?= (int) $row["id"] ?>
                        </h3>

                        <?php foreach ($row as $key => $value): ?>

                            <?php

                            if (
                                $key === "id" ||
                                $key === "id_panstwo"
                            ) {
                                continue;
                            }

                            $nazwaPola = ucwords(
                                str_replace("_", " ", $key)
                            );

                            ?>

                            <div class="field">

                                <span class="field-name">
                                    <?= htmlspecialchars(
                                        $nazwaPola,
                                        ENT_QUOTES,
                                        "UTF-8"
                                    ) ?>:
                                </span>

                                <div class="field-value">

                                    <?php if (
                                        $value !== null &&
                                        trim((string) $value) !== ""
                                    ): ?>

                                        <?= nl2br(
                                            htmlspecialchars(
                                                (string) $value,
                                                ENT_QUOTES,
                                                "UTF-8"
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

            <div class="box">
                <p class="empty-message">
                    Nie dodano jeszcze monet dla tego państwa.
                </p>
            </div>

        <?php endif; ?>

    </section>

    <a class="back" href="kontynenty.php">
        ‹ Powrót do katalogu
    </a>

</main>

</body>
</html>

<?php

mysqli_stmt_close($stmtPanstwo);
mysqli_stmt_close($stmtMonety);
mysqli_close($conn);

?>