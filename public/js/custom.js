$(document).ready(function() {
    if ($('.current').length > 0) {
        startTracking(false);
    }
    $('.start-tracking').on('click', function() {
        startTracking(true);
    });

    $('.stop-tracking').on('click', function() {
        stopTracking();
    });

    $('.activity-list').on('click', '.edit', function() {
        editRow($(this).closest('.row'));
    });

    $('#edit-modal input[name=tracking]').on('click', function() {
        endTrackingCheckbox($(this));
    });

    $('.save-modal-activity').on('click', function() {
        saveModalActivity();
    });

    $('.delete-modal-activity').on('click', function() {
        deleteModalActivity();
    });

    $('.add-previous-activity').on('click', function() {
        showEmptyModal();
    });
});

var activityTimer;

/**
 *
 * @param {Boolean} isNew Indicates wether this is a new activity or not
 */
function startTracking(isNew) {
    var start = getCurrentTime(false);
    var end = '';
    var startTime = getCurrentTime(true);
    var endTime = '';
    var activityData = getACtivityData(isNew);
    var duration = '-';

    if (activityData.activity === '' || activityData.tag === '') {
        return false;
    }

    if (isNew === false) {
        $('[name=activity]').val(activityData.activity);
        $('[name=tag]').val(activityData.tag);
        $('[name=description]').val(activityData.description);
    } else {
        duration = updateTimeDifference();
        saveActivity({action: 'save-activity', id: null, activity: activityData.activity, tag: activityData.tag, description: activityData.description});
        newActivity(activityData.activity, activityData.tag, activityData.description, start, end, startTime, endTime, '', duration);
    }
    updateTrackerInterface('start', activityData.activity, activityData.tag);
}

function updateTrackerInterface(type, activity, tag) {
    if (type === 'start') {
        updateActivityHeader(activity, tag);
        updatePageTitle(activity);
        startTimer(activity);

        $('.start-tracking').addClass('hide');
        $('.stop-tracking').removeClass('hide');
    } else {
        $current = $('.activity-list .current');
        document.title = 'Dashboard - Project Hamster';
        activity = $('.titles .activity').attr('data-stopped');
        tag = '';
        $('.start-tracking').removeClass('hide');
        $('.stop-tracking').addClass('hide');
        $current.removeClass('current');
        clearInterval(activityTimer);
    }
    updateActivityHeader(activity, tag);
    updateTimeDifference();
}

function updateActivityHeader(activity, tag) {
    var $activity = $('.titles .activity');
    var $tag = $('.titles .bigtag');

    $activity.html(activity);
    $tag.html(tag);

    if (tag === '') {
        $tag.addClass('hide');
    } else {
        $tag.removeClass('hide');
    }
}

function startTimer(title) {
    activityTimer = setInterval(function() {
        updatePageTitle(title);
        updateTimeDifference();
    }, 2000);
}

function updateTimeDifference() {
    var $activities = $('.activity-list .row');
    var $dashboardTotal = $('.dashboard-total');
    var start = '';
    var end = '';
    var totalTime = 0;
    var currentTime = 0;

    $activities.each(function() {
        start = $(this).find('.item:nth(0)').attr('title');
        end = $(this).find('.item:nth(1)').attr('title');
        if (end === '') {
            end = getCurrentTime(false);
            currentTime = getTimeDiff(start, end);
            $(this).find('.item:nth(4)').html(toHours(currentTime));
        }
        totalTime = totalTime + getTimeDiff(start, end);
    });

    currentTime = toHours(currentTime);
    totalTime = toHours(totalTime);
    $dashboardTotal.find('span').html(totalTime);

    return currentTime;
}

function getACtivityData(isNew) {
    if (isNew === false) {
        var activity = $('.current .item:nth(2)').contents().get(0).nodeValue;
        var tag = $('.current .item:nth(2) span').contents().get(0).nodeValue;
        var description = $('.current .item:nth(3)').contents().get(0).nodeValue;
    } else {
        var activity = $('[name=activity]').val();
        var tag = $('[name=tag]').val();
        var description = $('[name=description]').val();
    }

    return {
        activity: activity,
        tag: tag,
        description: description
    }
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
        var $row = $('.activity-list .row.current');
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

function newActivity(activity, tag, description, start, end, startTime, endTime, duration) {
    var $table = $('.activity-list');
    var $row = $('<div data-start="' + start + '" class="row current"></div>');
    $row.append('<div class="col-2 col-lg-1 item" title="' + start + '">' + startTime + '</div>');
    $row.append('<div class="col-2 col-lg-1 item" title="' + end + '">' + endTime + '</div>');
    $row.append('<div class="col-2 col-lg-2 item">' + activity + '<span class="tag">' + tag + '</span></div>');
    $row.append('<div class="col-3 col-lg-6 item">' + description + '</div>');
    $row.append('<div class="col-2 col-lg-1 item">' + duration + '</div>');
    $row.append('<div class="col-1 col-lg-1 item"><a class="edit" href="#"></a></div>');

    $table.append($row);
}

function updatePageTitle(tag) {
    if (document.title === '..tracking - ' + tag) {
        document.title = 'tracking.. - ' + tag;
    } else {
        document.title = '..tracking - ' + tag;
    }
}

function stopTracking() {
    var $activity = $('.activity-list .row.current');
    var $dashboardTotal = $('.dashboard-total');
    var dashboardTotalMinutes = $dashboardTotal.attr('data-value');
    var start = $activity.attr('data-start');
    var id = $activity.attr('data-id');
    var end = getCurrentTime(false);
    var endTime = getCurrentTime(true);
    var diff = getTimeDiff(start, end);
    var newTotal = parseInt(diff) + parseInt(dashboardTotalMinutes);
    diff = toHours(diff);
    newTotal = toHours(newTotal);

    document.title = 'Dashboard - Project Hamster';
    updateTrackerInterface('stop', null, null);
    saveActivity({action: 'save-activity', id: id, end: end});
    clearInterval(activityTimer);

    $activity.find('.item:nth(1)').html(endTime);
    $activity.find('.item:nth(4)').html(diff);
    $dashboardTotal.find('span').html(newTotal);
}

function deleteModalActivity() {
    var $modal = $('#edit-modal');
    var id = $modal.find('[name=id]').val();
    var baseUrl = $('.baseUrl').html();
    var url = baseUrl + 'ajax.php';

    $.ajax({
        type: 'POST',
        url: url,
        data: {
            action: 'delete-activity',
            id: id
        }
    }).done(function(data) {
        data = JSON.parse(data);
        if (data.status === 'success') {
            var $row = $('.activity-list').find('[data-id='+ id +']');
            if ($row.hasClass('current')) {
                updateTrackerInterface('stop', null, null);
            }
            $row.remove();
            updateTimeDifference();
        } else {
            jsAlert('Wooops!');
        }
    }).fail(function(jqXHR, textStatus) {
        console.log('Error!');
        console.log(textStatus);
    });
}

function saveModalActivity() {
    var $modal = $('#edit-modal');

    var id = $modal.find('[name=id]').val();
    var activity = $modal.find('[name=activity]').val();
    var tag = $modal.find('[name=tag]').val();
    var description = $modal.find('[name=description]').val();
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

    if (activity === '' || tag === '') {
        return false;
    }

    saveActivity({action: 'save-activity', id: id, activity: activity, tag: tag, description:description, start: start, end: end});
    $modal.modal('hide');

    if (id === '') {
        if (startDay == moment().format('DD/MM/YYYY')) {
            newActivity(activity, tag, description, start, end, startTime, endTime, diff);
            if (tracking === true) {
                updateTrackerInterface('start', activity, tag);
            }
        }
    } else {
        var $activity = $('.activity-list .row[data-id=' + id + ']');
        $activity.find('.item:nth(0)').html(startTime).attr('title', start);
        $activity.find('.item:nth(1)').html(endTime).attr('title', end);
        $activity.find('.item:nth(2)').html(activity + '<span class="tag">' + tag + '</span>');
        $activity.find('.item:nth(3)').html(description);
        $activity.find('.item:nth(4)').html(diff);

        if (tracking === true) {
            $activity.addClass('current');
            updateTrackerInterface('start', activity, tag);
        } else {
            $activity.removeClass('current');
            updateTrackerInterface('stop', null, null);
        }
    }

    updateTimeDifference();
}

function getCurrentTime(timeOnly) {
    var time = moment().format('YYYY-MM-DD HH:mm:ss');

    if (timeOnly === true) {
        time = moment().format('HH:mm:ss');
    }

    return time;
}

function getTimeDiff(start, end) {
    if (start.indexOf("/") > -1) {
        start = moment.utc(start, "DD/MM/YYYY HH:mm:ss").format('YYYY-MM-DD HH:mm:ss');
    }
    if (end.indexOf("/") > -1) {
        end = moment.utc(end, "DD/MM/YYYY HH:mm:ss").format('YYYY-MM-DD HH:mm:ss');
    }

    start = moment.utc(start, 'YYYY-MM-DD HH:mm:ss');
    end = moment.utc(end, 'YYYY-MM-DD HH:mm:ss');

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

function readActivityData(id) {
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

    result.push(readActivityData(id));
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
        $editModal.find('textarea[name=description]').val(data.description);
        $editModal.find('.delete-modal-activity').show();

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
    $editModal.find('textarea[name=description]').val('');
    $editModal.find('input[name=tracking]').prop('checked', true).attr('checked', true);
    $editModal.find('input[name=endtime]').attr('disabled', true);
    $editModal.find('.delete-modal-activity').hide();
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
