<?php

function setCookieRules()
{
    // **PREVENTING SESSION HIJACKING**
    // Prevents javascript XSS attacks aimed to steal the session ID
    ini_set('session.cookie_httponly', 1);

    // **PREVENTING SESSION FIXATION**
    // Session ID cannot be passed through URLs
    ini_set('session.use_only_cookies', 1);

    // Uses a secure connection (HTTPS) if possible
    ini_set('session.cookie_secure', 1);
}

/**
 * Check if a variable is number or not
 *
 * @param int $number Numero
 * @return int
 */
function isNumber($number)
{
    return (is_numeric($number)) ? $number : 0;
}

/**
 * Get a session variable
 *
 * @param string $name Session name
 * @return string
 */
function getSession($name)
{
    return $_SESSION[$name];
}

/**
 * Writes a session variable
 *
 * @param string $name Session name
 * @param string $value Session value
 * @return void
 */
function setSession($name, $value)
{
    $_SESSION[$name] = $value;
}

/**
 * Check if a GET variable exists
 *
 * @param string $val GET Variable's index
 * @return string variable's value
 */
function checkExist($key)
{
    $params = splitQueryParams();
    return isset($params[$key]);
}

/**
 * Parse a GET variable returning a clean string
 *
 * @param string $val GET Variable's index
 * @return string
 */
function filterString($key)
{
    $params = splitQueryParams();
    return filter_var($params[$key], FILTER_SANITIZE_STRING);
}

/**
 * Parse a GET variable returning a clean email address
 *
 * @param string $val GET Variable's index
 * @return string
 */
function filterEmail($key)
{
    $params = splitQueryParams();

    return filter_var($params[$key], FILTER_VALIDATE_EMAIL);
}

/**
 * Parse a GET variable returning a clean int value
 *
 * @param string $val GET Variable's index
 * @return string
 */
function filterInt($key)
{
    $params = splitQueryParams();
    return (int)filter_var($params[$key], FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Parse a GET variable returning a RAW string
 *
 * @param string $val GET Variable's index
 * @return string
 */
function filterRaw($key)
{
    $params = splitQueryParams();
    return filter_var($params[$key], FILTER_UNSAFE_RAW);
}

/**
 * Checks if a POST variable exist
 *
 * @param string $name POST Variable's name
 * @return string
 */
function checkExistPost($name)
{
    return isset($_POST[$name]);
}

/**
 * Parse a POST variable returning a clean string
 *
 * @param string $name POST Variable's name
 * @return string
 */
function filterStringPost($name)
{
    return filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);
}

/**
 * Parse a POST variable returning a clean int value
 *
 * @param string $name POST Variable's name
 * @return string
 */
function filterIntPost($name)
{
    return filter_input(INPUT_POST, $name, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Parse a POST variable returning a RAW string
 *
 * @param string $name POST Variable's name
 * @return string
 */
function filterRawPost($name)
{
    return filter_input(INPUT_POST, $name, FILTER_UNSAFE_RAW);
}

/**
 * Cycle an array cleaning all of its values
 *
 * @param string $array Array name
 * @return string
 */
function filterArray($array)
{
    $clean = [];
    foreach ($array as $key => $item) {
        if (is_array($item)) {
            $clean[$key] = filterArray($item);
        } else {
            switch ($key) {
                case 'id':
                    $clean[$key] = filter_var($item, FILTER_SANITIZE_NUMBER_INT);
                    break;
                case 'email':
                    $clean[$key] = filter_var($item, FILTER_VALIDATE_EMAIL);
                    break;
                case 'website':
                    $clean[$key] = filter_var($item, FILTER_VALIDATE_URL);
                    break;
                case 'testo':
                    $clean[$key] = filter_var($item, FILTER_UNSAFE_RAW);
                    break;
                case 'description':
                    $clean[$key] = filter_var($item, FILTER_UNSAFE_RAW);
                    break;
                case 'notes':
                    $clean[$key] = filter_var($item, FILTER_UNSAFE_RAW);
                    break;
                default:
                    $clean[$key] = filter_var($item, FILTER_SANITIZE_STRING);
                    break;
            }

            $clean[$key] = html_entity_decode($clean[$key]);
        }
    }

    return $clean;
}
