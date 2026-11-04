$(function() {
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        $.fx.off = true;
    }

    let isEnlargedA = false;
    let isHoveredB = false;
    let isFocusedB = false;
    let stepsC = 0;
    const baseWidth = 160;
    const baseHeight = 90;

    $('#btn-anim-a').on('click', function() {
        isEnlargedA = !isEnlargedA;
        $(this).attr('aria-pressed', isEnlargedA);

        let targetWidth = isEnlargedA ? 240 : baseWidth;
        let targetHeight = isEnlargedA ? 130 : baseHeight;

        $(this).stop(true, false).animate({
            width: targetWidth,
            height: targetHeight
        }, 300);
    });

    function updateAnimB($elem) {
        let enlarge = isHoveredB || isFocusedB;
        let targetWidth = enlarge ? 220 : baseWidth;
        let targetHeight = enlarge ? 120 : baseHeight;

        $elem.stop(true, false).animate({
            width: targetWidth,
            height: targetHeight
        }, 300);
    }

    $('#btn-anim-b')
        .on('mouseenter', function() {
            isHoveredB = true;
            updateAnimB($(this));
        })
        .on('mouseleave', function() {
            isHoveredB = false;
            updateAnimB($(this));
        })
        .on('focus', function() {
            isFocusedB = true;
            updateAnimB($(this));
        })
        .on('blur', function() {
            isFocusedB = false;
            updateAnimB($(this));
        });

    $('#btn-anim-c').on('click', function() {
        if ($(this).is(':animated')) return;

        if (stepsC >= 5) return;

        stepsC++;
        
        $('#status-c').text('Liczba kroków: ' + stepsC);

        let targetWidth = baseWidth + (stepsC * 20);
        let targetHeight = baseHeight + (stepsC * 10);

        $(this).animate({
            width: targetWidth,
            height: targetHeight
        }, 300);
    });

    $('#btn-reset').on('click', function() {
        isEnlargedA = false;
        $('#btn-anim-a')
            .stop(true, false)
            .css({ width: baseWidth, height: baseHeight })
            .attr('aria-pressed', 'false');

        stepsC = 0;
        $('#status-c').text('Liczba kroków: ' + stepsC);
        
        $('#btn-anim-c')
            .stop(true, false)
            .css({ width: baseWidth, height: baseHeight });
    });

    $('#btn-check-server').on('click', function() {
        let $btn = $(this);
        let $statusArea = $('#ajax-status');

        $btn.prop('disabled', true);
        $statusArea.text('Ładowanie...');

        $.ajax({
            url: 'status.php',
            method: 'GET',
            dataType: 'text',
            timeout: 5000
        })
        .done(function(response) {
            $statusArea.text(response);
        })
        .fail(function() {
            $statusArea.text('Błąd: Nie udało się pobrać danych z serwera.');
        })
        .always(function() {
            $btn.prop('disabled', false);
        });
    });
});