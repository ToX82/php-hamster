<?php
$params = trim($_SERVER['QUERY_STRING'], '/');
$params = explode("/", $params);
$params = array_filter($params, 'strlen');
$users = new logics\Users();

// Array con le pagine di template da includere
$views = [];
$sidebar = 'sidebar';

require_once BASE_PATH . 'routers/router.public.php';

// Pagine visibili da tutti (loggati oppure no)
if (isPage('login')) {
    if (checkExistPost("email")) {
        $users->login();
    }
    $layout = 'fullwidth';
    $views[] = "templates/users/login.php";
}
if (isPage('register')) {
    $pageTitle = "Registrazione";
    $layout = 'fullwidth';

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

// Se l'utente è un admin prelevo la lista di utenti
if (isset($_SESSION['Admin'])) {
    if (in_array($_SESSION['Admin']['role_name'], ['admin'])) {
        $userId = $_SESSION['Admin']['id'];
        $usersList = logics\Users::getRelatedUsers($userId);
    }
}

// Se non esiste un parametro nella URL seleziono la home page
if (empty($params)) {
    reload('/dashboard');
}

// Carico gli altri routers
require_once BASE_PATH . 'routers/router.users.php';
require_once BASE_PATH . 'routers/router.dashboard.php';
