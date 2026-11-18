# Lab 5

## Instrukcja uruchomienia
1. Projekt wymaga lokalnego serwera Apache (np. z pakietu XAMPP).
2. Skopiuj cały folder `lab5` do katalogu `htdocs` serwera XAMPP.
3. Uruchom moduł Apache w panelu kontrolnym XAMPP.
4. W przeglądarce otwórz adres: `http://localhost/lab5/`.

### Pseudokod wyboru widoku

```text
Zdefiniuj stałą mapę dozwolonych widoków $strony:
    'glowna'  -> ['tytul' => 'Strona główna', 'plik' => 'html/glowna.html']
    'produkt' -> ['tytul' => 'Produkt',       'plik' => 'html/produkt.html']
    'kontakt' -> ['tytul' => 'Kontakt',       'plik' => 'html/kontakt.html']
    'filmy'   -> ['tytul' => 'Filmy',         'plik' => 'html/filmy.html']

Odczytaj wartość parametru 'idp' z tablicy $_GET (jeśli nie istnieje, przyjmij null)
Ustaw zmienne pomocnicze: aktywny_klucz = null, plik_widoku = null

// 1. Obsługa braku parametru oraz błędnego żądania (przed wysłaniem HTML)
JEŚLI idp == null LUB idp == "":
    // Brak parametru lub pusty tekst otwiera widok domyślny
    aktywny_klucz = 'glowna'

INACZEJ JEŚLI idp nie jest tekstem (np. tablica ?idp[]=...) LUB idp nie istnieje jako klucz w mapie $strony:
    // Błędne żądanie (nieznany klucz lub nieprawidłowy typ)
    Ustaw kod odpowiedzi HTTP 404 (Not Found)
    Ustaw tytuł strony na komunikat błędu 404
    Przygotuj komunikat o nieznalezieniu podstrony oraz link powrotny do strony głównej

INACZEJ:
    // Poprawny klucz dopasowany z mapy
    aktywny_klucz = idp

// 2. Weryfikacja pliku widoku przed użyciem include
JEŚLI aktywny_klucz != null:
    Zbuduj ścieżkę systemową: sciezka = __DIR__ + '/' + $strony[aktywny_klucz]['plik']

    JEŚLI is_file(sciezka) == PRAWDA ORAZ is_readable(sciezka) == PRAWDA:
        Ustaw kod odpowiedzi HTTP 200 (OK)
        Ustaw tytuł strony z mapy: $strony[aktywny_klucz]['tytul']
        plik_widoku = sciezka
    INACZEJ:
        // Błąd po stronie serwera (brak pliku lub brak praw odczytu)
        Ustaw kod odpowiedzi HTTP 500 (Internal Server Error)
        Ustaw tytuł strony na komunikat błędu 500
        Przygotuj komunikat błędu serwera (bez ujawniania ścieżki systemowej)

// 3. Generowanie szablonu HTML
Wygeneruj <head> z tytułem zabezpieczonym przez htmlspecialchars(..., ENT_QUOTES, 'UTF-8')
Wygeneruj nagłówek oraz menu <nav> na podstawie mapy $strony (aktywny link oznacz atrybutem aria-current="page")

Wewnątrz znacznika <main>:
    JEŚLI plik_widoku != null:
        Dołącz zawartość widoku instrukcją: include plik_widoku
    INACZEJ:
        Wyświetl przygotowany komunikat błędu (404 lub 500) wraz z odnośnikiem do strony głównej

Wygeneruj stopkę <footer>
```

## Dokumentacja prób testowych

### Adres domyślny i menu
* **Kroki:** Otwórz `http://localhost/lab5/index.php`, następnie `http://localhost/lab5/index.php?idp=` oraz przeklikaj wszystkie pozycje w nawigacji (`Strona główna`, `Produkt`, `Kontakt`, `Filmy`).
* **Oczekiwany wynik:** `index.php` i `index.php?idp=` pokazują stronę główną. Odnośniki otwierają właściwe treści ze wspólnym szablonem i poprawnym tytułem.
* **Wynik rzeczywisty:** Zgodny. Oba adresy domyślne zwracają HTTP 200 OK i wyświetlają `html/glowna.html`. Kliknięcie każdego linku w menu ładuje odpowiedni fragment wewnątrz szablonu, zmienia `<title>` strony i oznacza aktywny link atrybutem `aria-current="page"`.

### Nieznany lub tablicowy klucz
* **Kroki:** Otwórz w przeglądarce `http://localhost/lab5/index.php?idp=nieznana` oraz `http://localhost/lab5/index.php?idp[]=filmy`.
* **Oczekiwany wynik:** `?idp=nieznana` i `?idp[]=filmy` dają HTTP 404 i odnośnik powrotny, bez ostrzeżeń PHP.
* **Wynik rzeczywisty:** Zgodny. W zakładce Sieć oba żądania otrzymały status HTTP 404 Not Found. Wyświetlony został czytelny komunikat błędu wraz z linkiem do `index.php?idp=glowna`, bez jakichkolwiek komunikatów `Warning` lub `Notice` z PHP.

### Próba podania ścieżki
* **Kroki:** Otwórz adres `http://localhost/lab5/index.php?idp=..%2Fstatus.php`.
* **Oczekiwany wynik:** `?idp=..%2Fstatus.php` daje HTTP 404. Zawartość `status.php` nie zostaje dołączona.
* **Wynik rzeczywisty:** Zgodny. Ponieważ ciąg `../status.php` nie jest kluczem w mapie `$strony`, serwer odrzucił żądanie kodem HTTP 404 Not Found i nie wykonał `include` na pliku `status.php`.

### Brak pliku widoku
* **Kroki:** Zmień tymczasowo nazwę pliku `html/filmy.html`, otwórz `http://localhost/lab5/index.php?idp=filmy`, sprawdź kod odpowiedzi, a następnie przywróć pierwotną nazwę pliku `html/filmy.html`.
* **Oczekiwany wynik:** Adres `?idp=filmy` zwraca HTTP 500 bez ujawnienia ścieżki systemowej.
* **Wynik rzeczywisty:** Zgodny. Funkcje `is_file()` oraz `is_readable()` wykryły brak pliku przed wywołaniem `include`, ustawiły kod HTTP 500 Internal Server Error i wyświetliły ogólny komunikat błędu bez ścieżki katalogów na dysku. Po próbie przywrócono oryginalną nazwę pliku.

### Strona główna i AJAX
* **Kroki:** Otwórz `http://localhost/lab5/index.php`, przetestuj tryb ciemny, zegar, animacje z Lab 3 oraz kliknij przycisk „Sprawdź serwer”.
* **Oczekiwany wynik:** Na stronie głównej działają style, grafiki i animacje. W panelu Sieć widać udane żądanie do `status.php`, a na stronie jego wynik.
* **Wynik rzeczywisty:** Zgodny. Wszystkie style CSS, obrazy z `img/` i skrypty JS działają poprawnie. Kliknięcie przycisku wysłało żądanie GET do `status.php` (status HTTP 200 OK), a w elemencie `<output>` pojawił się tekst `PHP działa`.

### Katalog i sesja po migracji
* **Kroki:** Przejdź z menu do „Katalog PHP” (`lab4.php`), wybierz filtr kategorii, wyślij formularz wyboru produktu i ilości, a następnie przejdź na „Stronę główną”, potem na „Filmy” i wróć do katalogu.
* **Oczekiwany wynik:** Zapis poprawnego wyboru daje POST 303, potem GET 200. Po powrocie ze strony głównej i podstrony Filmy zapisany wybór nadal jest widoczny.
* **Wynik rzeczywisty:** Zgodny. Formularz zwrócił przekierowanie HTTP 303 See Other na `lab4.php` (GET 200 OK). Przełączanie się między podstronami w `index.php` nie naruszyło sesji i po powrocie do `lab4.php` zapisany produkt oraz ilość pozostały widoczne.

### Filmy i układ mobilny
* **Kroki:** Otwórz `http://localhost/lab5/index.php?idp=filmy`, uruchom odtwarzanie każdego z trzech filmów, sprawdź odnośniki obok odtwarzaczy oraz ustaw szerokość ekranu na `360 px` w DevTools.
* **Oczekiwany wynik:** Trzy filmy można uruchomić, odnośniki działają, a przy szerokości 360 px strona nie przewija się w poziomie.
* **Wynik rzeczywisty:** Zgodny. Wszystkie trzy osadzone materiały YouTube uruchamiają się po kliknięciu, linki prowadzą do odpowiednich filmów i są dostępne z klawiatury, a przy szerokości `360 px` odtwarzacze skalują się do kontenera bez poziomego paska przewijania.