<?php
$projekt_id = 'sklep-demo';

$strony = [
    'glowna' => [
        'tytul' => 'Strona główna',
        'plik'  => 'html/glowna.html',
    ],
    'produkt' => [
        'tytul' => 'Produkt',
        'plik'  => 'html/produkt.html',
    ],
    'kontakt' => [
        'tytul' => 'Kontakt',
        'plik'  => 'html/kontakt.html',
    ],
    'filmy' => [
        'tytul' => 'Filmy',
        'plik'  => 'html/filmy.html',
    ],
];

$raw_idp = $_GET['idp'] ?? null;
$aktywny_klucz = null;
$plik_widoku   = null;
$blad_komunikat = null;
$tytul_strony  = 'Błąd';

if ($raw_idp === null || $raw_idp === '') {
    $aktywny_klucz = 'glowna';
} elseif (!is_string($raw_idp) || !array_key_exists($raw_idp, $strony)) {
    http_response_code(404);
    $tytul_strony   = 'Nie znaleziono podstrony (404)';
    $blad_komunikat = 'Wybrana podstrona nie istnieje.';
} else {
    $aktywny_klucz = $raw_idp;
}

if ($aktywny_klucz !== null) {
    $sciezka_pliku = __DIR__ . '/' . $strony[$aktywny_klucz]['plik'];

    if (is_file($sciezka_pliku) && is_readable($sciezka_pliku)) {
        $plik_widoku  = $sciezka_pliku;
        $tytul_strony = $strony[$aktywny_klucz]['tytul'];
    } else {
        http_response_code(500);
        $tytul_strony   = 'Błąd serwera (500)';
        $blad_komunikat = 'Wystąpił problem po stronie serwera. Nie można wczytać zawartości podstrony.';
    }
}
?>
<!DOCTYPE html>
<html lang="pl">
    <head>
        <meta charset="UTF-8">
        <title><?php echo htmlspecialchars($tytul_strony, ENT_QUOTES, 'UTF-8'); ?></title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="css/style.css">
        <script src="vendor/jquery-4.0.0.min.js" defer></script>
        <script src="js/main.js" defer></script>
        <script src="js/lab3.js" defer></script>
    </head>
    <body>
        <header>
            <h1>Sklep z TCG</h1>
            <button type="button" id="start-clock">Start zegara</button>
            <p id="clock-display"></p>
            <button type="button" id="stop-clock">Stop zegara</button>
            <button id="theme-toggle" type="button" aria-pressed="false">Tryb ciemny</button>
        </header>
        <nav>
            <?php foreach ($strony as $klucz => $dane): ?>
                <a href="index.php?idp=<?php echo htmlspecialchars($klucz, ENT_QUOTES, 'UTF-8'); ?>"<?php echo $klucz === $aktywny_klucz ? ' aria-current="page"' : ''; ?>>
                    <?php echo htmlspecialchars($dane['tytul'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
            <a href="lab4.php">Katalog PHP</a>
        </nav>
        <main class="catalog">
             <?php if ($plik_widoku !== null): ?>
                <?php include $plik_widoku; ?>
            <?php else: ?>
                <section class="card" role="alert">
                    <h2><?php echo htmlspecialchars($tytul_strony, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo htmlspecialchars($blad_komunikat, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><a href="index.php?idp=glowna">Wróć na stronę główną</a></p>
                </section>
            <?php endif; ?>
        </main>
        <footer>
            <p>&copy; 2026 Sklep z TCG. Projekt: <?php echo htmlspecialchars($projekt_id, ENT_QUOTES, 'UTF-8'); ?></p>
        </footer>
    </body>
</html>
