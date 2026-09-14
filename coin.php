<?php
declare(strict_types=1);

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once "db_connect.php";

$conn->set_charset("utf8mb4");

$komunikat = "";
$blad = "";


/* =========================
   POBIERANIE PAŃSTW
========================= */

$sqlPanstwa = "
    SELECT
        id,
        nazwa_panstwa
    FROM panstwo
    ORDER BY nazwa_panstwa ASC
";

$resultPanstwa = $conn->query($sqlPanstwa);


/* =========================
   DODAWANIE MONETY
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $idPanstwo = filter_input(
        INPUT_POST,
        "id_panstwo",
        FILTER_VALIDATE_INT
    );

    $waluta = trim($_POST["waluta"] ?? "");
    $nominal = trim($_POST["nominał"] ?? "");
    $walutaObiegowa = trim($_POST["waluta_obiegowa"] ?? "");
    $walutaKolekcjonerska = trim($_POST["waluta_kolekcjonerska"] ?? "");
    $rokBicia = trim($_POST["rok_bicia"] ?? "");
    $zdjecie = trim($_POST["zdjecie"] ?? "");


    if (!$idPanstwo || $idPanstwo <= 0) {

        $blad = "Wybierz państwo.";

    } else {

        $sql = "
            INSERT INTO coin
            (
                id_panstwo,
                waluta,
                `nominał`,
                `waluta obiegowa`,
                `waluta kolekcjonerska`,
                rok_bicia,
                zdjecie
            )
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "issssss",
            $idPanstwo,
            $waluta,
            $nominal,
            $walutaObiegowa,
            $walutaKolekcjonerska,
            $rokBicia,
            $zdjecie
        );

        $stmt->execute();

        $noweId = $stmt->insert_id;

        $stmt->close();

        $komunikat =
            "Moneta została dodana. ID monety: " . $noweId;
    }
}


/* =========================
   FUNKCJA BEZPIECZNEGO HTML
========================= */

function e(?string $tekst): string
{
    return htmlspecialchars(
        $tekst ?? "",
        ENT_QUOTES,
        "UTF-8"
    );
}

?>
<!DOCTYPE html>
<html lang="pl-PL">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Dodaj monetę | KatalogMonety</title>

<style>

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    padding: 0;
    font-family: Arial, Helvetica, sans-serif;
    background: #f2f4f7;
    color: #222;
}

header {
    background: linear-gradient(
        135deg,
        #1d3557,
        #457b9d
    );
    color: white;
    padding: 30px 20px;
    text-align: center;
}

header h1 {
    margin: 0;
    font-size: 32px;
}

.container {
    width: 90%;
    max-width: 900px;
    margin: 30px auto;
}

.card {
    background: white;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
}

.card h2 {
    margin-top: 0;
    color: #1d3557;
    border-bottom: 2px solid #eee;
    padding-bottom: 12px;
}

.form-group {
    margin-bottom: 20px;
}

label {
    display: block;
    font-weight: bold;
    margin-bottom: 7px;
    color: #444;
}

input,
select {
    width: 100%;
    padding: 12px;
    font-size: 16px;
    border: 1px solid #ccc;
    border-radius: 6px;
    background: white;
}

input:focus,
select:focus {
    outline: none;
    border-color: #457b9d;
}

button {
    width: 100%;
    padding: 14px;
    border: none;
    border-radius: 6px;
    background: #1d3557;
    color: white;
    font-size: 17px;
    font-weight: bold;
    cursor: pointer;
}

button:hover {
    background: #457b9d;
}

.success {
    background: #d8f3dc;
    color: #1b4332;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.error {
    background: #ffe5e5;
    color: #9b2226;
    padding: 15px;
    border-radius: 6px;
    margin-bottom: 20px;
}

.info {
    background: #eef5fb;
    border-left: 4px solid #457b9d;
    padding: 15px;
    margin-bottom: 25px;
    line-height: 1.6;
}

.back {
    display: inline-block;
    margin-top: 20px;
    color: #1d3557;
    text-decoration: none;
    font-weight: bold;
}

.back:hover {
    text-decoration: underline;
}

@media (max-width: 700px) {

    header h1 {
        font-size: 25px;
    }

    .container {
        width: 94%;
    }

    .card {
        padding: 20px;
    }

}

</style>

</head>

<body>


<header>

    <h1>Dodaj monetę</h1>

</header>


<main class="container">

<div class="card">

    <h2>Nowa moneta</h2>


    <?php if ($komunikat !== ""): ?>

        <div class="success">
            <?= e($komunikat) ?>
        </div>

    <?php endif; ?>


    <?php if ($blad !== ""): ?>

        <div class="error">
            <?= e($blad) ?>
        </div>

    <?php endif; ?>


    <div class="info">

        <strong>Państwo:</strong><br>

        Wybierz państwo z listy.
        Do tabeli <strong>coin</strong> zostanie zapisane
        tylko jego ID.

    </div>


    <form method="post">


        <!-- PAŃSTWO -->

        <div class="form-group">

            <label for="id_panstwo">
                Państwo
            </label>

            <select
                name="id_panstwo"
                id="id_panstwo"
                required
            >

                <option value="">
                    -- wybierz państwo --
                </option>

                <?php while ($panstwo = $resultPanstwa->fetch_assoc()): ?>

                    <option
                        value="<?= (int)$panstwo["id"] ?>"
                        <?= (
                            isset($_POST["id_panstwo"]) &&
                            (int)$_POST["id_panstwo"] ===
                            (int)$panstwo["id"]
                        ) ? "selected" : "" ?>
                    >

                        <?= (int)$panstwo["id"] ?>
                        |
                        <?= e($panstwo["nazwa_panstwa"]) ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </div>


        <!-- WALUTA -->

        <div class="form-group">

            <label for="waluta">
                Waluta
            </label>

            <input
                type="text"
                name="waluta"
                id="waluta"
                value="<?= e($_POST["waluta"] ?? "") ?>"
                placeholder="np. złoty"
            >

        </div>


        <!-- NOMINAŁ -->

        <div class="form-group">

            <label for="nominał">
                Nominał
            </label>

            <input
                type="text"
                name="nominał"
                id="nominał"
                value="<?= e($_POST["nominał"] ?? "") ?>"
                placeholder="np. 1"
            >

        </div>


        <!-- WALUTA OBIEGOWA -->

        <div class="form-group">

            <label for="waluta_obiegowa">
                Waluta obiegowa
            </label>

            <input
                type="text"
                name="waluta_obiegowa"
                id="waluta_obiegowa"
                value="<?= e($_POST["waluta_obiegowa"] ?? "") ?>"
                placeholder="np. PLN"
            >

        </div>


        <!-- WALUTA KOLEKCJONERSKA -->

        <div class="form-group">

            <label for="waluta_kolekcjonerska">
                Waluta kolekcjonerska
            </label>

            <input
                type="text"
                name="waluta_kolekcjonerska"
                id="waluta_kolekcjonerska"
                value="<?= e($_POST["waluta_kolekcjonerska"] ?? "") ?>"
                placeholder="np. moneta kolekcjonerska"
            >

        </div>


        <!-- ROK BICIA -->

        <div class="form-group">

            <label for="rok_bicia">
                Rok bicia
            </label>

            <input
                type="text"
                name="rok_bicia"
                id="rok_bicia"
                value="<?= e($_POST["rok_bicia"] ?? "") ?>"
                placeholder="np. 2017"
            >

        </div>


        <!-- ZDJĘCIE -->

        <div class="form-group">

            <label for="zdjecie">
                Zdjęcie
            </label>

            <input
                type="text"
                name="zdjecie"
                id="zdjecie"
                value="<?= e($_POST["zdjecie"] ?? "") ?>"
                placeholder="np. images/polska_1_zloty_2017.jpg"
            >

        </div>


        <button type="submit">
            Dodaj monetę
        </button>


    </form>


    <a
        href="index.php"
        class="back"
    >
        ‹ Powrót do katalogu
    </a>


</div>

</main>

</body>
</html>