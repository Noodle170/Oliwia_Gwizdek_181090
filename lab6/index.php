<?php
$projekt_id = 'sklep-demo';
$projekt_ver = 'v1.5';

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
try{
    require_once __DIR__ . '/cfg.php';
    require_once __DIR__ . '/showpage.php';

    $stmt_menu = $pdo->query('SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id');
    $menu_pozycje = $stmt_menu->fetchAll(PDO::FETCH_ASSOC);

    $raw_idp = $_GET['idp'] ?? null;
    $aktywny_alias = null;
    $podstrona = null;
    $blad_komunikat = null;
    $tytul_strony = 'Nie znaleziono podstrony (404)';

    if ($raw_idp === null || $raw_idp === '') {
        $aktywny_alias = 'glowna';
    } elseif (is_string($raw_idp) && preg_match('/^[a-z0-9-]{1,20}$/', $raw_idp) === 1) {
        $aktywny_alias = $raw_idp;
    }

    if ($aktywny_alias !== null) {
        $podstrona = PokazPodstrone($pdo, $aktywny_alias);
    }

    if ($podstrona !== null) {
        $tytul_strony = $podstrona['page_title'];
    } else {
        http_response_code(404);
        $blad_komunikat = 'Wybrana podstrona nie istnieje lub jest nieopublikowana.';
    }
} catch (PDOException $e) {
    error_log('Błąd bazy danych w index.php: ' . $e->getMessage());

    http_response_code(500);
    $tytul_strony = 'Błąd serwera (500)';
    $blad_komunikat = 'Wystąpił tymczasowy problem techniczny. Spróbuj ponownie później.';
    $menu_pozycje = [];
    $podstrona = null; 
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
            <?php foreach ($menu_pozycje as $pozycja): ?>
                <a href="index.php?idp=<?php echo htmlspecialchars($pozycja['alias'], ENT_QUOTES, 'UTF-8'); ?>"<?php echo ($podstrona !== null && $pozycja['alias'] === $aktywny_alias) ? ' aria-current="page"' : ''; ?>>
                    <?php echo htmlspecialchars($pozycja['page_title'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
            <a href="lab4.php">Katalog PHP</a>
        </nav>
        <main class="catalog">
             <?php if ($podstrona !== null): ?>
                <?php echo $podstrona['page_content']; ?>
            <?php else: ?>
                <section class="card" role="alert">
                    <h2><?php echo htmlspecialchars($tytul_strony, ENT_QUOTES, 'UTF-8'); ?></h2>
                    <p><?php echo htmlspecialchars($blad_komunikat, ENT_QUOTES, 'UTF-8'); ?></p>
                    <p><a href="index.php?idp=glowna">Wróć na stronę główną</a></p>
                </section>
            <?php endif; ?>
            <!-- ----------------------------------------------------------------------------------------------- -->
        </main>
        <footer>
            <p>&copy; 2026 Sklep z TCG. Projekt: <?php echo htmlspecialchars($projekt_id, ENT_QUOTES, 'UTF-8'); ?> Wersja: <?php echo htmlspecialchars($projekt_ver, ENT_QUOTES, 'UTF-8'); ?></p>
        </footer>
    </body>
</html>