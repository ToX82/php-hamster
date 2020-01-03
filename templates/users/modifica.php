<div class="row">
    <div class="col-12 col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header"><?= __('user_data') ?></div>

            <form action="<?= buildUrl("salva-utente") ?>" method="post" class="card-body content">
                <input type="hidden" name="id" value="<?= $data['id'] ?>">

                <div class="row">
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="username"><?= __('username') ?></label>
                        <input name="username" id="username" type="text" class="form-control" required value="<?= $data['username'] ?>">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="email"><?= __('email') ?></label>
                        <input name="email" id="email" type="email" class="form-control" required value="<?= $data['email'] ?>">
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="password1"><?= __('password_8_chars') ?></label>
                        <input type="password" id="password1" class="form-control password-field" <?= ($data['id'] == '') ? 'required' : '' ?>>
                    </div>
                    <div class="form-group col-12 col-md-6 col-lg-3">
                        <label for="password2"><?= __('password_repeat') ?></label>
                        <input type="password" id="password2" class="form-control password-field" <?= ($data['id'] == '') ? 'required' : '' ?>>
                    </div>

                    <div class="form-group col-12 col-md-4">
                        <label><?= __('role') ?></label>
                        <select class="form-control ruolo-utente" name="role_id">
                            <?php foreach ($roles as $role) { ?>
                                <option <?= ($role['id'] === $data['role_id']) ? 'selected' : '' ?> value="<?= $role['id'] ?>"><?= $role['description'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="row nomargin">
                    <div class="col-12 col-md-4">
                        <label>
                            <input type="checkbox" value="1" name="active" <?= ($data['active'] == 1) ? 'checked' : '' ?> />
                            <span><?= __('activated') ?></span>
                        </label>
                    </div>
                </div>

                <input type="submit" class="btn btn-primary" value="Salva">
            </form>
        </div>
    </div>
</div>
