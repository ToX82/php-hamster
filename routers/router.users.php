<?php
/*
* Gestione utenti
*/

// These routes are for admins only. It's ok if an admin is logged as someone else...
if (isset($_SESSION['Usr'])) {
    if (in_array($_SESSION['Usr']['role_name'], ['admin']) || isset($_SESSION['Admin'])) {
        if (isPage('utenti')) {
            $users = new logics\Users();
            $pageTitle = "Elenco utenti";
            $users = $users->index(true);
            $sidebar = 'sidebar_users';

            $views[] = "templates/users/index.php";
        }
        if (isPage('impersona')) {
            $users = new logics\Users();
            $userId = filterInt(1);
            $users->loginAs($userId);
        }
    }

    // These routes are only for admins logged in as admins
    if (in_array($_SESSION['Usr']['role_name'], ['admin'])) {
        if (isPage('crea-utente')) {
            $users = new logics\Users();
            $pageTitle = "Crea nuovo utente";
            $sidebar = 'sidebar_users';

            $data = $users->new();
            $roles = $users->listRoles();
            $users = $users->list();
            $views[] = "templates/users/modifica.php";
        }
        if (isPage('modifica-utente')) {
            $users = new logics\Users();
            $pageTitle = "Modifica utente";
            $sidebar = 'sidebar_users';

            $userId = filterInt(1);
            $data = $users->get($userId);
            $roles = $users->listRoles();
            $users = $users->list();
            $views[] = "templates/users/modifica.php";
        }
        if (isPage('salva-utente')) {
            $users = new logics\Users();
            $data = filterArray($_POST);
            $users->save($data);
        }
        if (isPage('elimina-utente')) {
            $users = new logics\Users();
            $userId = filterInt(1);
            $status = checkExist('restore');
            $data = $users->delete($userId, $status);
        }
    }
}
