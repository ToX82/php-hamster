$(document).ready(function() {
    $('.start-tracking').on('click', function() {
        startTracking(true);
    });
    $('.stop-tracking').on('click', function() {
        stopTracking();
    });

    if ($('.current').length > 0) {
        startTracking(false);
    }

    $('.today').on('click', '.edit', function() {
        editRow($(this).closest('tr'));
    });

    $('#edit-modal input[name=tracking]').on('click', function() {
        endTrackingCheckbox($(this));
    });

    $('.save-modal-activity').on('click', function() {
        saveModalActivity();
    });

    $('.add-previous-activity').on('click', function() {
        showEmptyModal();
    });
});

/**
 *
 * @param {Boolean} isNew Indicates wether this is a new activity or not
 */
function startTracking(isNew) {
    var start = getCurrentTime(false);
    var time = getCurrentTime(true);

    if (isNew === false) {
        // this is not a new activity, it's probably a page refresh
        // Let's get the activity data from the table
        var activity = $('.current td:nth(2)').contents().get(0).nodeValue;
        var tag = $('.current td:nth(2) span').contents().get(0).nodeValue;
        $('[name=activity]').val(activity);
        $('[name=tag]').val(tag);
    } else {
        // For newly created activities, let's save the item in the database
        var activity = $('[name=activity]').val();
        var tag = $('[name=tag]').val();

        saveActivity({action: 'save-activity', id: null, activity: activity, tag: tag});
        newActivity(activity, tag, start, time, '', '0 min');
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
        data = JSON.parse(data);
        var $row = $('.today tbody .current');
        if (data.status === 'success') {
            $row.attr('data-id', data.id);
        } else {
            jsAlert('Wooops!');
        }
    }).fail(function(jqXHR, textStatus) {
        console.log('Error!');
        console.log(textStatus);
    });
}

function newActivity(activity, tag, start, startTime, endTime, duration) {
    var $table = $('.today tbody');
    var $row = $('<tr data-start="' + start + '" class="current"></tr>');
    $row.append('<td style="width: 100px">' + startTime + '</td>');
    $row.append('<td style="width: 100px">' + endTime + '</td>');
    $row.append('<td style="width: ">' + activity + '<span class="tag">' + tag + '</span></td>');
    $row.append('<td style="width: 130px">' + duration + '</td>');
    $row.append('<td style="width: 50px"><a class="edit" href="#"></a></td>');

    $table.append($row);
}

function updateTitle(activity, tag) {
    var $activity = $('.titles .activity');
    var $tag = $('.titles .bigtag');

    $activity.html(activity);
    $tag.html(tag);

    $tag.removeClass('hide');
    if (tag === '') {
        $tag.addClass('hide');
    }
}

var activityTimer;
function startTimer() {
    var $activity = $('.today .current');
    var $dashboardTotal = $('.dashboard-total');
    var start = $activity.attr('data-start');
    var dashboardTotalMinutes = $dashboardTotal.attr('data-value');

    activityTimer = setInterval(function() {
        var end = getCurrentTime(false);
        var diff = getTimeDiff(start, end);
        var newTotal = parseInt(diff) + parseInt(dashboardTotalMinutes);

        diff = toHours(diff);
        newTotal = toHours(newTotal);

        $activity.find('td:nth(3)').html(diff);
        $dashboardTotal.find('span').html(newTotal);
    }, 2000);
}

function stopTracking() {
    var $activity = $('.today .current');
    var $dashboardTotal = $('.dashboard-total');
    var dashboardTotalMinutes = $dashboardTotal.attr('data-value');
    var title = $('.titles .activity').attr('data-stopped');
    var start = $activity.attr('data-start');
    var id = $activity.attr('data-id');
    var end = getCurrentTime(false);
    var endTime = getCurrentTime(true);
    var diff = getTimeDiff(start, end);
    var newTotal = parseInt(diff) + parseInt(dashboardTotalMinutes);
    diff = toHours(diff);
    newTotal = toHours(newTotal);

    updateTitle(title, '');
    saveActivity({action: 'save-activity', id: id, end: end});
    $('.start-tracking').removeClass('hide');
    $('.stop-tracking').addClass('hide');
    $activity.removeClass('current');
    clearInterval(activityTimer);

    $activity.find('td:nth(1)').html(endTime);
    $activity.find('td:nth(3)').html(diff);
    $dashboardTotal.find('span').html(newTotal);
}

function saveModalActivity() {
    var $modal = $('#edit-modal');

    var id = $modal.find('[name=id]').val();
    var activity = $modal.find('[name=activity]').val();
    var tag = $modal.find('[name=tag]').val();
    var startDay = $modal.find('[name=startdate]').val();
    var startTime = $modal.find('[name=starttime]').val();
    var start = startDay + ' ' + startTime;
    var endTime = $modal.find('[name=endtime]').val();
    var end = startDay + ' ' + endTime;
    var tracking = $modal.find('[name=tracking]').prop('checked');
    var diff = '';

    if (endTime === '') {
        end = moment().format('DD/MM/YYYY HH:mm:ss');
    }

    diff = getTimeDiff(start, end);
    diff = toHours(diff);

    if (tracking === true) {
        end = '';
        diff = '';
    }

    saveActivity({action: 'save-activity', id: id, activity: activity, tag: tag, start: start, end: end});
    $modal.modal('hide');

    if (id === '') {
        if (startDay == moment().format('DD/MM/YYYY')) {
            newActivity(activity, tag, startTime, endTime, '');
            updateTitle(activity, tag);
            startTimer();
            $('.start-tracking').addClass('hide');
            $('.stop-tracking').removeClass('hide');
        }
    } else {
        var $activity = $('.today tr[data-id=' + id + ']');
        $activity.find('td:nth(0)').html(startTime);
        $activity.find('td:nth(1)').html(endTime);
        $activity.find('td:nth(2)').html(activity + '<span class="tag">' + tag + '</span>');
        $activity.find('td:nth(3)').html(diff);

        if (tracking === true) {
            updateTitle(activity, tag);
            $activity.addClass('current');
            $('.start-tracking').addClass('hide');
            $('.stop-tracking').removeClass('hide');
            clearInterval(activityTimer);
        } else {
            $activity.removeClass('current');
            clearInterval(activityTimer);
            var title = $('.titles .activity').attr('data-stopped');
            updateTitle(title, '');
            $('.start-tracking').removeClass('hide');
            $('.stop-tracking').addClass('hide');
            startTimer();
        }
    }
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

function toHours(minutes)
{
    hours = Math.floor(minutes / 60);
    minutes = minutes % 60;

    if (hours > 0) {
        return hours + 'h ' + minutes + 'min';
    }

    return minutes + 'min';
}

function findActivity(id) {
    var result = $.Deferred();
    var baseUrl = $('.baseUrl').html();
    var url = baseUrl + 'ajax.php?action=find-activity&id=' + id;

    $.ajax({
        type: 'GET',
        url: url
    }).done(function(data) {
        data = JSON.parse(data);
        result.resolve(data[0]);
    }).fail(function(jqXHR, textStatus) {
        console.log('Error!');
        console.log(textStatus);
    });

    return result.promise();
}

function editRow($row) {
    var $editModal = $('#edit-modal');
    var id = $row.attr('data-id');
    var result = [];

    result.push(findActivity(id));
    $.when.apply($, result).then(function(data) {
        var startDate = moment.utc(data.start).format('DD/MM/YYYY');
        var startTime = moment.utc(data.start).format('HH:mm:ss');
        var endTime = moment.utc(data.end).format('HH:mm:ss');

        $editModal.modal('show');
        $editModal.find('input[name=id]').val(data.id);
        $editModal.find('input[name=startdate]').val(startDate);
        $editModal.find('input[name=starttime]').val(startTime);
        $editModal.find('input[name=endtime]').val(endTime);
        $editModal.find('input[name=activity]').val(data.activity);
        $editModal.find('input[name=tag]').val(data.tag);

        if (data.end === null) {
            $editModal.find('input[name=tracking]').prop('checked', true).attr('checked', true);
            $editModal.find('input[name=endtime]').attr('disabled', true);
        } else {
            $editModal.find('input[name=tracking]').prop('checked', false).attr('checked', false);
            $editModal.find('input[name=endtime]').attr('disabled', false);
        }
    });
}

function showEmptyModal() {
    var $editModal = $('#edit-modal');
    $editModal.modal('show');
    $editModal.find('input[name=id]').val('');
    $editModal.find('input[name=startdate]').val(moment().format('DD/MM/YYYY'));
    $editModal.find('input[name=starttime]').val(moment().format('HH:mm:ss'));
    $editModal.find('input[name=endtime]').val('');
    $editModal.find('input[name=activity]').val('');
    $editModal.find('input[name=tag]').val('');
    $editModal.find('input[name=tracking]').prop('checked', true).attr('checked', true);
    $editModal.find('input[name=endtime]').attr('disabled', true);
}

function endTrackingCheckbox($this) {
    var $input = $('.modal input[name=endtime]');
    var time = getCurrentTime(true);

    if ($this.is(':checked')) {
        $input.val('');
        $input.attr('disabled', true);
    } else {
        $input.val(time);
        $input.attr('disabled', false);
    }
}
