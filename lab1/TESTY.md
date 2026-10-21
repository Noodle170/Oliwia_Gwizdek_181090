**WERSJA PRZEGLĄDARKI** - Google Chrome Wersja 155.0.8059.85

## Struktura
- **Oczekiwany wynik:** Brak błędów walidacji w Nu HTML Checker dla wszystkich trzech plików HTML. Po wyłączeniu CSS treść i nagłówki powinny zachować logiczną, czytelną kolejność od góry do dołu.
- **Zaobserwowany wynik:** Nu HTML Checker nie wykrył błędów w kodzie dla plików `index.html`, `produkt.html` i `kontakt.html`. Po wyłączeniu stylów CSS w narzędziach DevTools nagłówki i treść zachowały swoją poprawną, logiczną kolejność.

## Widoki
- **Oczekiwany wynik:** Przy szerokościach 375 px i 1280 px strona wyświetla się bez poziomego paska przewijania. Układ katalogu zmienia się z jednej kolumny na trzy po przekroczeniu progu 768 px. Formularz nie jest ucięty na urządzeniach mobilnych, a obrazy zachowują proporcje.
- **Zaobserwowany wynik:** W trybie responsywnym na 375 px i 1280 px wszystko działa poprawnie, treść nie jest ucięta. Po przekroczeniu 768 px szerokości, układ strony zmienia się z jednej na trzy kolumny. Formularz mieści się na małym ekranie, a wszystkie obrazy zachowały prawidłowe proporcje.

## Klawiatura
- **Oczekiwany wynik:** Możliwość przechodzenia przez menu i formularz za pomocą Tab oraz Shift+Tab. Aktywacja linków klawiszem Enter. Widoczny wskaźnik fokusu na aktywnych elementach. Kliknięcie w etykietę powinno aktywować przypisane do niej pole formularza.
- **Zaobserwowany wynik:** Nawigacja klawiaturą działa prawidłowo. Na każdym zaznaczonym elemencie pojawia się wyraźny pomarańczowy obrys. Linki poprawnie reagują na klawisz Enter. Kliknięcie etykiety poprawnie przenosi kursor do przypisanego pola tekstowego.

## Walidacja
- **Oczekiwany wynik:** Przeglądarka zablokuje wysłanie formularza przy błędnych danych. Po podaniu prawidłowych danych formularz zostanie wysłany, a wartości pojawią się w adresie URL.
- **Zaobserwowany wynik:** Próby wysłania formularza pustego, z błędnym adresem e-mail, ilością sztuk równą 0 lub 6 oraz wiadomością krótszą niż 10 znaków zostały skutecznie zablokowane przez walidatory przeglądarki. Po poprawnym wypełnieniu wszystkich pól i wysłaniu, strona przeładowała się, a w adresie URL pojawiły się cztery parametry, np. http://localhost:8000/kontakt.html?email=test%40test.pl&produkt=talia_jinx&ilosc=5&wiadomosc=test+test+test 

## Eksperyment
- **Oczekiwany wynik:** Usunięcie atrybutu `name` z pola e-mail sprawi, że wartość tego pola nie zostanie dołączona do adresu URL po wysłaniu formularza.
- **Zaobserwowany wynik:** Po usunięciu `name`, wypełnieniu poprawnie całego formularza i kliknięciu przycisku wysyłania, w pasku adresu pojawiły się tylko trzy parametry (`produkt`, `ilosc`, `wiadomosc`). Adres e-mail nie został przekazany.
- **Wyjaśnienie:** Atrybut `name` jest kluczowy dla formularzy, ponieważ definiuje nazwę zmiennej (klucz), pod którą wartość z danego pola jest wysyłana do serwera (lub doklejana do URL w metodzie GET). Atrybut `id` służy wyłącznie do identyfikacji elementu po stronie klienta (np. łączenie etykiety `<label>` z polem lub stylowanie w CSS), ale nie bierze udziału w transmisji danych.