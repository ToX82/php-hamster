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
    $data = file_get_contents(APP_ROOT . "/i18n/" . $lang . ".json");
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
 * Trasforma la data in formato leggibile
 *
 * @param string $date Data
 * @param string $delimiter Delimitatore data
 * @return string
 */
function toLocalizedDate($date, $delimiter = " ")
{
    if ($date === '0000-00-00') {
        return '-';
    }

    $strTime = strtotime($date);
    $dayName = __(date('l', $strTime));
    $monthName = __(date('M', $strTime));
    $dayNumber  = date('d', $strTime);

    return $dayName . $delimiter . $dayNumber . $delimiter . $monthName;
}

/**
 * Trasforma la data in formato leggibile
 *
 * @param string $date Data
 * @param string $delimiter Delimitatore data
 * @return string
 */
function toLocalizedShortDate($date, $delimiter = " ")
{
    if ($date === '0000-00-00') {
        return '-';
    }

    $strTime = strtotime($date);
    $monthName = __(date('M', $strTime));
    $dayNumber  = date('d', $strTime);

    return $dayNumber . $delimiter . $monthName;
}

function toMysqlDate($date)
{
    if (!date_parse($date)) {
        return null;
    }

    if (strpos($date, '/')) {
        if (strpos($date, ' ')) {
            $date = date_create_from_format('d/m/Y H:i:s', $date);
        } else {
            $date = date_create_from_format('d/m/Y', $date);
        }
    } else {
        if (strpos($date, ' ')) {
            $date = date_create_from_format('Y-m-d H:i:s', $date);
        } else {
            $date = date_create_from_format('Y-m-d', $date);
        }
    }

    if ($date === false) {
        return null;
    }
    return date_format($date, 'Y-m-d');
}

function toMysqlDateTime($date)
{
    if (strpos($date, '/')) {
        $date = date_create_from_format('d/m/Y H:i:s', $date);
    } else {
        $date = date_create_from_format('Y-m-d H:i:s', $date);
    }

    if ($date === false) {
        return null;
    }

    return date_format($date, 'Y-m-d H:i:s');
}

/**
 * Transforms minutes to Xh YYmin
 * In a horrible way. Better solutions are welcome
 *
 * @param int $minutes Minutes
 * @return string
 */
function toHours($minutes)
{
    $hours = floor($minutes / 60);
    $minutes = $minutes % 60;

    if ($hours > 0) {
        return $hours . "h " . $minutes . "min";
    }

    return $minutes . "min";
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

    return date('d' . $delimiter . 'm' . $delimiter . 'Y' . ' - ' . 'H:i:s', strtotime($date));
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

    return date('H:i:s', strtotime($date));
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

function sortArray($a, $b)
{
    if ($a == $b) {
        return 0;
    }

    return ($a > $b) ? -1 : 1;
}
