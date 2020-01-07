<?php
/**
 * Traduce in lingue diverse
 *
 * @param string $string Stringa da tradurre
 * @return string Stringa tradotta
 */
function __($string)
{
    if (!isset($_COOKIE['language'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    } else {
        $lang = $_COOKIE['language'];
    }
    $lang = setCookieLanguage($lang);

    $return = '';
    $data = file_get_contents(BASE_PATH . "/i18n/" . $lang . ".json");
    $data = json_decode($data, true);

    if (!isset($data[$string]) || $data[$string] === '') {
        return $string;
    }

    return $data[$string];
}

function getLanguage()
{
    if (!isset($_COOKIE['language'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    } else {
        $lang = $_COOKIE['language'];
    }
    $lang = setCookieLanguage($lang);

    $return = 'English';

    if ($lang === 'it') {
        $return = 'Italian';
    }

    return [
        'short' => $lang,
        'full' => $return
    ];
}

/**
 * Scrive il cookie della lingua corrente
 *
 * @param string $lang Lingua corrente
 * @return void
 */
function setCookieLanguage($lang)
{
    global $config;
    if (!in_array($lang, $config['languages'])) {
        $lang = $config['default_language'];
    }
    setcookie('language', $lang, time() + 3600, '/');

    return $lang;
}

/**
 * Trasforma la data in formato italiano
 *
 * @param string $date Data
 * @param string $delimiter Delimitatore data
 * @return string
 */
function toDate($date, $delimiter = ".")
{
    if ($date === '0000-00-00') {
        return '-';
    }

    return date('d' . $delimiter . 'm' . $delimiter . 'Y', strtotime($date));
}

/**
 * Trasforma data e ora in formato italiano
 *
 * @param string $date Data
 * @param string $delimiter Delimitatore data
 * @return string
 */
function toDateTime($date, $delimiter = ".")
{
    if ($date === '0000-00-00 00:00:00') {
        return '-';
    }

    return date('d' . $delimiter . 'm' . $delimiter . 'Y' . ' - ' . 'H:i', strtotime($date));
}

/**
 * Trasforma l'ora in formato italiano
 *
 * @param string $date Data
 * @param string $delimiter Delimitatore data
 * @return string
 */
function toTime($date, $delimiter = ".")
{
    if ($date === '0000-00-00 00:00:00') {
        return '-';
    }

    return date('H:i', strtotime($date));
}

/**
 * Traduce in italiano il nome del mese
 *
 * @param int $key Numero del mese
 * @return string
 */
function nomiMesi($key)
{
    $mesi = [
        'Gennaio',
        'Febbraio',
        'Marzo',
        'Aprile',
        'Maggio',
        'Giugno',
        'Luglio',
        'Agosto',
        'Settembre',
        'Ottobre',
        'Novembre',
        'Dicembre',
    ];

    return $mesi[$key];
}

function timeDiffMinutes($start, $end)
{
    $start_date = new DateTime($start);
    $since_start = $start_date->diff(new DateTime($end));

    $minutes = $since_start->days * 24 * 60;
    $minutes += $since_start->h * 60;
    $minutes += $since_start->i;
    $seconds = $since_start->s;

    // If there are more than 30 spare seconds
    // we'll add 1 minute to the final count
    if ($seconds >= 30) {
        $minutes = $minutes + 1;
    }

    return $minutes;
}

/**
 * Time tracker
 *
 * @return float
 */
function benchmark()
{
    static $start = null;

    if (is_null($start)) {
        $start = getmicrotime();
    } else {
        $benchmark = getmicrotime() - $start;
        $start = getmicrotime();

        return round($benchmark, 2);
    }
}

/**
 * Get timestamp
 *
 * @return timestamp
 */
function getmicrotime()
{
    list($usec, $sec) = explode(" ", microtime());

    return ((float)$usec + (float)$sec);
}

/**
 * Convert memory size in human readable format
 *
 * @param int $size Size
 * @return float
 */
function convert($size)
{
    $unit = ['b', 'kb', 'mb', 'gb', 'tb', 'pb'];

    return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
}

/**
 * Genera l'hash di una password
 *
 * @param string $pass Password
 * @return string
 */
function encryptPassword(string $pass)
{
    return $pass;
}

/**
 * Adds a log record in the activities table
 *
 * @param integer $subjectId ID Oggetto
 * @param string $subjectType Nome oggetto
 * @param string $action Azione
 * @param string $info Informazioni aggiuntive
 * @return void
 */
function addLog(int $subjectId, string $subjectType, string $action, string $info)
{
    $userId = $_SESSION['Usr']['id'];

    setDb(
        "INSERT INTO activities
        (user_id, subject_id, subject_type, action, info, created_at, updated_at)
        VALUES (:user_id, :subject_id, :subject_type, :action, :info, :created_at, :updated_at)",
        [
            'user_id' => $userId,
            'subject_id' => $subjectId,
            'subject_type' => $subjectType,
            'action' => $action,
            'info' => $info,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]
    );
}
