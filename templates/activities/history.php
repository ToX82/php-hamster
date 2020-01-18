<form action='<?= buildUrl('history') ?>' method='post' class="row my-2 noprint">
    <div class="col-6 col-md-4 offset-md-0 col-lg-3">
        <div class="form-group">
            <input type="text" name="dates" class="form-control rangepicker">
            <input type="hidden" class="dates-start" name="start" value="<?= $activities['start'] ?>" data-nice="<?= $activities['startNice'] ?>">
            <input type="hidden" class="dates-end" name="end" value="<?= $activities['end'] ?>" data-nice="<?= $activities['endNice'] ?>">
        </div>
    </div>

    <div class="col-6 col-md-4 offset-md-4 offset-lg-5">
        <div class="input-group">
            <input type="text" name="search" class="form-control" autocomplete="off" placeholder="<?= __('search') ?>" value="<?= $activities['search'] ?>">
            <div class="input-group-append">
                <button class="input-group-text" type="submit">
                    <span class="iconify" data-icon="bx:bx-search-alt" data-inline="false" data-width="22"></span>
                </button>
            </div>
        </div>
    </div>
</form>

<div class="row noprint">
    <div id="chart" class="col-12 mb-4" data-label="<?= __('hours') ?>"></div>
</div>

<div class="row noprint">
    <div class="col-12">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="att" data-toggle="tab" href="#activities" role="tab"><?= __('activities') ?></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="tot" data-toggle="tab" href="#totals" role="tab"><?= __('totals') ?></a>
            </li>
        </ul>
    </div>
</div>

<div class="tab-content mb-3">
    <div class="tab-pane fade show active" id="activities" role="tabpanel">
        <div class="row">
            <div class="col-10 offset-1 col-md-12 offset-md-0 activity-list history">
                <?php foreach ($activities['activities'] as $key => $day) { ?>
                    <h4>
                        <?= toLocalizedDate($key) ?>
                        <span class="float-right"><?= ($activities['totalsPerDay'][$key] > 0) ? toHours($activities['totalsPerDay'][$key]) : '' ?></span>
                    </h4>
                    <table class="table compact">
                        <tbody>
                        <?php foreach ($day as $activity) { ?>
                            <tr data-id="<?= $activity['id'] ?>" data-start="<?= $activity['start'] ?>" class="<?= $activity['current'] ?>">
                                <td style="width: 200px" title="<?= $activity['start'] ?>">
                                    <?= $activity['time_start'] ?> - <?= $activity['time_end'] ?>
                                </td>
                                <td style="width: auto"><?= $activity['activity'] ?> <span class="tag"><?= $activity['tag'] ?></span></td>
                                <td class="text-right" style="width: 130px"><?= toHours($activity['duration_minutes']) ?></td>
                                <td style="width: 50px"><a class="edit noprint" href="#"></a></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="tab-pane fade" id="totals" role="tabpanel">
        <h3 class="printonly"><?= __('worked_hours_report') ?> <?= $activities['search'] ?></h3>
        <div class="row">
            <fieldset class="col-12 col-lg-7 acitivities-totals">
                <legend><?= __('activity') ?></legend>
                <?php foreach ($activities['totalsAct'] as $name => $time) { ?>
                    <div>
                        <div class="name"><?= $name ?></div>
                        <div class="graph">
                            <span class="bar" style="width: <?= round($time / $activities['topAct'] * 100, 0) ?>%"></span>
                        </div>
                        <div class="value"><?= round($time / 60, 1) ?></div>
                    </div>
                <?php } ?>
            </fieldset>
            <fieldset class="col-12 col-lg-4 offset-lg-1 tags-totals">
                <legend><?= __('tag') ?></legend>
                <?php foreach ($activities['totalsTags'] as $name => $time) { ?>
                    <div>
                        <span class="name"><?= $name ?></span>
                        <div class="graph">
                            <span class="bar" style="width: <?= round($time / $activities['topTags'] * 100, 0) ?>%"></span>
                        </div>
                        <span class="value"><?= round($time / 60, 1) ?></span>
                    </div>
                <?php } ?>
            </fieldset>
        </div>
    </div>
</div>

<div class="hide chartData"><?= json_encode($activities['chartData']) ?></div>
<div class="hide autocompleteHints" data-type="activity"><?= $hintActivities ?></div>
<div class="hide autocompleteHints" data-type="tag"><?= $hintTags ?></div>
