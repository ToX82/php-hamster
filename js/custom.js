$(document).ready(function() {
    $('.start-tracking').on('click', function() {
        startTracking();
    });
    $('.stop-tracking').on('click', function() {
        stopTracking();
    });
});

var activityTimer;

function startTracking() {
    var activity = $('#activity').val();
    var tag = $('#tag').val();
    var time = getCurrentTime();

    newActivity(activity, tag, time);
    updateTitle(activity, tag);
    startTimer();
    $('.start-tracking').addClass('hide');
    $('.stop-tracking').removeClass('hide');
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
    var end = getCurrentTime();
    var diff = getTimeDiff(start, end);
    updateTitle(title, '');
    $('.start-tracking').removeClass('hide');
    $('.stop-tracking').addClass('hide');
    $activity.removeClass('current');
    clearInterval(activityTimer);

    $activity.find('td:nth(1)').html(end);
    $activity.find('td:nth(4)').html(diff);
}

function getCurrentTime() {
    var time = moment().format('HH:mm:ss');

    return time;
}

function getTimeDiff(start, end) {
    var start = moment(start, 'HH:mm:ss');
    var end = moment(end, 'HH:mm:ss');
    var diff = moment(end, 'HH:mm:ss').diff(moment(start, 'HH:mm:ss'));

    return moment(diff).format('H[h] mm[min] ss[sec]');
}
