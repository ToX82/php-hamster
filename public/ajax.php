<?php
define('APP_ROOT', realpath('../') . '/');
include APP_ROOT . "config.php";
include APP_ROOT . "libs/libs.php";
init();
require_once APP_ROOT . 'routers/router.ajax.php';

echo json_encode($return);
