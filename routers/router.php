<?php

$params = trim($_SERVER['QUERY_STRING'], '/');
$params = explode("/", $params);
$params = array_filter($params, 'strlen');
$users = new logics\Users();

// Array con le pagine di template da includere
$views = [];
$sidebar = 'sidebar';

// Pagine visibili da tutti (loggati oppure no)
if (isPage('setlang')) {
    $lang = filterString(1);
    setCookieLanguage($lang);
    reload('/');
}

if (isPage('login')) {
    if (checkExistPost("email")) {
        $users->login();
    }
    $views[] = "templates/users/login.php";
}

if (isPage('register')) {
    $pageTitle = "Registrazione";

    if (isset($_POST['email'])) {
        $data = filterArray($_POST);
        $users->register($data);
    } else {
        $users = $users->new();
    }
    $views[] = "templates/users/register.php";
}

if (isPage('logout')) {
    $users->logout();
}

// Se l'utente non è loggato reindirizzo alla pagina di login
if (!isset($_SESSION['Usr'])) {
    if (isset($_COOKIE['hamster'])) {
        $users->autologin();
    } elseif (empty($views)) {
        reload('login');
    }
}

// Se non esiste un parametro nella URL seleziono la home page
if (empty($params)) {
    reload('dashboard');
}

// Carico gli altri routers
require_once APP_ROOT . 'routers/router.users.php';
require_once APP_ROOT . 'routers/router.activities.php';
