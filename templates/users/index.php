<div class="row">
    <div class="col-12 col-lg-10 offset-lg-1">
        <table class="table compact table-hover table-striped elenco-utenti datatable">
            <thead>
                <tr>
                    <th class="text-center"><?= __('role') ?></th>
                    <th class="text-center"><?= __('user') ?></th>
                    <th class="text-center"><?= __('email') ?></th>
                    <th class="text-center"><?= __('creation') ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $usr) { ?>
                    <tr>
                        <td><?= $usr['name'] ?></td>
                        <td><?= $usr['username'] ?></td>
                        <td><?= $usr['email'] ?></td>
                        <td><?= toDateTime($usr['created']) ?></td>
                        <td class="text-right nowrap">
                            <?php if ($usr['id'] === $_SESSION['Usr']['id']) { ?>
                                <?php if ($_SESSION['Usr']['role_name'] !== 'admin') { ?>
                                    <a href="<?= buildUrl("impersona/" . $_SESSION['Admin']['id']) ?>" class="text-danger">
                                        <span class="iconify" data-width="26" data-icon="mdi-logout"></span>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                                <a href="<?= buildUrl("impersona/" . $usr['id']) ?>" class="text-success">
                                    <span class="iconify" data-width="26" data-icon="mdi-login"></span>
                                </a>
                            <?php } ?>
                            <?php if (isset($deleted)) { ?>
                                <a href="<?= buildUrl("elimina-utente/" . $usr['id']) ?>&restore" class="text-warning">
                                    <span class="iconify" data-width="26" data-icon="mdi:delete-restore"></span>
                                </a>
                            <?php } else { ?>
                                <a href="<?= buildUrl("modifica-utente/" . $usr['id']) ?>" class="text-primary">
                                    <span class="iconify" data-width="26" data-icon="mdi-playlist-edit"></span>
                                </a>
                                <a href="<?= buildUrl("elimina-utente/" . $usr['id']) ?>" class="text-danger">
                                    <span class="iconify" data-width="26" data-icon="mdi-delete"></span>
                                </a>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<div class="fixed-action-btn">
    <a href="<?= buildUrl("crea-utente") ?>" class="btn btn-large btn-danger">
        <span class="iconify" data-width="18" data-icon="fa-solid:plus"></span>
    </a>
</div>
