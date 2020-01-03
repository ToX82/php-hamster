<?php

/*
* Dashboards
*/
if (isPage('dashboard') && in_array($_SESSION['Usr']['role_name'], ['admin', 'user'])) {
    $objDashboards = new logics\Dashboards();
    $pageTitle = "Dashboard";
    $layout = 'fullwidth';

    $activities = $objDashboards->index();
    $views[] = "templates/dashboard/index.php";
}
