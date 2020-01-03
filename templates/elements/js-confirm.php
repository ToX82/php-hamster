<?php
// Materialize onfirmation modal
?>

<div id="js-confirm" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= __('warning') ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><?= __('are_you_sure_you_want_to_delete_this') ?></p>
            </div>
            <div class="modal-footer">
                <a href="javascript: return false;" class="btn btn-secondary no-btn"><?= __('no') ?></a>
                <a href="javascript: return true;" class="btn btn-primary yes-btn"><?= __('yes') ?></a>
            </div>
        </div>
    </div>
</div>
