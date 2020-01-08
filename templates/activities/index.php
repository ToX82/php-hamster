<div class="row mt-3">
    <div class="col-12 col-md-6 titles">
        <h3>
            <span class="activity" data-stopped="<?= __('no_activity') ?>"><?= __('no_activity') ?></span>
            <span class="bigtag hide"></span>
        </h3>
    </div>
</div>

<div class="row mt-3 mb-5">
    <div class="col-12 col-md-5">
        <div class="form-group">
            <input type="text" class="form-control autocomplete" autocomplete="off" id="activity" placeholder="<?= __('activity') ?>">
        </div>
    </div>
    <div class="col-12 col-md-5">
        <div class="form-group">
            <input type="text" class="form-control autocomplete" autocomplete="off" id="tag" placeholder="<?= __('tag') ?>">
        </div>
    </div>
    <div class="col-12 col-md-2 text-right">
        <button type="button" class="btn btn-primary start-tracking"><?= __('start_tracking') ?></button>
        <button type="button" class="btn btn-primary stop-tracking hide"><?= __('stop_tracking') ?></button>
    </div>
</div>

<div class="row">
    <div class="col-10 offset-1 col-md-12 offset-md-0 today">
        <table class="table compact">
            <tbody>
                <?php foreach ($activities['activities'] as $activity) { ?>
                    <tr data-id="<?= $activity['id'] ?>" data-start="<?= $activity['start'] ?>" class="<?= $activity['current'] ?>">
                        <td style="width: 100px" title="<?= $activity['start'] ?>"><?= $activity['time_start'] ?></td>
                        <td style="width: 100px" title="<?= $activity['end'] ?>"><?= $activity['time_end'] ?></td>
                        <td style="width: auto"><?= $activity['activity'] ?><span class="tag"><?= $activity['tag'] ?></span></td>
                        <td style="width: 130px"><?= $activity['duration_nice'] ?></td>
                        <td style="width: 50px"></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <div class="dashboard-total" data-value="<?= $activities['duration_total'] ?>">
        <?= __('todays_time') ?>:
        <span>
            <?= toHours($activities['duration_total']) ?>
        </span>
    </div>
</div>