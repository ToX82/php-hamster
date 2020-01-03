<?php
/*
* Gestione utenti
*/
if (isPage('utenti') && (in_array($_SESSION['Usr']['role_name'], ['admin']) || isset($_SESSION['Admin']))) {
    $users = new logics\Users();
    $pageTitle = "Elenco utenti";
    $users = $users->index(true);
    $sidebar = 'sidebar_users';
    $views[] = "templates/users/index.php";
}
if (isPage('impersona') && (in_array($_SESSION['Usr']['role_name'], ['admin']) || isset($_SESSION['Admin']))) {
    $users = new logics\Users();
    $userId = filterInt(1);
    $users->loginAs($userId);
}
if (isPage('utenti-cancellati') && in_array($_SESSION['Usr']['role_name'], ['admin'])) {
    $users = new logics\Users();
    $pageTitle = "Elenco utenti cancellati";
    $users = $users->index(false);
    $deleted = true;
    $sidebar = 'sidebar_users';
    $views[] = "templates/users/index.php";
}
if (isPage('crea-utente') && in_array($_SESSION['Usr']['role_name'], ['admin'])) {
    $users = new logics\Users();
    $pageTitle = "Crea nuovo utente";
    $sidebar = 'sidebar_users';
    $data = $users->new();
    $roles = $users->listRoles();
    $users = $users->list();
    $views[] = "templates/users/modifica.php";
}
if (isPage('modifica-utente') && in_array($_SESSION['Usr']['role_name'], ['admin'])) {
    $users = new logics\Users();
    $pageTitle = "Modifica utente";
    $sidebar = 'sidebar_users';
    $userId = filterInt(1);
    $data = $users->get($userId);
    $roles = $users->listRoles();
    $users = $users->list();
    $views[] = "templates/users/modifica.php";
}
if (isPage('salva-utente') && in_array($_SESSION['Usr']['role_name'], ['admin'])) {
    $users = new logics\Users();
    $data = filterArray($_POST);
    $users->save($data);
}
if (isPage('elimina-utente') && in_array($_SESSION['Usr']['role_name'], ['admin'])) {
    $users = new logics\Users();
    $userId = filterInt(1);
    $status = checkExist('restore');
    $data = $users->delete($userId, $status);
}
if (isPage('setlang')) {
    $lang = filterString(1);
    setCookieLanguage($lang);
    reload('/');
}
