<!doctype html>
<html lang="<?= $lang['short'] ?>">
    <head>
    <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">

        <meta name="mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-capable" content="yes" />

        <link rel="apple-touch-icon" sizes="180x180" href="<?= buildAssetUrl("img/favicon/apple-touch-icon.png") ?>">
        <link rel="icon" type="image/png" sizes="32x32" href="<?= buildAssetUrl("img/favicon/favicon-32x32.png") ?>">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= buildAssetUrl("img/favicon/favicon-16x16.png") ?>">
        <link rel="manifest" href="<?= buildAssetUrl("img/favicon/site.webmanifest") ?>">
        <link rel="mask-icon" href="<?= buildAssetUrl("img/favicon/safari-pinned-tab.svg") ?>" color="#5bbad5">
        <link rel="shortcut icon" href="<?= buildAssetUrl("img/favicon/favicon.ico") ?>">
        <meta name="msapplication-TileColor" content="#2d89ef">
        <meta name="msapplication-config" content="<?= buildAssetUrl("img/favicon/browserconfig.xml") ?>">
        <meta name="theme-color" content="#ffffff">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1/css/dataTables.bootstrap4.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@4/dist/flatly/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast@1/dist/css/iziToast.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/daterangepicker@3/daterangepicker.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jquery-typeahead@2/dist/jquery.typeahead.min.css">

        <link rel="stylesheet" href="<?= buildAssetUrl("css/libraries-hacks.css") ?>">
        <link rel="stylesheet" href="<?= buildAssetUrl("css/layout.css") ?>">
        <link rel="stylesheet" href="<?= buildAssetUrl("css/print.css") ?>" media="print">

        <script src="https://cdn.jsdelivr.net/npm/@iconify/iconify@1/dist/iconify.min.js"></script>

        <title><?= $pageTitle ?> - Project Hamster</title>
    </head>
<body data-language="<?= $lang['full'] ?>">
    <div class="noprint">
        <?php include('templates/elements/topbar.php'); ?>
    </div>

    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-md-8 offset-md-2 main-content">
                    <div class="row">
                        <div class="col-12">
                            <?php
                            foreach ($views as $view) {
                                include_once($view);
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <div class="noprint">
        <?php include('templates/elements/topbar_bottom.php'); ?>
    </div>

    <?php include('templates/elements/messaggi.php'); ?>
    <?php include('templates/elements/js-alert.php'); ?>
    <?php include('templates/elements/js-confirm.php'); ?>
    <?php include('templates/elements/edit.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4/dist/js/bootstrap.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/izitoast@1/dist/js/iziToast.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/moment@2/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2/locale/it.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/daterangepicker@3/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-typeahead@2/dist/jquery.typeahead.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@2/dist/Chart.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartkick@3/dist/chartkick.min.js"></script>

    <script src="<?= buildAssetUrl("js/layout.js") ?>"></script>
    <script src="<?= buildAssetUrl("js/custom.js") ?>"></script>

    <span class="baseUrl hide"><?= BASE_URL ?></span>
</body>
</html>
