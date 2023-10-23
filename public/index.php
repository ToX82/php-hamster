<?php

$pageTitle = "Project Hamster";
define('APP_ROOT', dirname(__DIR__) . '/');

include APP_ROOT . "config.php";
include APP_ROOT . "libs/libs.php";
init();
benchmark();
$lang = getLanguage();

include APP_ROOT . "routers/router.php";

if (!isset($layout)) {
    $layout = 'default';
}

include APP_ROOT . "templates/layouts/" . $layout . ".php";
?>

<!-- <?= $_SESSION['query_count'] ?> query, <?= benchmark(); ?> sec. <?= convert(memory_get_usage()) ?> mem. -->
