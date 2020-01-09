<?php
namespace Logics;

class Activities
{
    /**
     * Validation rules
     *
     * @param array $data Dirty values
     * @return array
     */
    private function validate(array $data)
    {
        $data['duration_minutes'] = isset($data['duration_minutes']) ? intval($data['duration_minutes']): 0;

        if (isset($data['start'])) {
            $data['start'] = toMysqlDateTime($data['start']);
        }
        if (isset($data['end']) && $data['end'] !== 'true') {
            $data['end'] = toMysqlDateTime($data['end']);
        }

        return $data;
    }

    /**
     * List historic activities
     *
     * @param array $data Filtering rules
     * @return array
     */
    public function history(array $data = [])
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
     * Get today's data for the dashboard
     *
     * @return array
     */
    public function dashboard()
    {
        $start = date('Y-m-d');
        $end = date('Y-m-d');

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

    public function get(int $id)
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

    /**
     * Grabs a list of activities according to the selected parameters
     *
     * @param string $start Start date
     * @param string $end End date
     * @param string $search Search string
     * @return array
     */
    public function listActivities(string $start, string $end, string $search = '')
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

    /**
     * List unfinished activites
     *
     * @return array
     */
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

    /**
     * Saves an activity, by creating a new one or updating an existing one
     *
     * @param array $data Activity data
     * @return array
     */
    public function save(array $data)
    {
        $data = $this->validate($data);

        if ($data['id'] == null) {
            $id = $this->create($data);
        } else {
            $id = $this->update($data);
        }

        return [
            'status' => 'success',
            'id' => $id,
            'data' => $data
        ];
    }

    /**
     * Create a new activity
     *
     * @param array $data Activity data
     * @return int
     */
    public function create(array $data)
    {
        if (!isset($data['start'])) {
            $data['start'] = date('Y-m-d H:i:s');
        }
        if (!isset($data['end'])) {
            $data['end'] = null;
            $data['duration_minutes'] = 0;
        } else {
            $data['duration_minutes'] = timeDiffMinutes($data['start'], $data['end']);
        }

        $id = setDb(
            "INSERT INTO activities
            (activity, tag, start, end, duration_minutes)
            VALUES
            (:activity, :tag, :start, :end, :duration_minutes)",
            [
                'activity' => $data['activity'],
                'tag' => $data['tag'],
                'start' => $data['start'],
                'end' => $data['end'],
                'duration_minutes' => $data['duration_minutes']
            ]
        );

        return $id;
    }

    /**
     * Updates an activity
     *
     * @param array $data Activity data
     * @return int
     */
    public function update(array $data)
    {
        $activity = $this->get($data['id']);

        if (!isset($data['start'])) {
            $data['start'] = $activity[0]['start'];
        }
        if (!isset($data['tag'])) {
            $data['tag'] = $activity[0]['tag'];
        }
        if (!isset($data['activity'])) {
            $data['activity'] = $activity[0]['activity'];
        }

        if ($data['end'] === 'true') {
            $data['end'] = null;
        }

        $data['duration_minutes'] = timeDiffMinutes($data['start'], $data['end']);

        setDb(
            "UPDATE activities
            SET activity = :activity, tag = :tag, start = :start, end = :end, duration_minutes = :duration_minutes
            WHERE id = :id",
            [
                'activity' => $data['activity'],
                'tag' => $data['tag'],
                'start' => $data['start'],
                'end' => $data['end'],
                'duration_minutes' => $data['duration_minutes'],
                'id' => $data['id']
            ]
        );

        return $data['id'];
    }

    /**
     * Deletes an activity
     *
     * @param integer $id Activity ID
     * @return array
     */
    public function delete(int $id)
    {
        setDb(
            "DELETE FROM activities WHERE id = :id",
            [
                'id' => $id
            ]
        );

        return [
            'status' => 'success',
            'id' => $id
        ];
    }

    /**
     * Generates an array of activities/tags for the autocomplete inputs
     *
     * @param array $params Search parameters
     * @return array
     */
    public function autocomplete(array $params)
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
