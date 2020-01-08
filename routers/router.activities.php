<?php

/*
* Dashboards
*/
if (isPage('dashboard')) {
    $objActivities = new logics\Activities();
    $pageTitle = "Dashboard";
    $layout = 'fullwidth';

    $activities = $objActivities->dashboard();
    $views[] = "templates/activities/index.php";
}
if (isPage('history')) {
    $objActivities = new logics\Activities();
    $pageTitle = "Activities";
    $layout = 'fullwidth';

    $data = filterArray($_POST);
    $activities = $objActivities->history($data);
    $views[] = "templates/activities/history.php";
}
