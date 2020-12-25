<?php

define('APP_ROOT', realpath('../') . '/');
include(APP_ROOT . 'libs/libs.php');
init();

echo $_SESSION['Usr']['id'];
