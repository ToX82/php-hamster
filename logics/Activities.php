<?php
namespace Logics;

class Activities
{
    private function validate($data)
    {
        $data['duration_minutes'] = isset($data['duration_minutes']) ? intval($data['duration_minutes']): 0;

        return $data;
    }

    public function get($id)
    {
        $data = getDb(
            "SELECT *
            FROM activities
            WHERE id = :id",
            [
                'id' => $id
            ]
        );

        return $data;
    }

    public static function listActivities($start, $end)
    {
        $data = getDb(
            "SELECT
                id,
                TIME(start) AS start,
                TIME(end) AS end,
                activity,
                tag,
                duration_minutes,
                '' AS current
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

    public static function listCurrentActivities()
    {
        $data = getDb(
            "SELECT
                id,
                TIME(start) AS start,
                TIME(end) AS end,
                activity,
                tag,
                duration_minutes,
                'current' AS current
            FROM activities
            WHERE end IS NULL",
            [
            ]
        );

        return $data;
    }

    public function save($data)
    {
        $data = $this->validate($data);

        if ($data['id'] == null) {
            $id = $this->create($data);
        } else {
            $id = $this->update($data);
        }

        return $id;
    }

    public function create($data)
    {
        $id = setDb(
            "INSERT INTO activities
            (activity, tag, start, end, duration_minutes)
            VALUES
            (:activity, :tag, :start, :end, :duration_minutes)",
            [
                'activity' => $data['activity'],
                'tag' => $data['tag'],
                'start' => date('Y-m-d H:i:s'),
                'end' => null,
                'duration_minutes' => 0
            ]
        );

        return $id;
    }

    public function update($data)
    {
        $activity = $this->get($data['id']);
        $duration = timeDiffMinutes($activity[0]['start'], $activity[0]['end']);

        setDb(
            "UPDATE activities
            SET end = :end, duration_minutes = :duration_minutes
            WHERE id = :id",
            [
                'end' => date('Y-m-d H:i:s'),
                'duration_minutes' => $duration,
                'id' => $data['id']
            ]
        );

        return $data['id'];
    }
}
