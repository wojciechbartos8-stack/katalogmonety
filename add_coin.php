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

function komunikatBleduUploadu(int $kod): string
{
    return match ($kod) {
        UPLOAD_ERR_INI_SIZE =>
            'Zdjęcie przekracza limit serwera.',

        UPLOAD_ERR_FORM_SIZE =>
            'Zdjęcie jest za duże.',

        UPLOAD_ERR_PARTIAL =>
            'Zdjęcie zostało przesłane tylko częściowo.',

        UPLOAD_ERR_NO_TMP_DIR =>
            'Brakuje katalogu tymczasowego.',

        UPLOAD_ERR_CANT_WRITE =>
            'Serwer nie może zapisać zdjęcia.',

        UPLOAD_ERR_EXTENSION =>
            'Przesyłanie zostało zatrzymane przez serwer.',

        default =>
            'Wystąpił błąd podczas przesyłania zdjęcia.'
    };
}

$blad = '';
$sukces = '';

$idPanstwo = '';
$waluta = '';
$walutaObiegowa = '';
$walutaKolekcjonerska = '';
$nominal = '';
$rokBicia = '';
$historiaWaluty = '';
$historiaJednostkiMonetarnej = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $idPanstwo = trim($_POST['id_panstwo'] ?? '');
    $waluta = trim($_POST['waluta'] ?? '');
    $walutaObiegowa = trim($_POST['waluta_obiegowa'] ?? '');
    $walutaKolekcjonerska = trim(
        $_POST['waluta_kolekcjonerska'] ?? ''
    );
    $nominal = trim($_POST['nominal'] ?? '');
    $rokBicia = trim($_POST['rok_bicia'] ?? '');
    $historiaWaluty = trim($_POST['historia_waluty'] ?? '');
    $historiaJednostkiMonetarnej = trim(
        $_POST['historia_jednostki_monetarnej'] ?? ''
    );

    $sciezkaZdjecia = '';
    $pelnaSciezkaZdjecia = null;

    if (
        $idPanstwo === '' ||
        !ctype_digit($idPanstwo) ||
        (int)$idPanstwo <= 0
    ) {
        $blad = 'Podaj prawidłowe ID państwa.';
    } elseif ($waluta === '') {
        $blad = 'Podaj nazwę waluty.';
    } elseif ($walutaObiegowa === '') {
        $blad = 'Podaj informację o walucie obiegowej.';
    } elseif ($walutaKolekcjonerska === '') {
        $blad = 'Podaj informację o walucie kolekcjonerskiej.';
    } elseif ($nominal === '') {
        $blad = 'Podaj nominał monety.';
    } elseif (
        $rokBicia === '' ||
        !ctype_digit($rokBicia) ||
        (int)$rokBicia < 1 ||
        (int)$rokBicia > 2100
    ) {
        $blad = 'Podaj prawidłowy rok bicia.';
    } elseif ($historiaWaluty === '') {
        $blad = 'Podaj historię waluty.';
    } elseif ($historiaJednostkiMonetarnej === '') {
        $blad = 'Podaj historię jednostki monetarnej.';
    }

    /*
     * Zdjęcie jest opcjonalne.
     */
    if (
        $blad === '' &&
        isset($_FILES['zdjecie']) &&
        $_FILES['zdjecie']['error'] !== UPLOAD_ERR_NO_FILE
    ) {
        $zdjecie = $_FILES['zdjecie'];

        if ($zdjecie['error'] !== UPLOAD_ERR_OK) {
            $blad = komunikatBleduUploadu(
                (int)$zdjecie['error']
            );
        } elseif ((int)$zdjecie['size'] > 5 * 1024 * 1024) {
            $blad = 'Zdjęcie może mieć maksymalnie 5 MB.';
        } elseif (!is_uploaded_file($zdjecie['tmp_name'])) {
            $blad = 'Przesłany plik jest nieprawidłowy.';
        } else {
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $typMime = $finfo->file($zdjecie['tmp_name']);

            $dozwoloneTypy = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp'
            ];

            if (!isset($dozwoloneTypy[$typMime])) {
                $blad = 'Dozwolone formaty: JPG, PNG oraz WEBP.';
            } elseif (@getimagesize($zdjecie['tmp_name']) === false) {
                $blad = 'Wybrany plik nie jest prawidłowym zdjęciem.';
            } else {
                $rozszerzenie = $dozwoloneTypy[$typMime];

                try {
                    $losowyFragment = bin2hex(random_bytes(8));
                } catch (Throwable $e) {
                    $losowyFragment = str_replace(
                        '.',
                        '',
                        uniqid('', true)
                    );
                }

                $nazwaPliku =
                    'moneta-' .
                    date('Ymd-His') .
                    '-' .
                    $losowyFragment .
                    '.' .
                    $rozszerzenie;

                $katalogZdjec =
                    __DIR__ .
                    DIRECTORY_SEPARATOR .
                    'images';

                if (!is_dir($katalogZdjec)) {
                    if (!mkdir($katalogZdjec, 0775, true)) {
                        $blad = 'Nie udało się utworzyć katalogu images.';
                    }
                }

                if (
                    $blad === '' &&
                    !is_writable($katalogZdjec)
                ) {
                    $blad = 'Katalog images nie ma uprawnień do zapisu.';
                }

                if ($blad === '') {
                    $pelnaSciezkaZdjecia =
                        $katalogZdjec .
                        DIRECTORY_SEPARATOR .
                        $nazwaPliku;

                    if (
                        !move_uploaded_file(
                            $zdjecie['tmp_name'],
                            $pelnaSciezkaZdjecia
                        )
                    ) {
                        $blad = 'Nie udało się zapisać zdjęcia.';
                    } else {
                        $sciezkaZdjecia =
                            'images/' .
                            $nazwaPliku;
                    }
                }
            }
        }
    }

    if ($blad === '') {
        try {
            $idPanstwoDoBazy = (int)$idPanstwo;
            $rokBiciaDoBazy = (int)$rokBicia;

            $stmt = $conn->prepare(
                "INSERT INTO `coin`
                (
                    `id_panstwo`,
                    `waluta`,
                    `waluta obiegowa`,
                    `waluta kolekcjonerska`,
                    `nominal`,
                    `rok_bicia`,
                    `historia_waluty`,
                    `historia_jednostki _monetarnej`,
                    `zdjecie`
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->bind_param(
                'issssisss',
                $idPanstwoDoBazy,
                $waluta,
                $walutaObiegowa,
                $walutaKolekcjonerska,
                $nominal,
                $rokBiciaDoBazy,
                $historiaWaluty,
                $historiaJednostkiMonetarnej,
                $sciezkaZdjecia
            );

            $stmt->execute();

            $idNowejMonety = $stmt->insert_id;

            $stmt->close();

            $sukces =
                'Moneta została dodana. ID rekordu: ' .
                $idNowejMonety .
                '.';

            $idPanstwo = '';
            $waluta = '';
            $walutaObiegowa = '';
            $walutaKolekcjonerska = '';
            $nominal = '';
            $rokBicia = '';
            $historiaWaluty = '';
            $historiaJednostkiMonetarnej = '';

        } catch (mysqli_sql_exception $e) {

            if (
                $pelnaSciezkaZdjecia !== null &&
                is_file($pelnaSciezkaZdjecia)
            ) {
                unlink($pelnaSciezkaZdjecia);
            }

            $blad =
                'Nie udało się zapisać monety. Błąd SQL: ' .
                $e->getMessage();
        }
    }
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

    <title>Dodaj monetę</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            padding: 30px 15px;
            font-family: Arial, Helvetica, sans-serif;
            color: #263238;
            background: #eef2f7;
        }

        .container {
            width: 100%;
            max-width: 850px;
            margin: 0 auto;
        }

        .card {
            padding: 30px;
            background: #ffffff;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin: 0 0 8px;
            color: #1f3b73;
            font-size: 30px;
        }

        .subtitle {
            margin: 0 0 28px;
            color: #607d8b;
            line-height: 1.6;
        }

        .message {
            margin-bottom: 22px;
            padding: 14px 16px;
            border-radius: 8px;
            line-height: 1.5;
        }

        .success {
            color: #155724;
            background: #d4edda;
            border: 1px solid #c3e6cb;
        }

        .error {
            color: #721c24;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 700;
        }

        input,
        textarea,
        select {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #c7d0da;
            border-radius: 8px;
            background: #ffffff;
            font-family: inherit;
            font-size: 16px;
        }

        input:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #1f6fbe;
            box-shadow: 0 0 0 3px rgba(31, 111, 190, 0.15);
        }

        textarea {
            min-height: 130px;
            resize: vertical;
        }

        input[type="file"] {
            background: #f8fafc;
        }

        .required {
            color: #c62828;
        }

        .help {
            display: block;
            margin-top: 7px;
            color: #607d8b;
            font-size: 13px;
            line-height: 1.5;
        }

        .preview {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background: #f5f7fa;
            border: 1px solid #dce3ea;
            border-radius: 10px;
            text-align: center;
        }

        .preview.visible {
            display: block;
        }

        .preview img {
            display: block;
            width: auto;
            max-width: 100%;
            max-height: 350px;
            margin: 0 auto;
            border-radius: 8px;
            object-fit: contain;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 28px;
        }

        .button,
        button {
            display: inline-flex;
            min-height: 46px;
            align-items: center;
            justify-content: center;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            color: #ffffff;
            background: #1f6fbe;
            font-family: inherit;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button:hover,
        button:hover {
            background: #155a9c;
        }

        .button-secondary {
            background: #546e7a;
        }

        .button-secondary:hover {
            background: #37474f;
        }

        @media (max-width: 650px) {
            body {
                padding: 15px 10px;
            }

            .card {
                padding: 22px 18px;
            }

            .buttons {
                flex-direction: column;
            }

            .button,
            button {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="container">

    <main class="card">

        <h1>Dodaj monetę</h1>

        <p class="subtitle">
            Uzupełnij dane monety i opcjonalnie dodaj zdjęcie.
        </p>

        <?php if ($sukces !== ''): ?>

            <div class="message success">
                <?= h($sukces) ?>
            </div>

        <?php endif; ?>

        <?php if ($blad !== ''): ?>

            <div class="message error">
                <?= h($blad) ?>
            </div>

        <?php endif; ?>

        <form
            method="post"
            enctype="multipart/form-data"
        >

            <div class="form-group">

                <label for="id_panstwo">
                    ID państwa
                    <span class="required">*</span>
                </label>

                <input
                    type="number"
                    id="id_panstwo"
                    name="id_panstwo"
                    min="1"
                    value="<?= h($idPanstwo) ?>"
                    required
                >

            </div>

            <div class="form-group">

                <label for="waluta">
                    Waluta
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="waluta"
                    name="waluta"
                    maxlength="255"
                    value="<?= h($waluta) ?>"
                    placeholder="np. Cedi"
                    required
                >

            </div>

            <div class="form-group">

                <label for="waluta_obiegowa">
                    Waluta obiegowa
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="waluta_obiegowa"
                    name="waluta_obiegowa"
                    maxlength="255"
                    value="<?= h($walutaObiegowa) ?>"
                    placeholder="np. Tak"
                    required
                >

            </div>

            <div class="form-group">

                <label for="waluta_kolekcjonerska">
                    Waluta kolekcjonerska
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="waluta_kolekcjonerska"
                    name="waluta_kolekcjonerska"
                    maxlength="255"
                    value="<?= h($walutaKolekcjonerska) ?>"
                    placeholder="np. Nie"
                    required
                >

            </div>

            <div class="form-group">

                <label for="nominal">
                    Nominał
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="nominal"
                    name="nominal"
                    maxlength="255"
                    value="<?= h($nominal) ?>"
                    placeholder="np. 1 Cedi"
                    required
                >

            </div>

            <div class="form-group">

                <label for="rok_bicia">
                    Rok bicia
                    <span class="required">*</span>
                </label>

                <input
                    type="number"
                    id="rok_bicia"
                    name="rok_bicia"
                    min="1"
                    max="2100"
                    value="<?= h($rokBicia) ?>"
                    placeholder="np. 2007"
                    required
                >

            </div>

            <div class="form-group">

                <label for="historia_waluty">
                    Historia waluty
                    <span class="required">*</span>
                </label>

                <textarea
                    id="historia_waluty"
                    name="historia_waluty"
                    required
                ><?= h($historiaWaluty) ?></textarea>

            </div>

            <div class="form-group">

                <label for="historia_jednostki_monetarnej">
                    Historia jednostki monetarnej
                    <span class="required">*</span>
                </label>

                <textarea
                    id="historia_jednostki_monetarnej"
                    name="historia_jednostki_monetarnej"
                    required
                ><?= h($historiaJednostkiMonetarnej) ?></textarea>

            </div>

            <div class="form-group">

                <label for="zdjecie">
                    Zdjęcie monety
                </label>

                <input
                    type="file"
                    id="zdjecie"
                    name="zdjecie"
                    accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                >

                <span class="help">
                    Formaty: JPG, JPEG, PNG albo WEBP.
                    Maksymalnie 5 MB.
                </span>

                <div
                    id="preview"
                    class="preview"
                >
                    <p>Podgląd zdjęcia:</p>

                    <img
                        id="previewImage"
                        src=""
                        alt="Podgląd zdjęcia"
                    >
                </div>

            </div>

            <div class="buttons">

                <button type="submit">
                    Dodaj monetę
                </button>

                <a
                    href="admin_panel.php"
                    class="button button-secondary"
                >
                    Powrót do panelu
                </a>

            </div>

        </form>

    </main>

</div>

<script>
    const fileInput = document.getElementById('zdjecie');
    const preview = document.getElementById('preview');
    const previewImage = document.getElementById('previewImage');

    fileInput.addEventListener('change', function () {
        const file = this.files[0];

        if (!file) {
            preview.classList.remove('visible');
            previewImage.src = '';
            return;
        }

        if (!file.type.startsWith('image/')) {
            alert('Wybrany plik nie jest zdjęciem.');
            this.value = '';
            preview.classList.remove('visible');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Zdjęcie może mieć maksymalnie 5 MB.');
            this.value = '';
            preview.classList.remove('visible');
            return;
        }

        const reader = new FileReader();

        reader.onload = function (event) {
            previewImage.src = event.target.result;
            preview.classList.add('visible');
        };

        reader.readAsDataURL(file);
    });
</script>

</body>
</html>