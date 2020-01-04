<?php
if ($_POST['action'] === 'save-activity') {
    $objActivities = new logics\Activities();
    $data = filterArray($_POST);
    $return = $objActivities->save($data);
}
