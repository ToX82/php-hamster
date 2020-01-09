$(document).ready(function() {
    // Se l'autocomplete perde il focus, nascondo l'autocomplete
    $('body').on('click focus touchend', '.overlay', function() {
        hideAutocomplete();
    });

    // Quando l'autocomplete ottiene il focus inizializzo l'autocomplete
    $('body').on('focus', '.autocomplete', function() {
        var $this = $(this);
        var $body = $('body');
        var $container = $(this).parent();
        var $wrapper = null;
        var search = $this.val();
        var searchType = $this.attr('name');

        if ($container.find('.autocompleteWrapper').length < 1) {
            $body.append('<div class="overlay"></div>');
            $wrapper = $('<div class="autocompleteWrapper d-none"></div>');
            $this.after($wrapper);
        }
        $wrapper = $container.find('.autocompleteWrapper');

        $.when(ajaxCall(search, searchType)).then(function(data) {
            data = JSON.parse(data);
            createOptions(data, $wrapper);
        });
    });

    // Evitiamo che le richieste vengano accavallate...
    var debouncer = (function() {
        var timer = 0;
        return function(callback, milliseconds) {
            clearTimeout (timer);
            timer = setTimeout(callback, milliseconds);
        };
    })();

    // Quando effettuo una ricerca...
    $('body').on('keyup change paste', '.autocomplete', function(e) {

        if (e.keyCode == 9 || e.which == 9) {
            hideAutocomplete();
        }

        var $this = $(this);
        var $wrapper = $this.parent().find('.autocompleteWrapper');
        var search = $this.val();
        var searchType = $this.attr('name');

        debouncer(function() {
            $.when(ajaxCall(search, searchType)).then(function(data) {
                data = JSON.parse(data);
                createOptions(data, $wrapper);
            });
        }, 200);
    });

    // Quando clicco su un elemento dell'autocomplete...
    $('body').on('click', '.autocompleteWrapper .item', function() {
        var $this = $(this);
        var $input = $this.closest('.autocompleteWrapper').parent().find('.autocomplete');
        var values = $this.attr('data-values');
        var text = '';

        values = JSON.parse(values);
        text = values.name;
        $input.val(text);

        setTimeout(function() {
            hideAutocomplete();
        }, 100);
    });

    /**
     * Rimuove l'autocomplete
     * @return {void}
     */
    function hideAutocomplete() {
        $('.overlay').remove();
        $('.autocompleteWrapper').remove();
    }

    /**
     * Filtra i risultati (AJAX)
     *
     * @param {object} data Dati
     * @param {any[]} $wrapper Elemento wrapper dell'autocomplete
     * @return {void}
     */
    function createOptions(data, $wrapper) {
        var items = '';

        if (data.length < 1) {
            hideAutocomplete();
        } else {
            $wrapper.removeClass('d-none');
        }

        $.each(data, function(index, item) {
            name = item['name'];
            values = JSON.stringify(item).replace("'", " ");
            items += '<span class="item" data-values=\'' + values + '\'>' + name + '</span>';
        });

        $wrapper.html('');
        $wrapper.append(items);
    }

    function ajaxCall(search, searchType) {
        var baseUrl = $('.baseUrl').html();
        var url = baseUrl + 'ajax.php';

        data = $.ajax({
            type: 'POST',
            url: url,
            data: {
                action: 'autocomplete',
                field: searchType,
                search: search
            },
            timeout: 3000
        }).done(function(data) {
            return data;
        }).fail(function(jqXHR, textStatus) {
            console.log('Impossibile effettuare la ricerca');
            console.log(textStatus);
        });

        return data;
    }
});
