<?php

define('APP_ROOT', dirname(__DIR__) . '/');
include(APP_ROOT . 'libs/libs.php');
init();

echo $_SESSION['Usr']['id'];
