**Przeglądarka i wersja:** Google Chrome Wersja 156.0.8078.49

**1. Motyw i nawigacja**
*   **Oczekiwany wynik:** Zmiana motywu utrzymuje się po odświeżeniu i zmianie podstrony; `aria-pressed` odpowiada stanowi.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Motyw wczytuje się z `localStorage`. Atrybut `aria-pressed` zmienia wartość na `true`/`false`.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów. Działa zgodnie z założeniami.

**2. Start trzy razy potem Stop i Start**
*   **Oczekiwany wynik:** Działa jeden interwał; Stop zatrzymuje aktualizację, a wznowienie pokazuje aktualny czas.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Zegar nie przyspiesza (nie tworzą się równoległe interwały). Po wznowieniu odczytywana jest prawidłowa, nowa godzina.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów. Zastosowano warunek `if (!clock)` blokujący dublowanie interwałów.

**3. Fragment nazwy ze spacjami i inną wielkością liter**
*   **Oczekiwany wynik:** Pasujące karty pozostają widoczne; licznik zgadza się z widokiem.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Wyszukiwarka ignoruje skrajne spacje i wielkość liter. Licznik aria-live pokazuje poprawną sumę widocznych kart.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów. Użyto metod `trim()` i `toLowerCase()`.

**4. Brak dopasowania i wyczyszczenie pola**
*   **Oczekiwany wynik:** Pojawia się komunikat o braku wyników; wyczyszczenie przywraca trzy karty.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Przy 0 wynikach pojawia się paragraf z ostrzeżeniem. Skasowanie tekstu przywraca atrybuty widoczności kartom.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów.

**5. Cena 1999 gr oraz ilość 3**
*   **Oczekiwany wynik:** Wynik wynosi 59,97 zł; sprawdź samą funkcję obliczeniową również w konsoli.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Wywołanie `calculateTotal(1999, 3)` w konsoli rzuca wynik `5997`. W interfejsie sformatowano go poprawnie jako 59,97 zł.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów.

**6. Ilość pusta 0 6 oraz 1,5**
*   **Oczekiwany wynik:** Wycena nie pokazuje błędnej wartości; formularz nie przechodzi do poprawnego podsumowania.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Skrypt wyrzuca komunikat z instrukcją o błędnych danych. Formularz jest blokowany przed podsumowaniem.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów. Warunek `Number.isInteger(qty) && qty >= 1 && qty <= 5` prawidłowo filtruje ułamki i liczby spoza zakresu.

**7. Dziesięć spacji oraz tekst `<b>Test</b>`**
*   **Oczekiwany wynik:** Same spacje są odrzucone; poprawna wiadomość ze znacznikami jest pokazana jako dosłowny tekst.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Sama spacja nie przechodzi walidacji (skrypt pokazuje błąd i ustawia `focus`). Znaczniki HTML wyświetlają się jako zwykły tekst, zapobiegając wstrzykiwaniu kodu.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów. Użyto właściwości `textContent` zamiast `innerHTML`, co naturalnie blokuje ataki XSS.

**8. Klawiatura widoki i wyłączony JavaScript**
*   **Oczekiwany wynik:** Kontrolki działają klawiaturą przy 375 i 1280 px; bez JS katalog, treść i menu pozostają dostępne.
*   **Zaobserwowany wynik:** Zgodny z oczekiwanym. Nawigacja (Tab/Enter) działa płynnie na testowanych rozdzielczościach. Po wyłączeniu JS zawartość strony jest w pełni czytelna.
*   **Przyczyna błędu i wprowadzona poprawka:** Brak błędów.
