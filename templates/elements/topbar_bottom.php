<nav class="fixed-bottom topbar-bottom">
    <?php if (!isset($_SESSION['Usr'])) { ?>
        <ul class="d-flex justify-content-around">
            <li>
                <a class="<?= checkPage('/login', 'active') ?>" href="<?= buildUrl("login") ?>">
                    <span class="iconify" data-icon="mdi:login" data-inline="false" data-width="30" data-height="30"></span>
                    <br>
                    <?= __('login') ?>
                </a>
            </li>
        </ul>
    <?php } else { ?>
        <ul class="d-flex justify-content-around">
            <li>
                <a class="<?= checkPage('//dashboard', 'active') ?>" href="<?= buildUrl("") ?>">
                    <span class="iconify" data-icon="ant-design:home-outline" data-inline="false" data-width="30" data-height="30"></span>
                    <br>
                    <?= __('home_page') ?>
                </a>
            </li>
            <li>
                <a class="bottom-bar-extend" href="#">
                    <span class="iconify" data-icon="zmdi:more" data-inline="false" data-width="30" data-height="30"></span>
                    <br>
                    <?= __('more') ?>
                </a>
            </li>
        </ul>
        <div class="bottom-bar-extended">
            <ul class="">
                <li>
                    <a class="<?= checkPage('/logout', 'active') ?>" href="<?= buildUrl("logout") ?>">
                        <?= __('logout') ?>
                    </a>
                </li>
                <li>
                    <a class="mx-2" href='<?= buildUrl("setlang/en") ?>'>
                        <span class="iconify" data-icon="twemoji:flag-for-flag-united-kingdom" data-inline="false" data-width="24"></span>
                    </a>
                    <a class="mx-2" href='<?= buildUrl("setlang/it") ?>'>
                        <span class="iconify" data-icon="twemoji:flag-for-flag-italy" data-inline="false" data-width="24"></span>
                    </a>
                </li>
            </ul>
        </div>
    <?php } ?>
</nav>
