<?php
/**
 * Initialization instructions
 *
 * @return void
 */
function init()
{
    require_once __DIR__ . "/utilities.php";
    require_once __DIR__ . "/paths.php";
    require_once __DIR__ . "/security.php";

    if (isset($_SERVER['HTTPS'])) {
        setCookieRules();
    }

    header('Content-type: text/html;charset=utf-8');
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    ob_start();
    session_start();

    // Start session
    $_SESSION['query_count'] = 0;
    if (isset($_SESSION['mysqlError'])) {
        unset($_SESSION['mysqlError']);
    }
}

/**
 * Debug function
 *
 * @param mixed $var Variable to be printed (string or array)
 * @return void
 */
function debug($var)
{
    echo "<pre>";
        print_r($var);
    echo "</pre>";
}

/**
 * Opens a DB connection
 *
 * @return $connessione
 */
function openDb()
{
    require 'config.php';
    static $connessione;
    if (!isset($connessione)) {
        try {
            $connessione = new PDO('mysql:host=' . $config['db']['host'] . ';dbname=' . $config['db']['db'], $config['db']['user'], $config['db']['pass']);
            $connessione->exec("set names utf8");
            $connessione->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connessione->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    return $connessione;
}

/**
 * Read something from DB
 *
 * @param string $sql The Query
 * @param array $params Query params
 * @param bool $debug Wether to print out the generated query
 * @return array
 */
function getDb($sql, $params = null, $debug = false)
{
    static $connessione;
    if ($sql != '') {
        $_SESSION['query_count'] ++;

        $riga = null;
        $connessione = openDb();
        $query = $connessione->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $query->bindValue(":{$key}", $value, PDO::PARAM_STR);
            }
        }

        if ($debug === true) {
            echo "<h2>Query generata:</h2>";
            debugPdo($sql, $params);
        }

        $query->execute();

        if ($query->errorCode() == 0) {
            $riga = $query->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $errors = $query->errorInfo();
            if (!isset($_SESSION['mysqlError'])) {
                $_SESSION['mysqlError'] = [];
            }
            $_SESSION['mysqlError'][] = $errors[2] . " | " . $sql;
        }

        return $riga;
    }
}

/**
 * Write something to DB
 *
 * @param string $sql The Query
 * @param array $params Query params
 * @param bool $debug Wether to print out the generated query
 * @return int Last inserted ID
 */
function setDb($sql, $params = null, $debug = false)
{
    static $connessione;
    if ($sql != '') {
        $_SESSION['query_count'] ++;

        $connessione = openDb();
        $query = $connessione->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $name => $value) {
                $query->bindValue(":{$name}", $value, PDO::PARAM_STR);
            }
        }

        if ($debug === true) {
            echo "<h2>Query generata:</h2>";
            debugPdo($sql, $params);
        }

        $query->execute();

        if ($query->errorCode() != 0) {
            $errors = $query->errorInfo();
            debug("<h3>" . $errors[2] . "</h3>" . $sql);
            debug($errors);
            die;
        }

        return $connessione->lastInsertId();
    }
}

/**
 * Prints a generated query
 *
 * @param string $sql The Query
 * @param array $params Query params
 * @return mixed
 */
function debugPdo($sql, $params)
{
    $keys = [];
    $values = $params;
    foreach ($params as $key => $value) {
        // check if named parameters (':param') or anonymous parameters ('?') are used
        if (is_string($key)) {
            $keys[] = '/:' . $key . '/';
        } else {
            $keys[] = '/[?]/';
        }
        // bring parameter into human-readable format
        if (is_string($value)) {
            $values[$key] = "'" . $value . "'";
        } elseif (is_array($value)) {
            $values[$key] = implode(',', $value);
        } elseif (is_null($value)) {
            $values[$key] = 'NULL';
        }
    }
    $sql = preg_replace($keys, $values, $sql, 1);
    debug($sql);

    return $sql;
}
