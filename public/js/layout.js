$(document).ready(function() {
    /*
    * Non lasciamo scadere la sessione finché il browser è aperto...
    */
    setInterval(function() {
        console.log('staying alive');
        $.ajax({
            url: 'ping.php'
        });
    }, 5 * 60 * 1000);

    /*
    * Bottom bar - menu extend
    */
    $('.bottom-bar-extend').on('click', function() {
        $menu = $('.bottom-bar-extended');
        $menu.slideToggle();
    });

    /*
    * Search bar
    */
    $('.empty-input').on('click', function() {
        var $inputGroup = $(this).closest('.input-group');
        $inputGroup.find('input').val('');
        $inputGroup.find('.search-input').click();
    });

    /*
    * Stampa
    */
    $('.print-page').on('click', function() {
        window.print();
    });
    if ((window.location.href).indexOf('#print') > -1) {
        window.print();
        history.replaceState(null, null, ' ');
    }

    /*
    * Date picker
    */
    var lang = $('html').attr('lang');
    moment.locale(lang);
    var ranges = {
        'Last 7 Days': [moment().subtract(6, 'days'), moment()],
        'Last 30 Days': [moment().subtract(29, 'days'), moment()],
        'This Month': [moment().startOf('month'), moment().endOf('month')],
        'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
    };
    if (lang === 'it') {
        ranges = {
            'Ultimi 7 Giorni': [moment().subtract(6, 'days'), moment()],
            'Ultimi 30 Giorni': [moment().subtract(29, 'days'), moment()],
            'Questo mese': [moment().startOf('month'), moment().endOf('month')],
            'Mese scorso': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        };
    }

    $('.datepicker').daterangepicker({
        autoapply: true,
        singleDatePicker: true,
        maxDate: moment(),
        locale: {
            format: 'DD/MM/YYYY'
        }
    });
    $('.rangepicker').daterangepicker({
        autoapply: true,
        ranges: ranges,
        startDate: moment($('.dates-start').attr('data-nice'), 'DD/MM/YYYY'),
        endDate: moment($('.dates-end').attr('data-nice'), 'DD/MM/YYYY'),
        locale: {
            format: 'DD MMM YYYY'
        }
    });
    $('.rangepicker').on('apply.daterangepicker', function(ev, picker) {
        $('.dates-start').val(picker.startDate.format('YYYY-MM-DD'));
        $('.dates-end').val(picker.endDate.format('YYYY-MM-DD'));
        $(this).closest('form').submit();
    });

    /*
    * Chart
    */
    if ($('#chart').length > 0) {
        var data = $('.chartData').html();
        var label = $('#chart').attr('data-label');
        new Chartkick.ColumnChart(
            'chart',
            JSON.parse(data),
            {
                colors: ['#567'],
                label: label
            }
        );
    }

    /*
    * Autocomplete
    */
    if ($('.autocomplete').length > 0) {
        $('.autocomplete').each(function() {
            var $this = $(this);
            var field = $this.attr('name');

            $this.typeahead({
                minLength: 0,
                maxItem: 15,
                hint: true,
                accent: true,
                searchOnFocus: true,
                blurOnTab: true,
                source: {
                    data: JSON.parse($('.autocompleteHints[data-type=' + field + ']').html())
                }
            });
        });
    }

    /*
    * Toast per messaggi di sessione
    */
    if ($('.sessionMsg').length > 0) {
        iziToast.show({
            message: $('.sessionMsg').html(),
            position: 'topRight',
            color: $('.sessionMsg').attr('data-color')
        });
    }

    /*
    * Save last tab's selection
    */
    $('a[data-toggle="pill"]').on('click', function (e) {
        localStorage.setItem('lastTab', $(e.target).attr('href'));
    });

    /*
    * On page load go to the latest tab, if it needed
    */
    var lastTab = localStorage.getItem('lastTab');
    if (lastTab) {
        $('a[href="' + lastTab + '"]').click();
    }
});


/*
* Alert modal
*/
function jsAlert(text) {
    var $modal = $('#js-alert');
    var $okBtn = $('.ok-btn');

    $modal.find('h5').html('Avviso');
    $modal.find('p').html(text);
    $modal.modal('show');

    $okBtn.on('click', function() {
        $modal.modal('hide');
    });
}

$('.js-confirm').on('click', function(e) {
    e.preventDefault();
    var link = $(this).attr('href');
    var $modal = $('#js-confirm');
    $modal.modal('show');

    $modal.find('.yes-btn').click(function() {
        window.location.href = link;
    });
    $modal.find('.no-btn').click(function() {
        $modal.modal('hide');
    });
});
