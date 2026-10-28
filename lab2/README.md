## Instrukcja uruchomienia

Aby uruchomić projekt lokalnie i poprawnie załadować wszystkie zasoby, wykonaj poniższe kroki:

1. Pobierz pliki projektu na swój dysk.
2. Otwórz terminal i przejdź do katalogu lab1/.
3. Uruchom lokalny serwer HTTP. Jeśli masz zainstalowany język Python w wersji 3, wpisz polecenie:
   ```bash
   python -m http.server 8000

## Źródła grafik
Grafiki pobrane ze strony:
- https://merch.riotgames.com/pl-pl/category/riftbound/?page=1&sort=dateDesc

## Przepływ działania (Kalkulator wyceny formularza)

**1. Zdarzenie:** 
Użytkownik klika przycisk "Sprawdź zapytanie", co wyzwala zdarzenie `submit` na formularzu, lub zmienia wartości w polach formularza. Odrzucamy domyślne wysłanie formularza za pomocą `e.preventDefault()`.

**2. Dane wejściowe:** 
Skrypt pobiera wartości z pól formularza za pomocą właściwości `value`. Wartość z pola ilości pobierana jest jako tekst, a następnie rzutowana na typ liczbowy funkcją `Number()`. Równolegle, po identyfikatorze odnajdywany jest właściwy obiekt produktu z lokalnej tablicy, z którego pobierana jest `cenaGrosze`.

**3. Obliczenie:** 
Skrypt weryfikuje poprawność danych. Następnie cena produktu w groszach jest mnożona przez wybraną ilość w czystej funkcji pomocniczej. Wynik jest dzielony przez 100, aby uzyskać kwotę w złotówkach.

**4. Zmiana DOM:** 
Obliczona kwota w złotówkach przechodzi przez formater walutowy. Używamy właściwości `textContent`, aby bezpiecznie wstawić wyliczoną wartość, wybraną nazwę produktu, ilość oraz uciętą wiadomość do docelowych tagów w ukrytej sekcji podsumowania. Na koniec sekcja podsumowania jest odkrywana poprzez zmianę właściwości `hidden = false`.