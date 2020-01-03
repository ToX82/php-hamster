<div class='row'>
    <form class='col-12 col-md-4 offset-1 offset-md-4 register-form' action='<?= buildUrl("register") ?>' method='post'>
        <h2><?= __('registration') ?></h2>
        <div class='form-group'>
            <label for='icon_email'><?= __('insert_email_address') ?></label>
            <input id='icon_email' type='text' class='form-control' name='email'>
        </div>
        <div class='form-group'>
            <label for='icon_username'><?= __('insert_your_name_or_nickname') ?></label>
            <input id='icon_username' type='text' class='form-control' name='username'>
        </div>
        <div class='form-group'>
            <label for='icon_password'><?= __('insert_password') ?></label>
            <input id='icon_password' type='password' class='form-control' name='password1'>
        </div>
        <div class="form-group">
            <label for="password2"><?= __('password_repeat') ?></label>
            <input type="password" id="password2" class="form-control password-field required">
        </div>

        <p class="text-primary">
        <?= __('make_sure_email_valid') ?>
            <!-- <?= __('you_will_receive_an_email_for_confirmation') ?> -->
        </p>
        <div class="row">
            <a class="col-12 col-md-4 btn btn-info" href="<?= buildUrl("") ?>">
            <?= __('back') ?>
            </a>
            <button class="col-12 col-md-4 offset-md-4 btn btn-primary" type="submit">
            <?= __('next') ?>
            </button>
        </div>
    </form>
</div>
