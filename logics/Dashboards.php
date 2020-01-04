<?php
namespace Logics;

use logics\Activities;

class Dashboards
{
    /**
     * Preleva i dati per la dashboard
     *
     * @return array
     */
    public function index()
    {
        $date = date('Y-m-d');
        $activities = Activities::listActivities($date, $date);
        $current = Activities::listCurrentActivities();
        $activities = array_merge($activities, $current);

        return $activities;
    }
}
