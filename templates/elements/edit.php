<div id="edit-modal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('edit_activity') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">

                <div class="row form-inline my-3">
                    <div class="col-4 col-md-1">
                        <label><?= __('start') ?></label>
                    </div>
                    <div class="col-8 col-md-4">
                        <div class="form-group">
                            <input type="text" name="startdate" class="form-control datepicker">
                        </div>
                    </div>

                    <div class="col-8 col-md-3">
                        <div class="form-group">
                            <input type="time" name="starttime" class="form-control">
                        </div>
                    </div>

                    <div class="col-4 col-md-1">
                        <label><?= __('end') ?></label>
                    </div>
                    <div class="col-8 col-md-3">
                        <div class="form-group">
                            <input type="time" name="endtime" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="row form-inline my-3">
                    <div class="col-6 col-md-6 offset-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tracking" id="tracking" value="1">
                            <label class="form-check-label" for="tracking">
                                <?= __('still_running') ?>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="row form-inline my-3">
                    <div class="col-4 col-md-1">
                        <label><?= __('activity') ?></label>
                    </div>
                    <div class="col-8 col-md-6">
                        <div class="form-group typeahead__container">
                            <input type="text" class="form-control autocomplete" autocomplete="off" name="activity" placeholder="<?= __('activity') ?>">
                        </div>
                    </div>
                    <div class="col-4 col-md-1">
                        <label><?= __('tag') ?></label>
                    </div>
                    <div class="col-8 col-md-4">
                        <div class="form-group typeahead__container">
                            <input type="text" class="form-control autocomplete" autocomplete="off" name="tag" placeholder="<?= __('tag') ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer justify-content-between">
                <div>
                    <a href="javascript: return false;" class="btn btn-secondary delete-modal-activity" data-dismiss="modal"><?= __('delete') ?></a>
                </div>
                <div>
                    <a href="javascript: return false;" class="btn btn-secondary" data-dismiss="modal"><?= __('cancel') ?></a>
                    <a href="javascript: return true;" class="btn btn-primary save-modal-activity"><?= __('save') ?></a>
                </div>
            </div>
        </div>
    </div>
</div>
