<nav class="navbar fixed-top navbar-expand topbar">
    <a class="navbar-brand" href="<?= buildUrl("") ?>">
        <img alt="Project Hamster" src="<?= buildUrl('img/logo_top.png') ?>">
    </a>
    <div class="collapse navbar-collapse" id="topBar">
        <ul class="navbar-nav">
            <?php if (isset($_SESSION['Admin'])) { ?>
                <li class="nav-item"><a class="text-dark nav-link <?= checkPage('/utenti', 'active') ?>" href="<?= buildUrl("utenti") ?>"><?= __('users') ?></a></li>
            <?php } ?>
        </ul>

        <?php if (isset($_SESSION['Usr'])) { ?>
            <div class="float-right text-right welcome">
                <span class="mr-4">
                    <a class="mx-1" href='<?= buildUrl("setlang/en") ?>'>
                        <span class="iconify" data-icon="twemoji:flag-for-flag-united-kingdom" data-inline="false" data-width="24"></span>
                    </a>
                    <a class="mx-1" href='<?= buildUrl("setlang/it") ?>'>
                        <span class="iconify" data-icon="twemoji:flag-for-flag-italy" data-inline="false" data-width="24"></span>
                    </a>
                </span>

                <a class="text-dark <?= checkPage('/logout', 'active') ?>" href="<?= buildUrl("logout") ?>">
                    <?= __('logout') ?>
                    <span class="text-dark iconify" data-icon="mdi:logout" data-inline="false" data-width="30" data-height="30"></span>
                </a>
            </div>
        <?php } else { ?>
            <div class="float-right text-right text-dark welcome">
                <a class="text-dark <?= checkPage('/login', 'active') ?>" href="<?= buildUrl("login") ?>">
                    <?= __('login') ?>
                    <span class="text-dark iconify" data-icon="mdi:login" data-inline="false" data-width="30" data-height="30"></span>
                </a>
            </div>
        <?php } ?>
    </div>
</nav>
