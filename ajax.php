<?php
include "config.php";
include "libs/libs.php";
init();
require_once BASE_PATH . 'routers/router.ajax.php';

echo json_encode($return);
