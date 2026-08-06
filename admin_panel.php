<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli(
        'localhost',
        'root',
        'mysql',
        'katalogmonety'
    );

    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    die('Nie udało się połączyć z bazą danych.');
}

function h(mixed $tekst): string
{
    return htmlspecialchars(
        (string)($tekst ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );
}

function urlZdjecia(?string $sciezka): ?string
{
    if ($sciezka === null || trim($sciezka) === '') {
        return null;
    }

    $sciezka = rawurldecode($sciezka);
    $sciezka = str_replace('\\', '/', $sciezka);
    $sciezka = trim($sciezka);

    $sciezka = preg_replace(
        '~^/?katalogmonety/~i',
        '',
        $sciezka
    );

    $sciezka = ltrim($sciezka, '/');

    $fragmenty = explode('/', $sciezka);
    $bezpieczneFragmenty = [];

    foreach ($fragmenty as $fragment) {
        if ($fragment === '' || $fragment === '.') {
            continue;
        }

        if ($fragment === '..') {
            return null;
        }

        $bezpieczneFragmenty[] = rawurlencode($fragment);
    }

    if (empty($bezpieczneFragmenty)) {
        return null;
    }

    return '/katalogmonety/' .
        implode('/', $bezpieczneFragmenty);
}

try {
    $result = $conn->query(
        "SELECT
            `id`,
            `id_panstwo`,
            `waluta`,
            `waluta obiegowa` AS `waluta_obiegowa`,
            `waluta kolekcjonerska`
                AS `waluta_kolekcjonerska`,
            `nominal`,
            `rok_bicia`,
            `historia_waluty`,
            `historia_jednostki _monetarnej`
                AS `historia_jednostki_monetarnej`,
            `zdjecie`
        FROM `coin`
        ORDER BY `id` DESC"
    );

    $monety = $result->fetch_all(MYSQLI_ASSOC);

} catch (mysqli_sql_exception $e) {
    die(
        'Błąd SQL: ' .
        h($e->getMessage())
    );
}

$liczbaMonet = count($monety);
?>

<!DOCTYPE html>
<html lang="pl">

<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Panel administratora – katalog monet</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            color: #263238;
            background: #eef2f7;
        }

        header {
            color: #ffffff;
            background: linear-gradient(135deg, #0b2148, #1f3b73);
        }

        .header-inner {
            width: 100%;
            max-width: 1550px;
            margin: 0 auto;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }

        .brand h1 {
            margin: 0;
            font-size: 27px;
        }

        .brand p {
            margin: 6px 0 0;
            color: #d9e3f3;
        }

        .header-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .button {
            display: inline-flex;
            min-height: 42px;
            padding: 10px 17px;
            align-items: center;
            justify-content: center;
            border: none;
            border-radius: 7px;
            color: #ffffff;
            background: #1e88e5;
            font-size: 15px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover {
            background: #1565c0;
        }

        .button-secondary {
            background: #546e7a;
        }

        .button-secondary:hover {
            background: #37474f;
        }

        .container {
            width: 100%;
            max-width: 1550px;
            margin: 0 auto;
            padding: 30px 20px;
        }

        .summary {
            margin-bottom: 22px;
            padding: 20px;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
        }

        .summary-number {
            margin: 0;
            color: #1f3b73;
            font-size: 34px;
            font-weight: 700;
        }

        .summary-label {
            margin: 5px 0 0;
            color: #607d8b;
        }

        .table-wrapper {
            overflow-x: auto;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.08);
        }

        table {
            width: 100%;
            min-width: 1550px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 13px;
            text-align: left;
            vertical-align: middle;
            border-bottom: 1px solid #e1e7ed;
        }

        th {
            color: #ffffff;
            background: #1f3b73;
            font-size: 13px;
        }

        tbody tr:hover {
            background: #f6f9fc;
        }

        .coin-image {
            position: relative;
            width: 100px;
            height: 100px;
            overflow: hidden;
            border: 1px solid #dce3ea;
            border-radius: 9px;
            background: #f1f4f7;
        }

        .coin-image img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: contain;
            background: #ffffff;
        }

        .no-image,
        .image-error {
            width: 100%;
            height: 100%;
            padding: 8px;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            text-align: center;
        }

        .no-image {
            display: flex;
            color: #78909c;
        }

        .image-error {
            display: none;
            position: absolute;
            inset: 0;
            color: #b71c1c;
            background: #ffebee;
        }

        .main-value {
            color: #1f3b73;
            font-weight: 700;
        }

        .long-text {
            min-width: 250px;
            max-width: 350px;
            color: #455a64;
            line-height: 1.5;
        }

        .path {
            min-width: 180px;
            max-width: 230px;
            overflow-wrap: anywhere;
            color: #607d8b;
            font-family: Consolas, monospace;
            font-size: 12px;
        }

        .empty {
            padding: 50px 20px;
            color: #607d8b;
            text-align: center;
        }

        footer {
            margin-top: 40px;
            padding: 20px;
            color: #d9e3f3;
            background: #0b2148;
            text-align: center;
        }

        @media (max-width: 700px) {
            .header-inner {
                align-items: stretch;
                flex-direction: column;
            }

            .header-buttons {
                flex-direction: column;
            }

            .button {
                width: 100%;
            }

            .container {
                padding: 20px 10px;
            }
        }
    </style>
</head>

<body>

<header>

    <div class="header-inner">

        <div class="brand">

            <h1>Panel administratora</h1>

            <p>
                Zarządzanie katalogiem monet
            </p>

        </div>

        <div class="header-buttons">

            <a
                href="add_coin.php"
                class="button"
            >
                Dodaj monetę
            </a>

            <a
                href="user_panel.php"
                class="button button-secondary"
            >
                Zobacz katalog
            </a>

            <a
                href="logout.php"
                class="button button-secondary"
            >
                Wyloguj
            </a>

        </div>

    </div>

</header>

<main class="container">

    <section class="summary">

        <p class="summary-number">
            <?= $liczbaMonet ?>
        </p>

        <p class="summary-label">
            Liczba monet zapisanych w tabeli coin
        </p>

    </section>

    <section class="table-wrapper">

        <?php if (empty($monety)): ?>

            <div class="empty">

                <h2>Brak monet</h2>

                <p>
                    W katalogu nie ma jeszcze żadnej monety.
                </p>

                <a
                    href="add_coin.php"
                    class="button"
                >
                    Dodaj pierwszą monetę
                </a>

            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>
                        <th>ID</th>
                        <th>Zdjęcie</th>
                        <th>ID państwa</th>
                        <th>Waluta</th>
                        <th>Waluta obiegowa</th>
                        <th>Waluta kolekcjonerska</th>
                        <th>Nominał</th>
                        <th>Rok bicia</th>
                        <th>Historia waluty</th>
                        <th>Historia jednostki monetarnej</th>
                        <th>Ścieżka zdjęcia</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach ($monety as $moneta): ?>

                    <?php
                    $zdjecie = urlZdjecia(
                        $moneta['zdjecie'] ?? null
                    );
                    ?>

                    <tr>

                        <td>
                            <?= (int)$moneta['id'] ?>
                        </td>

                        <td>

                            <div class="coin-image">

                                <?php if ($zdjecie !== null): ?>

                                    <img
                                        src="<?= h($zdjecie) ?>"
                                        alt="Zdjęcie monety"
                                        loading="lazy"
                                        onerror="
                                            this.style.display='none';
                                            this.nextElementSibling.style.display='flex';
                                        "
                                    >

                                    <div class="image-error">
                                        Nie znaleziono zdjęcia
                                    </div>

                                <?php else: ?>

                                    <div class="no-image">
                                        Brak zdjęcia
                                    </div>

                                <?php endif; ?>

                            </div>

                        </td>

                        <td>
                            <?= (int)$moneta['id_panstwo'] ?>
                        </td>

                        <td class="main-value">
                            <?= h($moneta['waluta']) ?>
                        </td>

                        <td>
                            <?= h($moneta['waluta_obiegowa']) ?>
                        </td>

                        <td>
                            <?= h($moneta['waluta_kolekcjonerska']) ?>
                        </td>

                        <td>
                            <?= h($moneta['nominal']) ?>
                        </td>

                        <td>
                            <?= (int)$moneta['rok_bicia'] ?>
                        </td>

                        <td class="long-text">
                            <?= nl2br(
                                h($moneta['historia_waluty'])
                            ) ?>
                        </td>

                        <td class="long-text">
                            <?= nl2br(
                                h(
                                    $moneta[
                                        'historia_jednostki_monetarnej'
                                    ]
                                )
                            ) ?>
                        </td>

                        <td class="path">

                            <?php if (!empty($moneta['zdjecie'])): ?>

                                <?= h($moneta['zdjecie']) ?>

                            <?php else: ?>

                                Brak

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </section>

</main>

<footer>
    Katalog monet – panel administratora
</footer>

</body>
</html>