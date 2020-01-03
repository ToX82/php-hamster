<?php
$pageTitle = "Project Hamster";
include "config.php";
include "libs/libs.php";
init();

include "routers/router.php";
benchmark();

if (!isset($layout)) {
    $layout = 'default';
}

$lang = getLanguage();

include "layouts/" . $layout . ".php";
?>

<!-- <?= $_SESSION['query_count'] ?> query, <?= benchmark(); ?> sec. <?= convert(memory_get_usage()) ?> mem. -->
