<div class="list-group mt-4">
    <a class="list-group-item list-group-item-action <?= checkPage('/utenti', 'active') ?>" href="<?= buildUrl("utenti") ?>">
        <i class="mdi mdi-home"></i> <?= __('users_list') ?>
    </a>
    <a class="list-group-item list-group-item-action <?= checkPage('/utenti-cancellati', 'active') ?>" href="<?= buildUrl("utenti-cancellati") ?>">
        <i class="mdi mdi-home"></i> <?= __('deleted_users_list') ?>
    </a>
</div>
