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

function startTracking(isNew) {
    var activity = $('#activity').val();
    var tag = $('#tag').val();
    var start = getCurrentTime(false);
    var time = getCurrentTime(true);

    if (isNew === true) {
        saveActivity({action: 'save-activity', id: null, activity: activity, tag: tag});
        newActivity(activity, tag, start, time);
    }

    updateTitle(activity, tag);
    startTimer();
    $('.start-tracking').addClass('hide');
    $('.stop-tracking').removeClass('hide');
}

function saveActivity(data) {
    var baseUrl = $('.baseUrl').html();
    var url = baseUrl + 'ajax.php/save-activity';

    $.ajax({
        type: 'POST',
        url: url,
        data: data
    }).done(function(data) {
        var $row = $('.today tbody .current');
        $row.attr('data-id', data);
    }).fail(function(jqXHR, textStatus) {
        console.log('Error!');
        console.log(textStatus);
    });
}

function newActivity(activity, tag, start, startTime) {
    var $table = $('.today tbody');
    var $row = $('<tr data-start="' + start + '" class="current"></tr>');
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

var activityTimer;
function startTimer() {
    var $activity = $('.today .current');
    var start = $activity.attr('data-start');

    activityTimer = setInterval(function() {
        var end = getCurrentTime(false);
        var diff = getTimeDiff(start, end);

        $activity.find('td:nth(4)').html(diff + ' min');
    }, 2000);
}

function stopTracking() {
    var $activity = $('.today .current');
    var title = $('.titles .activity').attr('data-stopped');
    var start = $activity.attr('data-start');
    var id = $activity.attr('data-id');
    var end = getCurrentTime(false);
    var endTime = getCurrentTime(true);
    var diff = getTimeDiff(start, end);
    updateTitle(title, '');
    saveActivity({action: 'save-activity', id: id});
    $('.start-tracking').removeClass('hide');
    $('.stop-tracking').addClass('hide');
    $activity.removeClass('current');
    clearInterval(activityTimer);

    $activity.find('td:nth(1)').html(endTime);
    $activity.find('td:nth(4)').html(diff + ' min');
}

function getCurrentTime(timeOnly) {
    var time = moment().format('YYYY-MM-DD HH:mm:ss');

    if (timeOnly === true) {
        time = moment().format('HH:mm:ss');
    }

    return time;
}

function getTimeDiff(start, end) {
    var start = moment.utc(start, 'YYYY-MM-DD HH:mm:ss');
    var end = moment.utc(end, 'YYYY-MM-DD HH:mm:ss');
    var minutes = end.diff(start, 'minutes');
    var seconds = end.diff(start, 'seconds');
    var spareSeconds = seconds - (minutes * 60);

    // If there are more than 30 spare seconds
    // we'll add 1 minute to the final count
    if (spareSeconds >= 30) {
        minutes = minutes + 1;
    }

    return minutes;
}
