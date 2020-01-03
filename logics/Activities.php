<?php
namespace Logics;

class Activities
{
    public static function listActivities($start, $end)
    {
        $data = getDb(
            "SELECT
                id,
                TIME(start) AS start,
                TIME(end) AS end,
                activity,
                tag,
                duration_minutes
            FROM activities
            WHERE DATE(start) = :start
            AND DATE(end) = :end",
            [
                'start' => $start,
                'end' => $end
            ]
        );

        return $data;
    }
}
