<?php
include("libs/libs.php");
include("libs/utilities.php");

if (checkExist('save-data')) {
    $sessions = new logics\Sessions();
    $data = filterArray($_POST);
    $return = $sessions->saveData($data);
}

echo json_encode($return);
