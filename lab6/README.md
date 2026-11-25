# Lab 6

## Wersja projektu: v1.5

## Instrukcja uruchomienia
1. Projekt wymaga lokalnego serwera Apache oraz MySQL(np. z pakietu XAMPP).
2. Skopiuj cały folder `lab6` do katalogu `htdocs` serwera XAMPP.
3. Uruchom moduł Apache w panelu kontrolnym XAMPP.
4. W przeglądarce otwórz adres: `http://localhost/lab6/`.

## Instrukcja odtworzenia środowiska i bazy danych

1. **Utworzenie bazy i import struktury oraz danych:**
   * W phpMyAdmin utwórz bazę danych `moja_strona` z kodowaniem `utf8mb4_general_ci`.
   * Zaimportuj kolejno plik `sql/schema.sql` (tworzy tabelę `page_list`), a następnie `sql/seed.sql` (wstawia treści podstron i szkic).

2. **Utworzenie lokalnego użytkownika bazy danych:**
   * Na koncie administratora (`root`) wykonaj w zakładce SQL zapytanie nadające uprawnienia wyłącznie do odczytu z tabeli `page_list`:
     ```sql
     CREATE USER 'uzytkownik'@'localhost' IDENTIFIED BY 'TwojeSilneHaslo';
     GRANT SELECT ON `moja_strona`.`page_list` TO 'uzytkownik'@'localhost';
     FLUSH PRIVILEGES;
     ```

3. **Konfiguracja połączenia PDO:**
   * Skopiuj plik `cfg.example.php` jako `cfg.php`.
   * W pliku `cfg.php` uzupełnij zmienne `$db_user` oraz `$db_pass` danymi utworzonego lokalnego użytkownika (`cfg.php` jest ignorowany przez Git).
   * Uruchom serwer Apache i MySQL w XAMPP i otwórz w przeglądarce `http://localhost/lab6/index.php`.

## Pseudokod działania aplikacji (`index.php` i `showpage.php`)

```text
START
  Inicjalizuj zmienne domyślne ($menu_pozycje = [], $podstrona = null,$tytul_strony = 'Błąd')

  SPRÓBUJ:
    Dołącz konfigurację bazy danych (cfg.php -> obiekt $pdo)
    Dołącz definicję funkcji PokazPodstrone() (showpage.php)

    Pobierz z bazy listę opublikowanych stron do menu:
      WYKONAJ: SELECT alias, page_title FROM page_list WHERE status = 1 ORDER BY id
      ZAPISZ wynik w $menu_pozycje

    Odczytaj parametr $_GET['idp'] jako$raw_idp
    JEŻELI $raw_idp jest null LUB pustym ciągiem znaków:
      $aktywny_alias = 'glowna'
    W PRZECIWNYM RAZIE JEŻELI $raw_idp jest tekstem ORAZ pasuje do wzorca /^[a-z0-9-]{1,20}$/:
      $aktywny_alias =$raw_idp
    W PRZECIWNYM RAZIE:
      $aktywny_alias = null

    JEŻELI $aktywny_alias != null:$podstrona = PokazPodstrone($pdo,$aktywny_alias)
        // Wewnątrz PokazPodstrone:
        // Przygotuj zapytanie: SELECT id, page_title, page_content FROM page_list WHERE alias = :alias AND status = 1 LIMIT 1
        // Wykonaj z parametrem :alias = $aktywny_alias
        // Zwróć wiersz lub null

    JEŻELI $podstrona != null:
      $tytul_strony =$podstrona['page_title']
    W PRZECIWNYM RAZIE:
      Ustaw kod odpowiedzi HTTP 404
      Ustaw tytuł i komunikat o braku podstrony

  PRZECHWYĆ PDOException ($e):
    Zapisz szczegóły błędu ($e->getMessage()) wyłącznie w lokalnym logu (error_log)
    Ustaw kod odpowiedzi HTTP 500
    Ustaw ogólny komunikat błędu technicznego (bez szczegółów SQL/ścieżek)
    Wyczyść $menu_pozycje i$podstrona

  WYGENERUJ dokument HTML:
    Wypisz <title> z użyciem htmlspecialchars()
    Wypisz menu nawigacyjne na podstawie $menu_pozycje (oznacz aktywną stronę aria-current="page") + link do lab4.php
    JEŻELI $podstrona != null:
      Wypisz treść HTML ($podstrona['page_content']) w sekcji <main>
    W PRZECIWNYM RAZIE:
      Wypisz komunikat błędu (404 lub 500)
KONIEC
```

## Testy działania i obsługa błędów (Lab 6)

* **Odczyt i aktualizacja treści**
  * **Kroki:** Otwarcie `index.php` oraz `index.php?idp=`, edycja pola `page_content` dla aliasu `glowna` w phpMyAdmin, odświeżenie strony, a następnie przywrócenie pierwotnej treści.
  * **Oczekiwany wynik:** Obie ścieżki otwierają stronę główną (`HTTP 200`), a zmiana w bazie jest widoczna od razu bez edycji plików PHP ani HTML.
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (`HTTP 200`, treść zaktualizowana dynamicznie).

* **Nowa strona i menu**
  * **Kroki:** Dodanie w phpMyAdmin nowego rekordu z unikalnym aliasem i `status = 1`, otwarcie go z poziomu nawigacji, a po teście usunięcie rekordu.
  * **Oczekiwany wynik:** Link pojawia się automatycznie w menu, otwiera podstronę bez zmian w kodzie PHP, a aktywny link otrzymuje `aria-current="page"`.
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (`HTTP 200`, nowy link widoczny i oznaczony jako aktywny).

* **Szkic i publikacja**
  * **Kroki:** Sprawdzenie widoczności szkicu `zapowiedzi` (`status = 0`), tymczasowa zmiana na `status = 1` i powrót do `0`; próba dodania rekordu z istniejącym już aliasem `glowna`.
  * **Oczekiwany wynik:** Szkic ukryty w menu, bezpośredni adres zwraca `HTTP 404`; po publikacji widoczny (`HTTP 200`). Duplikat aliasu odrzucony przez indeks `UNIQUE`.
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (`HTTP 404` dla szkicu, błąd `#1062 Duplicate entry` w bazie przy duplikacie aliasu).

* **Błędne dane w adresie**
  * **Kroki:** Wywołanie kolejno adresów: `index.php?idp=nieznana`, `index.php?idp[]=filmy`, `index.php?idp=../cfg.php` oraz `index.php?idp=' OR 1=1--`.
  * **Oczekiwany wynik:** Każdy z adresów zwraca `HTTP 404` z komunikatem błędu i linkiem powrotnym, bez ostrzeżeń PHP ani błędów SQL.
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (`HTTP 404` dla wszystkich 4 prób, brak wycieku szczegółów technicznych).

* **Awaria bazy**
  * **Kroki:** Tymczasowa zmiana portu w `cfg.php` na błędny (`3307`), odświeżenie `index.php`, sprawdzenie lokalnego logu (`php_error_log`), a następnie przywrócenie portu `3306`.
  * **Oczekiwany wynik:** Serwer zwraca `HTTP 500` i ogólny komunikat błędu bez danych dostępowych, ścieżek ani SQL; szczegółowy błąd trafia wyłącznie do lokalnego logu.
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (`HTTP 500`, szczegóły błędu zapisane w `php_error_log`).

* **Funkcje po migracji**
  * **Kroki:** Weryfikacja stylów CSS, obrazków, animacji JS, przycisku AJAX (`status.php`), 3 filmów na `index.php?idp=filmy` oraz filtra i zapisu w `lab4.php` z przejściem między podstronami.
  * **Oczekiwany wynik:** Zasoby i multimedia działają poprawnie; zapis w `lab4.php` wykonuje `POST 303` → `GET 200`, a wybór w sesji utrzymuje się po powrocie z `index.php`.
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (`POST 303` → `GET 200`, stan sesji, AJAX i multimedia działają bez zmian).

* **Odtworzenie środowiska**
  * **Kroki:** Utworzenie pustej bazy testowej, import `sql/schema.sql` oraz `sql/seed.sql`, podpięcie lokalnej konfiguracji i uruchomienie aplikacji (po teście usunięcie bazy testowej).
  * **Oczekiwany wynik:** Baza i dane odtwarzają się bez błędów, a strona uruchamia się poprawnie z polskimi znakami (`utf8mb4`).
  * **Rzeczywisty wynik:** Zgodny z oczekiwanym (import przebiegł poprawnie, polskie znaki wyświetlają się prawidłowo).