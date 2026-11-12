<?php
session_start();

require_once __DIR__ . '/includes/produkty.php';

$post_error = null;
$get_error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw_produkt = $_POST['produkt'] ?? null;
    $raw_ilosc   = $_POST['ilosc'] ?? null;

    $id_valid    = !is_array($raw_produkt) && filter_var($raw_produkt, FILTER_VALIDATE_INT) !== false;
    $ilosc_valid = !is_array($raw_ilosc) && filter_var($raw_ilosc, FILTER_VALIDATE_INT) !== false;

    if (!$id_valid || !$ilosc_valid) {
        http_response_code(400);
        $post_error = "Nieprawidłowe dane formularza.";
    } else {
        $produkt_id = (int)$raw_produkt;
        $ilosc      = (int)$raw_ilosc;

        $znaleziony_produkt = null;
        foreach ($produkty as $p) {
            if ($p['id'] === $produkt_id) {
                $znaleziony_produkt = $p;
                break;
            }
        }

        if (!$znaleziony_produkt) {
            http_response_code(400);
            $post_error = "Wskazany produkt nie istnieje.";
        } elseif ($ilosc < 1 || $ilosc > 5) {
            http_response_code(400);
            $post_error = "Ilość musi mieścić się w przedziale od 1 do 5.";
        } elseif ($ilosc > $znaleziony_produkt['stan']) {
            http_response_code(400);
            $post_error = "Wybrana ilość (" . $ilosc . ") przekracza dostępny stan magazynowy (" . $znaleziony_produkt['stan'] . ").";
        } else {
            $_SESSION['ostatni_wybor'] = [
                'id'    => $produkt_id,
                'ilosc' => $ilosc
            ];
            header("Location: lab4.php", true, 303);
            exit;
        }
    }
}

$wybrana_kategoria = 'wszystkie';
if (isset($_GET['kategoria'])) {
    $kat = $_GET['kategoria'];
    if (is_array($kat) || !in_array($kat, ['wszystkie', 'ksiazki', 'akcesoria'], true)) {
        $get_error = "Nieznana lub nieprawidłowa kategoria filtru. Wyświetlono pełną listę.";
        $wybrana_kategoria = 'wszystkie';
    } else {
        $wybrana_kategoria = $kat;
    }
}

$przefiltrowane_produkty = [];
foreach ($produkty as $p) {
    if ($wybrana_kategoria === 'wszystkie' || $p['kategoria'] === $wybrana_kategoria) {
        $przefiltrowane_produkty[] = $p;
    }
}

$tytul = "Katalog PHP";
$id_grupy = "GR-ISI-1";
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($tytul, ENT_QUOTES, 'UTF-8'); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body>

<?php include __DIR__ . '/includes/naglowek.php'; ?>

<main class="catalog">
    <h2><?php echo htmlspecialchars($tytul, ENT_QUOTES, 'UTF-8'); ?></h2>
    <p>Identyfikator grupy: <strong><?php echo htmlspecialchars($id_grupy, ENT_QUOTES, 'UTF-8'); ?></strong></p>

    <section class="session-info">
        <h3>Ostatnio wybrany produkt (sesja):</h3>
        <?php
        if (isset($_SESSION['ostatni_wybor']) && is_array($_SESSION['ostatni_wybor'])) {
            $saved_id    = $_SESSION['ostatni_wybor']['id'];
            $saved_ilosc = $_SESSION['ostatni_wybor']['ilosc'];
            $saved_nazwa = "Nieznany produkt";

            foreach ($produkty as $p) {
                if ($p['id'] === $saved_id) {
                    $saved_nazwa = $p['nazwa'];
                    break;
                }
            }
            echo "<p>Wybrano: <strong>" . htmlspecialchars($saved_nazwa, ENT_QUOTES, 'UTF-8') . "</strong> — ilość: <strong>" . htmlspecialchars((string)$saved_ilosc, ENT_QUOTES, 'UTF-8') . "</strong> szt.</p>";
        } else {
            echo "<p>Brak zapisanego wyboru.</p>";
        }
        ?>
    </section>

    <form method="get" action="lab4.php">
        <label for="kategoria">Filtruj kategorycznie:</label>
        <select id="kategoria" name="kategoria">
            <option value="wszystkie" <?php echo $wybrana_kategoria === 'wszystkie' ? 'selected' : ''; ?>>Wszystkie</option>
            <option value="ksiazki" <?php echo $wybrana_kategoria === 'ksiazki' ? 'selected' : ''; ?>>Książki</option>
            <option value="akcesoria" <?php echo $wybrana_kategoria === 'akcesoria' ? 'selected' : ''; ?>>Akcesoria</option>
        </select>
        <button type="submit">Filtruj</button>
        <?php if ($get_error): ?>
            <span class="error"><?php echo htmlspecialchars($get_error, ENT_QUOTES, 'UTF-8'); ?></span>
        <?php endif; ?>
    </form>

    <form method="post" action="lab4.php">
        <h3>Zapisz wybór produktu</h3>

        <label for="produkt">Produkt:</label>
        <select id="produkt" name="produkt" required>
            <option value="">-- Wybierz produkt --</option>
            <?php foreach ($produkty as $p): ?>
                <option value="<?php echo $p['id']; ?>">
                    <?php echo htmlspecialchars($p['nazwa'], ENT_QUOTES, 'UTF-8'); ?> (Stan: <?php echo $p['stan']; ?>)
                </option>
            <?php endforeach; ?>
        </select>

        <label for="ilosc">Ilość:</label>
        <select id="ilosc" name="ilosc" required>
            <?php for ($i = 1; $i <= 5; $i++): ?>
                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
            <?php endfor; ?>
        </select>

        <button type="submit">Zapisz</button>

        <?php if ($post_error): ?>
            <p class="error"><?php echo htmlspecialchars($post_error, ENT_QUOTES, 'UTF-8'); ?></p>
        <?php endif; ?>
    </form>

    <hr>

    <h3>Lista produktów</h3>
    <div class="product-list">
        <?php
        $index = 0;
        $total = count($przefiltrowane_produkty);

        while ($index < $total) {
            $produkt = $przefiltrowane_produkty[$index];

            switch ($produkt['kategoria']) {
                case 'ksiazki':
                    $etykieta_kategorii = "Książki";
                    break;
                case 'akcesoria':
                    $etykieta_kategorii = "Akcesoria";
                    break;
                default:
                    $etykieta_kategorii = "Inna kategoria";
                    break;
            }

            if ($produkt['stan'] == 0) {
                $komunikat_stanu = "Niedostępny";
            } elseif ($produkt['stan'] >= 1 && $produkt['stan'] <= 5) {
                $komunikat_stanu = "Niski stan";
            } else {
                $komunikat_stanu = "Dostępny";
            }
            ?>

            <article class="card">
                <h2><?php echo htmlspecialchars($produkt['nazwa'], ENT_QUOTES, 'UTF-8'); ?></h2>
                <p><strong>ID:</strong> <?php echo $produkt['id']; ?></p>
                <p><strong>Kategoria:</strong> <?php echo htmlspecialchars($etykieta_kategorii, ENT_QUOTES, 'UTF-8'); ?></p>
                <p><strong>Dostępność:</strong> <?php echo htmlspecialchars($komunikat_stanu, ENT_QUOTES, 'UTF-8'); ?> (sztuk: <?php echo $produkt['stan']; ?>)</p>
            </article>

            <?php
            $index++;
        }
        ?>
    </div>
</main>

<footer>
    <p>&copy; 2026 Sklep z TCG.</p>
</footer>
</body>
</html>