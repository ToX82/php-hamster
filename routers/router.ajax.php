<?php
if ($_POST['action'] === 'save-activity') {
    $objActivities = new logics\Activities();
    $data = filterArray($_POST);
    $return = $objActivities->save($data);
}
if ($_POST['action'] === 'autocomplete') {
    $objActivities = new logics\Activities();
    $data = filterArray($_POST);
    $return = $objActivities->autocomplete($data);
}
