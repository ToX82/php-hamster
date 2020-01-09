<?php
$return = null;

if (isset($_POST['action'])) {
    if ($_POST['action'] === 'save-activity') {
        $objActivities = new logics\Activities();
        $data = filterArray($_POST);
        $return = $objActivities->save($data);
    } elseif ($_POST['action'] === 'autocomplete') {
        $objActivities = new logics\Activities();
        $data = filterArray($_POST);
        $return = $objActivities->autocomplete($data);
    }
}

if (isset($_GET['action'])) {
    if (isset($_GET['action']) && isset($_GET['id'])) {
        $objActivities = new logics\Activities();
        $id = $_GET['id'];
        $return = $objActivities->get($id);
    }
}
