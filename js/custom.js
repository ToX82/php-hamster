$(document).ready(function() {
    $('.start-tracking').on('click', function() {
        startTracking(true);
    });
    $('.stop-tracking').on('click', function() {
        stopTracking();
    });

    if ($('.current').length > 0) {
        var activity = $('.current td:nth(2)').html();
        var tag = $('.current td:nth(3)').html();
        $('#activity').val(activity);
        $('#tag').val(tag);
        startTracking(false);
    }
});

var activityTimer;

function startTracking(isNew) {
    var activity = $('#activity').val();
    var tag = $('#tag').val();
    var time = getCurrentTime();

    if (isNew === true) {
        saveActivity({action: 'save-activity', id: null, activity: activity, tag: tag});
        newActivity(activity, tag, time);
    }

    updateTitle(activity, tag);
    startTimer();
    $('.start-tracking').addClass('hide');
    $('.stop-tracking').removeClass('hide');
}

function saveActivity(data) {
    var url = 'http://localhost/php-hamster/ajax.php/save-activity';
    var $row = $('.today tbody .current');

    $.ajax({
        type: 'POST',
        url: url,
        data: data
    }).done(function(data) {
        $row.attr('data-id', data);
    }).fail(function(jqXHR, textStatus) {
        console.log('Impossibile effettuare la ricerca');
        console.log(textStatus);
    });
}

function newActivity(activity, tag, startTime) {
    var $table = $('.today tbody');
    var $row = $('<tr class="current"></tr>');
    $row.append('<td style="width: 100px">' + startTime + '</td>');
    $row.append('<td style="width: 100px"></td>');
    $row.append('<td style="width: ">' + activity + '</td>');
    $row.append('<td style="width: ">' + tag + '</td>');
    $row.append('<td style="width: 130px">0 min</td>');
    $row.append('<td style="width: 50px"></td>');

    $table.append($row);
}

function updateTitle(activity, tag) {
    var $activity = $('.titles .activity');
    var $tag = $('.titles .tag');

    $activity.html(activity);
    $tag.html(tag);
}

function startTimer() {
    var $activity = $('.today .current');
    var start = $activity.find('td:nth(0)').html();

    activityTimer = setInterval(function() {
        var end = getCurrentTime();
        var diff = getTimeDiff(start, end);

        $activity.find('td:nth(4)').html(diff);
    }, 5000);
}

function stopTracking() {
    var $activity = $('.today .current');
    var title = $('.titles .activity').attr('data-stopped');
    var start = $activity.find('td:nth(0)').html();
    var id = $activity.attr('data-id');
    var end = getCurrentTime();
    var diff = getTimeDiff(start, end);
    updateTitle(title, '');
    saveActivity({action: 'save-activity', id: id, end: end, diff: diff});
    $('.start-tracking').removeClass('hide');
    $('.stop-tracking').addClass('hide');
    $activity.removeClass('current');
    clearInterval(activityTimer);

    $activity.find('td:nth(1)').html(end);
    $activity.find('td:nth(4)').html(diff + ' min');
}

function getCurrentTime() {
    var time = moment().format('HH:mm:ss');

    return time;
}

function getTimeDiff(start, end) {
    var start = moment(start, 'HH:mm:ss');
    var end = moment(end, 'HH:mm:ss');
    var diff = moment(end, 'HH:mm:ss').diff(moment(start, 'HH:mm:ss'));

    return moment(diff).format('m');
}
