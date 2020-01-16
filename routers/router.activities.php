<?php

/*
* Dashboards
*/
if (isPage('dashboard')) {
    $objActivities = new logics\Activities();
    $pageTitle = "Dashboard";

    $activities = $objActivities->dashboard();
    $hintActivities = $objActivities->autocomplete('activity');
    $hintTags = $objActivities->autocomplete('tag');
    $views[] = "templates/activities/index.php";
}
if (isPage('history')) {
    $objActivities = new logics\Activities();
    $pageTitle = "Activities";

    $data = filterArray($_POST);
    $activities = $objActivities->history($data);
    $hintActivities = $objActivities->autocomplete('activity');
    $hintTags = $objActivities->autocomplete('tag');
    $views[] = "templates/activities/history.php";
}
