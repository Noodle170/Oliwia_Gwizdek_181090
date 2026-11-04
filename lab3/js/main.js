function toggleTheme() {
    const toggleBtn = document.getElementById('theme-toggle')

    if (!toggleBtn) return;

    const html = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || 'light';

    function setTheme(theme) {
        html.setAttribute('data-theme', theme);
        localStorage.setItem('theme', theme);

        const isDark = theme === 'dark';
        toggleBtn.setAttribute('aria-pressed', isDark)
        toggleBtn.textContent = isDark ? 'Tryb jasny' : 'Tryb ciemny'
    }

    setTheme(savedTheme);

    toggleBtn.addEventListener('click', function(){
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        setTheme(newTheme);
    })
}

function initClock() {
    const clockDisplay = document.getElementById('clock-display');
    const startBtn = document.getElementById('start-clock');
    const stopBtn = document.getElementById('stop-clock');

    if (!clockDisplay || !startBtn || !stopBtn) return;

    let clock = null;

    const data = new Intl.DateTimeFormat('pl-PL', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false
    });

    function updateClock() {
        const now = new Date();
        clockDisplay.textContent = data.format(now);
    }

    updateClock();

    function startClock() {
        if (!clock) {
            clock = setInterval(updateClock, 1000);
        }
    }

        function stopClock() {
        if (clock) {
            clearInterval(clock);
            clock = null;
        }
    }

    startBtn.addEventListener('click', startClock);
    stopBtn.addEventListener('click', stopClock);

    startClock();
}

function initSearch() {
    const searchInput = document.getElementById('search-input');
    const cards = document.querySelectorAll('.catalog .card');
    const searchCounter = document.getElementById('search-counter');
    const noResult = document.getElementById('no-results')

    if(!searchInput) return;

    searchCounter.textContent = `Liczba produktów: ${cards.length}`;

    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim().toLowerCase();
        let visibleCount = 0;

        cards.forEach(function(card) {
            const title = card.querySelector('h2').textContent.toLowerCase();
            const isMatch = title.includes(query);

            card.hidden = !isMatch;

            if (isMatch) {
                visibleCount++;
            }
        });

        searchCounter.textContent = visibleCount > 0 ? `Znaleziono: ${visibleCount}` : '';
        noResult.hidden = visibleCount > 0;
    });
}

function initContactForm() {
    const form = document.getElementById('contact-form');
    if (!form) return;

    const productSelect = document.getElementById('produkt');
    const qtyInput = document.getElementById('ilosc');
    const msgInput = document.getElementById('wiadomosc');
    const valueDisplay = document.getElementById('value');
    const msgError = document.getElementById('msg-error');
    const summarySection = document.getElementById('summary');
    const products = [
        { id: 'booster_box', nazwa: 'Booster Box Riftbound: Radiance', cenaGrosze: 65000 },
        { id: 'showdown_deck', nazwa: 'Talia Zed vs Shen', cenaGrosze: 16000 },
        { id: 'talia_jinx', nazwa: 'Talia Jinx', cenaGrosze: 10000 }
    ];

    const currencyFormatter = new Intl.NumberFormat('pl-PL', {
        style: 'currency',
        currency: 'PLN'
    });

    function calculateTotal(price, quantity) {
        return price * quantity;
    }

    function removeOldSummary() {
        summarySection.hidden = true;
    }

    function updateLiveValue() {
        removeOldSummary();
        
        const selectedId = productSelect.value;
        const qtyRaw = qtyInput.value;
        const qty = Number(qtyRaw);
        const product = products.find(function(p) { return p.id === selectedId; });
        const isValidQty = Number.isInteger(qty) && qty >= 1 && qty <= 5;

        if (!product || !qtyRaw || !isValidQty) {
            valueDisplay.textContent = 'Wprowadź poprawne dane, aby zobaczyć wycenę.';
            return;
        }

        const totalGrosze = calculateTotal(product.cenaGrosze, qty);
        valueDisplay.textContent = currencyFormatter.format(totalGrosze / 100);
    }

    productSelect.addEventListener('change', updateLiveValue);
    qtyInput.addEventListener('input', updateLiveValue);

    msgInput.addEventListener('input', function() {
        removeOldSummary();
        if (msgInput.value.trim().length >= 10) {
            msgError.hidden = true;
        }
    });

    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const trimmedMsg = msgInput.value.trim();

        if (trimmedMsg.length < 10) {
            msgError.hidden = false;
            msgInput.focus();
            removeOldSummary();
            return;
        }

        const selectedId = productSelect.value;
        const qty = Number(qtyInput.value);
        const product = products.find(function(p) { return p.id === selectedId; });

        if (product && Number.isInteger(qty) && qty >= 1 && qty <= 5) {
            const totalGrosze = calculateTotal(product.cenaGrosze, qty);

            document.getElementById('sum-prod').textContent = product.nazwa;
            document.getElementById('sum-qty').textContent = qty;
            document.getElementById('sum-val').textContent = currencyFormatter.format(totalGrosze / 100);
            document.getElementById('sum-msg').textContent = trimmedMsg;

            summarySection.hidden = false;
        }
    });
}


function init(){
    toggleTheme();
    initClock();
    initSearch();
    initContactForm();
}

document.addEventListener('DOMContentLoaded', init);