<div class="row mt-3">
    <div class="col-12 titles">
        <h3>
            <span class="activity" data-stopped="<?= __('no_activity') ?>"><?= __('no_activity') ?></span>
            <span class="bigtag hide"></span>
        </h3>
    </div>
</div>

<div class="row mt-2 mb-4">
    <div class="col-12 col-md-5">
        <div class="form-group typeahead__container">
            <input type="text" class="form-control autocomplete border-secondary" autocomplete="off" name="activity" placeholder="<?= __('activity') ?>">
        </div>
    </div>
    <div class="col-7 col-md-4 col-lg-5">
        <div class="form-group typeahead__container">
            <input type="text" class="form-control autocomplete border-secondary" autocomplete="off" name="tag" placeholder="<?= __('tag') ?>">
        </div>
    </div>
    <div class="col-5 col-md-3 col-lg-2 text-right">
        <button type="button" class="btn btn-primary btn-block start-tracking"><?= __('start_tracking') ?></button>
        <button type="button" class="btn btn-primary btn-block stop-tracking hide"><?= __('stop_tracking') ?></button>
    </div>
</div>

<div class="row">
    <div class="col-12 activity-list card border-secondary">
        <?php foreach ($activities['activities'] as $activity) { ?>
            <div data-id="<?= $activity['id'] ?>" data-start="<?= $activity['start'] ?>" class="row <?= $activity['current'] ?>">
                <div class="col-2 col-lg-1 item" title="<?= $activity['start'] ?>"><?= $activity['time_start'] ?></div>
                <div class="col-2 col-lg-1 item" title="<?= $activity['end'] ?>"><?= $activity['time_end'] ?></div>
                <div class="col-5 col-lg-8 item"><?= $activity['activity'] ?><span class="tag"><?= $activity['tag'] ?></span></div>
                <div class="col-2 col-lg-1 item"><?= $activity['duration_nice'] ?></div>
                <div class="col-1 col-lg-1 item"><a class="edit" href="#"></a></div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="row mt-2">
    <div class="col-12 col-md-4">
        <div class="dashboard-total" data-value="<?= $activities['duration_total'] ?>">
            <?= __('todays_time') ?>:
            <span>
                <?= toHours($activities['duration_total']) ?>
            </span>
        </div>
    </div>
    <div class="col-12 col-md-8 text-right">
        <button type="button" class="btn btn-primary add-previous-activity"><?= __('add_previous_activity') ?></button>
        <a class="btn btn-primary" href="<?= buildUrl('history') ?>"><?= __('show_history') ?></a>
    </div>
</div>

<div class="hide autocompleteHints" data-type="activity"><?= $hintActivities ?></div>
<div class="hide autocompleteHints" data-type="tag"><?= $hintTags ?></div>
