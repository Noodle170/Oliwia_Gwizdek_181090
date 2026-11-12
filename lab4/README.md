# Lab 3

## Instrukcja uruchomienia
1. Projekt wymaga lokalnego serwera Apache (np. z pakietu XAMPP).
2. Skopiuj cały folder `lab3` do katalogu `htdocs` serwera XAMPP.
3. Uruchom moduł Apache w panelu kontrolnym XAMPP.
4. W przeglądarce otwórz adres: `http://localhost/lab3/`.

## Wersja jQuery
Wersja: **jQuery 4.0.0**.

## Pseudokody
* **Animacja A:** Przełącz stan zmiennej logicznej -> zaktualizuj aria-pressed -> oblicz docelowe wymiary (duże/małe) -> wyczyść kolejkę animacji -> animuj zmianę rozmiaru w 300ms.
* **Animacja B:** Na zdarzeniach enter/focus ustaw flagi na true; na leave/blur ustaw na false -> wylicz czy powiększyć (jeśli którakolwiek flaga to true) -> wyczyść kolejkę -> animuj.
* **Animacja C:** Jeśli trwa animacja LUB liczba kroków >= 5 to przerwij -> zwiększ licznik -> zaktualizuj tekst -> oblicz wymiary dodając wielokrotność kroków do bazy -> animuj.

## Wyniki testów AJAX
1. **Normalne działanie:** Status HTTP 200 OK. Metoda GET. Odpowiedź serwera: "PHP działa".
2. **Błąd 404 (zmiana nazwy pliku na serwerze):** Wyświetlany jest zdefiniowany w kodzie komunikat błędu "Błąd: Nie udało się pobrać danych z serwera."
3. **Symulacja wolnego połączenia (3G):** Komunikat "Ładowanie..." jest widoczny przez dłuższy czas, co potwierdza prawidłową obsługę stanu przed żądaniem.