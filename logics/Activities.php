<?php
namespace Logics;

class Activities
{
    private function validate($data)
    {
        $data['duration_minutes'] = isset($data['duration_minutes']) ? intval($data['duration_minutes']): 0;

        return $data;
    }

    public function history($data = null)
    {
        $search = (isset($data['search'])) ? $data['search'] : '';
        $start = (isset($data['start'])) ? $data['start'] : date('Y-m-1');
        $end = (isset($data['end'])) ? $data['end'] : date('Y-m-t');

        $activities = [];
        $totalsPerDay = [];
        $totalsAct = [];
        $totalsTags = [];
        for ($date = strtotime($start); $date <= strtotime($end); $date = strtotime("+1 day", $date)) {
            $day = date("Y-m-d", $date);
            $data = $this->listActivities($day, $day, $search);

            $activities[$day] = $data;
            if (!isset($totalsPerDay[$day])) {
                $totalsPerDay[$day] = 0;
            }

            $keyAct = '';
            $keyTag = '';
            foreach ($data as $record) {
                $keyAct = $record['activity'];
                $keyTag = $record['tag'];

                if (!isset($totalsAct[$keyAct])) {
                    $totalsAct[$keyAct] = 0;
                }
                if (!isset($totalsTags[$keyTag])) {
                    $totalsTags[$keyTag] = 0;
                }

                $totalsPerDay[$day] = $totalsPerDay[$day] + $record['duration_minutes'];
                $totalsAct[$keyAct] = $totalsAct[$keyAct] + $record['duration_minutes'];
                $totalsTags[$keyTag] = $totalsTags[$keyTag] + $record['duration_minutes'];
            }
        }

        uasort($totalsAct, "sortArray");
        uasort($totalsTags, "sortArray");

        return [
            'start' => $start,
            'end' => $end,
            'startNice' => toDate($start, '/'),
            'endNice' => toDate($end, '/'),
            'search' => $search,
            'activities' => $activities,
            'totalsAct' => $totalsAct,
            'totalsTags' => $totalsTags,
            'totalsPerDay' => $totalsPerDay,
            'topAct' => array_values($totalsAct)[0],
            'topTags' => array_values($totalsTags)[0]
        ];
    }

    /**
     * Preleva i dati per la dashboard
     *
     * @return array
     */
    public function dashboard($start = null, $end = null)
    {
        if ($start === null) {
            $start = date('Y-m-d');
        }
        if ($end === null) {
            $end = date('Y-m-d');
        }
        $pastActivities = $this->listActivities($start, $end, '');
        $current = $this->listCurrentActivities();
        $activities = array_merge($pastActivities, $current);
        $pastDuration = 0;

        foreach ($pastActivities as $record) {
            $pastDuration = $pastDuration + $record['duration_minutes'];
        }

        return [
            'activities' => $activities,
            'duration_total' => $pastDuration
        ];
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

    public function listActivities($start, $end, $search = '')
    {
        if ($search !== '') {
            $search = ' AND (activity LIKE "%' . $search . '%" OR tag LIKE "%' . $search . '%")';
        }

        $data = getDb(
            "SELECT
                id,
                start,
                end,
                TIME(start) AS time_start,
                TIME(end) AS time_end,
                activity,
                tag,
                duration_minutes,
                '' AS current
            FROM activities
            WHERE DATE(start) >= :start
            AND DATE(end) <= :end
            $search",
            [
                'start' => $start,
                'end' => $end
            ]
        );

        foreach ($data as $key => $value) {
            $data[$key]['duration_nice'] = toHours($value['duration_minutes']);
        }

        return $data;
    }

    public static function listCurrentActivities()
    {
        $data = getDb(
            "SELECT
                id,
                start,
                end,
                TIME(start) AS time_start,
                TIME(end) AS time_end,
                activity,
                tag,
                duration_minutes,
                0 AS duration_nice,
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
        $end = date('Y-m-d H:i:s');
        $duration = timeDiffMinutes($activity[0]['start'], $end);

        setDb(
            "UPDATE activities
            SET end = :end, duration_minutes = :duration_minutes
            WHERE id = :id",
            [
                'end' => $end,
                'duration_minutes' => $duration,
                'id' => $data['id']
            ]
        );

        return $data['id'];
    }

    public function autocomplete($params)
    {
        $field = $params['field'];
        $search = $params['search'];

        if (!in_array($field, ['activity', 'tag'])) {
            return [];
        }

        $data = getDb(
            "SELECT COUNT($field) AS count, $field AS name, MAX(start) AS data
            FROM activities
            WHERE start BETWEEN (NOW() - INTERVAL 2 MONTH) AND NOW()
            AND $field LIKE :search
            GROUP BY $field
            ORDER BY data DESC, count DESC",
            [
                'search' => "%" . $search . "%"
            ]
        );

        return $data;
    }
}
