<div class='row'>
    <form class='col-10 col-md-4 offset-1 offset-md-4 login-form' action='<?= buildUrl("login") ?>' method='post'>
        <h2>Accedi</h2>
        <div class='form-group'>
            <label for='icon_username'><?= __('username_or_email') ?></label>
            <input id='icon_username' type='text' class='form-control' name='email'>
        </div>
        <div class='form-group'>
            <label for='icon_password'><?= __('password') ?></label>
            <input id='icon_password' type='password' class='form-control' name='password'>
        </div>

        <p class="text-info">
        <?= __('not_yet_registered?') ?>
        </p>
        <div class="row">
            <a class="col-12 col-md-4 btn btn-info" href="<?= buildUrl("register") ?>">
            <?= __('register') ?>
            </a>
            <button class="col-12 col-md-4 offset-md-4 btn btn-primary" type="submit">
            <?= __('login') ?>
            </button>
        </div>
    </form>
</div>
