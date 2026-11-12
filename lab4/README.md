# Lab 3

## Instrukcja uruchomienia
1. Projekt wymaga lokalnego serwera Apache (np. z pakietu XAMPP).
2. Skopiuj cały folder `lab4` do katalogu `htdocs` serwera XAMPP.
3. Uruchom moduł Apache w panelu kontrolnym XAMPP.
4. W przeglądarce otwórz adres: `http://localhost/lab4/`.

# Pseudokody

### Poprawne żądanie POST
```text
Inicjalizuj sesję
Wczytaj dane o produktach

JEŚLI metoda żądania == POST:
    Pobierz wartości z $_POST
    
    WALIDACJA:
    - Obie wartości są liczbami całkowitymi
    - Wskazany produkt istnieje w katalogu
    - Zamówiona ilość mieści się w przedziale od 1 do 5
    - Zamówiona ilość jest <= stan_magazynowy_produktu

    GDY WARUNKI SPEŁNIONE:
        1. Zapisz identyfikator i ilość w sesji:
        2. Wyślij nagłówek przekierowania HTTP 303:
        3. Przerwij dalsze wykonywanie skryptu:
```

### Poprawne żądanie GET
```text
Pobierz parametr

JEŚLI parametr istnieje ORAZ kategoria należy do ORAZ kategoria nie jest tablicą:
    Ustaw wybrany filtr na podaną kategorię
INACZEJ:
    Ustaw wybrany filtr na 'wszystkie'

Przefiltruj tablicę $produkty według wybranego filtru
Przejdź do wygenerowania widoku HTML
```

### Błędne żądania (POST oraz GET)
```text
// BŁĘDNY POST:
JEŚLI metoda żądania == POST:
    Pobierz wartości z $_POST

    JEŚLI brak pól LUB wartość nie jest liczbą całkowitą LUB wysłano tablicę:
        Ustaw odpowiedź HTTP 400 Bad Request
        Ustaw błąd: "Nieprawidłowe dane formularza"

    INACZEJ JEŚLI wybrany produkt nie istnieje w katalogu:
        Ustaw odpowiedź HTTP 400 Bad Request
        Ustaw błąd: "Wskazany produkt nie istnieje"

    INACZEJ JEŚLI ilosc < 1 LUB ilosc > 5:
        Ustaw odpowiedź HTTP 400 Bad Request
        Ustaw błąd: "Nieprawidłowa ilość (zakres: 1–5)"

    INACZEJ JEŚLI ilosc > stan_magazynowy_produktu:
        Ustaw odpowiedź HTTP 400 Bad Request
        Ustaw błąd: "Wybrana ilość przekracza dostępny stan magazynowy"

    OBSŁUGA BŁĘDU:
    - NIE nadpisuj $_SESSION['ostatni_wybor']
    - Wyświetl komunikat błędu przy formularzu w widoku HTML

// BŁĘDNY GET:
JEŚLI parametr 'kategoria' istnieje ORAZ (wartość nieznana LUB jest tablicą):
    1. Ustaw filtr domyślny na 'wszystkie'
    2. Ustaw błąd: "Nieznana lub nieprawidłowa kategoria filtru"
    3. NIE zmieniaj wyboru w sesji
    4. Wyświetl pełną listę produktów z komunikatem błędu
```

# Wyniki testów i dokumentacja prób

## 1. Filtr GET
* **Kroki:** 
  1. Otwórz `lab4.php?kategoria=ksiazki`.
  2. Otwórz `lab4.php?kategoria=nieznana`.
  3. Otwórz `lab4.php?kategoria[]=ksiazki`.
* **Oczekiwany wynik:** `?kategoria=ksiazki` pokazuje tylko książki. Nieznana kategoria lub `?kategoria[]=ksiazki` pokazuje pełną listę i komunikat.
* **Wynik rzeczywisty:** Zgodny. Dla poprawnego parametru wyświetlono tylko książki. W przypadku nieznanej kategorii lub przekazania tablicy widoczna jest pełna lista produktów oraz komunikat błędu (HTTP 200).

---

## 2. Poprawny POST i odświeżenie
* **Kroki:** 
  1. Wybierz dostępny produkt i poprawną ilość (np. id 1, ilość 2).
  2. Wyślij formularz.
  3. Odśwież stronę oraz zmień filtr GET.
* **Oczekiwany wynik:** Wybierz dostępny produkt i poprawną ilość. W Network widać POST 303, potem GET 200. Po odświeżeniu i zmianie filtra zapisany wybór pozostaje widoczny.
* **Wynik rzeczywisty:** Zgodny. W zakładce Network zarejestrowano odpowiedź POST z kodem HTTP 303, a następnie GET z kodem 200. Odświeżenie nie powoduje ponownego wysłania formularza, a wybór z sesji pozostaje widoczny po zmianie filtra GET.

---

## 3. Błędne wartości POST
* **Kroki:** Zmień formularz w Inspektorze (DevTools): edytuj wartości opcji (ilość na 0, 6, 2.5, `2abc`), usuń atrybut `name` w polu, dopisz `[]` do nazwy pola (`ilosc[]`) lub podaj nieistniejący ID produktu.
* **Oczekiwany wynik:** Sprawdź ilość 0, 6, 2.5, 2abc, brak pola, tablicę i nieznany produkt. Każda próba daje HTTP 400 i pozostawia poprzedni wybór.
* **Wynik rzeczywisty:** Zgodny. Dla każdego nieprawidłowego wariantu serwer zwrócił status HTTP 400 Bad Request oraz wyświetlił błąd. Zapamiętany wcześniej wybór w sesji nie uległ zmianie.

---

## 4. Stan magazynowy
* **Kroki:** Wybierz produkt ze stanem magazynowym równym 1 i spróbuj zamówić 2 sztuki.
* **Oczekiwany wynik:** Dla produktu ze stanem 1 spróbuj wybrać 2 sztuki. Serwer odrzuca wybór, mimo że 2 mieści się w przedziale 1–5.
* **Wynik rzeczywisty:** Zgodny. Żądanie zostało odrzucone ze statusem HTTP 400 Bad Request z informacją o przekroczeniu dostępnego stanu magazynowego.

---

## 5. Odrębna sesja i tekst HTML
* **Kroki:** 
  1. Otwórz stronę w nowym oknie prywatnym (Incognito).
  2. Wprowadź nazwę testową zawierającą znacznik HTML: `<b>Test</b>`.
* **Oczekiwany wynik:** W nowej sesji, np. w osobnym oknie prywatnym, nie ma wyboru. Nazwa testowa `<b>Test</b>` wyświetla się jako tekst; przywróć zwykłą nazwę.
* **Wynik rzeczywisty:** Zgodny. Nowa sesja wyświetla informację o braku wyboru. Znaki HTML są bezpiecznie zamieniane przez `htmlspecialchars()` i wyświetlają się wprost jako czysty tekst.

---

## 6. Strona po zmianie ścieżek
* **Kroki:** Otwórz stronę główną (`index.html`) oraz podstrony w podkatalogu `html/`.
* **Oczekiwany wynik:** Otwórz stronę główną i podstrony. Działają style, obrazy, linki i funkcje z lab3, w tym animacje i żądanie do status.php.
* **Wynik rzeczywisty:** Zgodny. Strona główna oraz podstrony wczytują się poprawnie. Style CSS, obrazy, skrypty animacji oraz zapytanie AJAX do `status.php` działają ze statusem HTTP 200.