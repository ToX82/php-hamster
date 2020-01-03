<?php
namespace Logics;

class Users
{

    /**
     * Autologin - in base alla presenza di un cookie
     *
     * @return void
     */
    public function autologin()
    {
        $token = $_COOKIE['hamster'];

        if (strlen($token) !== 80) {
            $this->logout();
        }

        $user = getDb(
            "SELECT email
            FROM autologins
            WHERE (`token` = :token)
            AND `expires` >= :expires",
            [
                "token" => $token,
                "expires" => date('Y-m-d H:i:s')
            ]
        );

        if (empty($user)) {
            $this->logout();
        }

        $this->login($user[0]['email'], true);
    }

    /**
     * Disconnette l'utente, eliminando cookies e dati di sessione
     *
     * @return void
     */
    public function logout()
    {
        unset($_COOKIE['hamster']);
        setcookie('hamster', null, -1, '/');

        $sessionData = array_keys($_SESSION);
        foreach ($sessionData as $key) {
            unset($_SESSION[$key]);
        }

        reload('/');
    }

    /**
     * Login, in base ai dati inseriti nella form o da autologin (cookie)
     *
     * @return void
     */
    public function login($email = null, $autologin = false)
    {
        if ($email === null) {
            $email = filterStringPost("email");
        }

        $user = getDb(
            "SELECT users.id, users.username, users.password, users.email,
            roles.id AS role_id, roles.name AS role_name
            FROM users
            LEFT JOIN roles_users ON users.id = roles_users.user_id
            LEFT JOIN roles ON roles.id = roles_users.role_id
            WHERE (`email` = :email)
            AND `active` = 1",
            [
                "email" => $email
            ]
        );

        //verifica che l'array non sia vuoto
        if (empty($user)) {
            $msg = ['type' => 'error', 'text' => __('email_or_password_incorrect')];
            $_SESSION['msg'] = $msg;
            reload("/");
        } else {
            // Se non sto facendo l'autologin verifico email e password
            if ($autologin === false) {
                $password = filterStringPost("password");
                if (!password_verify($password, $user[0]['password'])) {
                    $msg = ['type' => 'error', 'text' => __('email_or_password_incorrect')];
                    $_SESSION['msg'] = $msg;
                    reload("/");
                }
            }
        }

        // Altrimenti imposta i valori in sessione
        $_SESSION['Usr'] = $user[0];

        if ($_SESSION['Usr']['role_name'] === 'admin') {
            $_SESSION['Admin'] = $_SESSION['Usr'];
        }


        $this->saveLogin($user[0]);

        if ($autologin === false) {
            $this->setAutoLogin($user[0]);
        }

        reload("/");
    }

    /**
     * Imposta i dati per l'autologin (imposta il cookie e salva i dati nel db)
     *
     * @param array $user Dati utente
     * @return void
     */
    public function setAutoLogin($user)
    {
        $tokenLength = 80;
        $token = substr(bin2hex(random_bytes(ceil($tokenLength))), 0, $tokenLength);
        $expires = time() + (86400 * 30);

        setDb(
            "INSERT INTO autologins
            (email, token, created, expires)
            VALUES ( :email, :token, :created, :expires )",
            [
                'email' => $user['email'],
                'token' => $token,
                'created' => date('Y-m-d H:i:s'),
                'expires' => date('Y-m-d H:i:s', $expires)
            ]
        );

        $cookie_name = "hamster";
        $cookie_value = $token;
        setcookie($cookie_name, $cookie_value, $expires, "/");
    }

    /**
     * Login, in base ai dati inseriti nella form o da autologin (cookie)
     *
     * @return void
     */
    public function loginAs($userId)
    {
        if (!is_numeric($userId)) {
            reload("/");
        }

        $user = getDb(
            "SELECT users.id, users.username, users.password, users.email,
            roles.id AS role_id, roles.name AS role_name
            FROM users
            LEFT JOIN roles_users ON users.id = roles_users.user_id
            LEFT JOIN roles ON roles.id = roles_users.role_id
            WHERE (users.id = :id)",
            [
                "id" => $userId
            ]
        );

        //verifica che l'array non sia vuoto
        if (empty($user)) {
            $msg = ['type' => 'error', 'text' => __('user_not_valid')];
            $_SESSION['msg'] = $msg;
            reload("/utenti");
        }

        // Altrimenti imposta i valori in sessione
        if (!isset($_SESSION['Admin'])) {
            if ($_SESSION['Usr']['role_name'] === 'admin') {
                $_SESSION['Admin'] = $_SESSION['Usr'];
            }
        }

        // Se mi sto loggando come admin elimino la vecchia sessione
        if ($user[0]['role_name'] === 'admin') {
            $_SESSION['Admin'] = $user[0];
        }

        $_SESSION['Usr'] = $user[0];

        reload("/");
    }

    /**
     * Salva i dati del login nel record dell'utente
     *
     * @param array $user Dati utente
     * @return void
     */
    public function saveLogin($user)
    {
        $date = date('Y-m-d H:i:s');
        $ipAddress = $_SERVER['REMOTE_ADDR'];

        setDb(
            "UPDATE users
            SET last_login_at = :last_login_at,
            last_login_ip = :last_login_ip,
            login_count = (login_count + 1)
            WHERE id = :id",
            [
                'last_login_at' => $date,
                'last_login_ip' => $ipAddress,
                'id' => $user['id']
            ]
        );
    }

    /**
     * Genera l'elenco degli utenti
     *
     * @param boolean $active Seleziona gli utenti attivi o quelli cancellati
     * @return array
     */
    public function index(bool $active = true)
    {
        $users = getDb(
            "SELECT users.*, roles.name
            FROM users
            LEFT JOIN roles_users ON roles_users.user_id = users.id
            LEFT JOIN roles ON roles_users.role_id = roles.id
            WHERE users.active = :active
            GROUP BY users.id
            ORDER BY created DESC",
            [
                'active' => $active
            ]
        );

        return $users;
    }

    /**
     * Elimina un utente (lo imposta come non active)
     *
     * @param int $userId ID Utente
     * @param boolean $status
     * @return void
     */
    public function delete(int $userId, bool $status)
    {
        $status = ($status === true) ? 1 : 0;

        setDb(
            "UPDATE users SET active = :active, modified = :modified WHERE id = :id",
            [
                'active' => $status,
                'modified' => date('Y-m-d H:i:s'),
                'id' => $userId
            ]
        );

        if ($status === 1) {
            $_SESSION['msg'] = ['type' => 'success', 'text' => __('user_restored_correctly')];
            reload('/utenti');
        } else {
            $_SESSION['msg'] = ['type' => 'success', 'text' => __('user_deleted_correctly')];
            reload('/utenti-cancellati');
        }
    }

    /**
     * Genera una lista degli utenti (per select)
     *
     * @return array
     */
    public function list()
    {
        $users = getDb(
            "SELECT id, username
            FROM users
            ORDER BY username ASC"
        );

        return $users;
    }

    /**
     * Genera una lista dei ruoli (per select)
     *
     * @return array
     */
    public function listRoles()
    {
        $roles = getDb(
            "SELECT id, `description`
            FROM roles
            WHERE `hidden` != 1
            ORDER BY `position` ASC"
        );

        return $roles;
    }

    /**
     * Genera un array di dati vuoti per la creazione di un nuovo utente
     *
     * @return array
     */
    public function new()
    {
        $data = [
            'id' => '',
            'username' => '',
            'email' => '',
            'role_id' => [],
            'active' => 1,
        ];

        if (isset($_SESSION['SavingUserData'])) {
            foreach ($_SESSION['SavingUserData'] as $key => $prev) {
                $data[$key] = $prev;
            }
        }

        return $data;
    }

    /**
     * Preleva i dati di un utente
     *
     * @param int $userId ID Utente
     * @return void
     */
    public function get(int $userId)
    {
        $user = getDb(
            "SELECT users.*, roles_users.role_id
            FROM users
            LEFT JOIN roles_users ON users.id = roles_users.user_id
            WHERE id = :id",
            [
                'id' => $userId
            ]
        );

        if (empty($user)) {
            $_SESSION['msg'] = ['type' => 'error', 'text' => __('user_not_found')];
            reload('/utenti');
        }

        return $user[0];
    }

    /**
     * Preleva gli utenti correlati all'agente selezionato
     *
     * @param integer $userId ID Utente
     * @return array
     */
    public static function getRelatedUsers(int $userId)
    {
        $relatedUsersRoles = 2;

        $clientships = getDb(
            "SELECT users.id, users.username
            FROM users
            LEFT JOIN roles_users ON roles_users.user_id = users.id
            WHERE roles_users.role_id IN ( $relatedUsersRoles )
            GROUP BY users.username",
            [
            ]
        );

        $return = [
            0 => [
                'id' => $userId,
                'username' => ''
            ]
        ];

        $return = array_merge($return, $clientships);

        return $return;
    }

    public function impersonate(int $userId)
    {
        $user = getDb(
            "SELECT users.id, users.username, users.password, users.email,
            roles.id AS role_id, roles.name AS role_name
            FROM users
            LEFT JOIN roles_users ON users.id = roles_users.user_id
            LEFT JOIN roles ON roles.id = roles_users.role_id
            WHERE users.id = :id",
            [
                "id" => $userId
            ]
        );

        $_SESSION['Usr'] = $user[0];
    }

    /**
     * Registrazione utenti
     *
     * @param array $data Array di dati utente
     * @return void
     */
    public function register(array $data)
    {
        $data['role_id'] = 2;
        $data['password'] = $this->checkPassword($data['password1'], $data['password2']);
        $this->checkRequired($data);
        $this->checkDuplicate($data);
        $this->create($data);

        $_SESSION['msg'] = ['type' => 'success', 'text' => __('thank_you_for_registering')];
        reload('/login');
    }

    /**
     * Funzione generica di salvataggio utenti. In base allo user_id verifico se fare insert o update
     *
     * @param array $data Array di dati utente
     * @return void
     */
    public function save(array $data)
    {
        $_SESSION['SavingUserData'] = $data;

        // In creazione utente verifico sempre la password
        // Altrimenti la verifico solo se viene impostata
        if ($data['password1'] !== '') {
            $data['password'] = $data['password1'];
        }

        // Verifico che i campi obbligatori siano compilati
        $this->checkRequired($data);

        if ($data['id'] === '') {
            $this->checkDuplicate($data);
            $userId = $this->create($data);
            $_SESSION['msg'] = ['type' => 'success', 'text' => __('user_created_correctly')];
        } else {
            $this->checkDuplicate($data, $data['id']);
            $userId = $this->update($data);
            $_SESSION['msg'] = ['type' => 'success', 'text' => __('user_updated_correctly')];
        }

        unset($_SESSION['SavingUserData']);

        reload('/utenti');
    }

    /**
     * Salva i dati di un nuovo utente
     *
     * @param array $data Array di dati utente
     * @return int ID utente
     */
    public function create(array $data)
    {
        $userId = setDb(
            "INSERT INTO users
            (username, email, active, password, created, modified)
            VALUES
            (:username, :email, :active, :password, :created, :modified)
            ",
            [
                'username' => $data['username'],
                'email' => $data['email'],
                'password' => $data['password'],
                'active' => isset($data['active']) ? 1 : 1,
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s')
            ]
        );

        $this->updateRoles($userId, $data);

        return $userId;
    }

    /**
     * Salva i dati di un utente già esistente (modifica)
     *
     * @param array $data Array di dati utente
     * @return int ID utente
     */
    public function update(array $data)
    {
        $userId = $data['id'];

        setDb(
            "UPDATE users
            SET username = :username, email = :email, active = :active, created = :created
            WHERE id = :id
            ",
            [
                'username' => $data['username'],
                'email' => $data['email'],
                'active' => isset($data['active']) ? 1 : 0,
                'created' => date('Y-m-d H:i:s'),
                'id' => $userId
            ]
        );

        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            setDb(
                "UPDATE users
                SET `password` = :password
                WHERE id = :id
                ",
                [
                    'password' => $data['password'],
                    'id' => $userId
                ]
            );
        }

        $this->updateRoles($userId, $data);

        return $userId;
    }

    /**
     * Salva i dati utente nella tabella ruoli
     *
     * @param int $userId ID Utente
     * @param array $data Dati utente
     * @return void
     */
    public function updateRoles(int $userId, array $data)
    {
        setDb(
            "DELETE
            FROM roles_users
            WHERE user_id = :user_id",
            [
                'user_id' => $userId
            ]
        );

        setDb(
            "INSERT INTO roles_users (role_id, user_id)
            VALUES (:role_id, :user_id)",
            [
                'role_id' => $data['role_id'],
                'user_id' => $userId
            ]
        );
    }

    /**
     * Verifica che i campi obbligatori siano compilati correttamente
     *
     * @param array $data Array di dati
     * @return void
     */
    public function checkRequired(array $data)
    {
        $fields = [
            'username' => 'Nome utente',
            'email' => 'Email',
        ];

        $error = '';
        foreach ($fields as $field => $name) {
            if ($data[$field] == '') {
                $error .= __('the_field_is_mandatory');
            }
        }

        if ($error !== '') {
            $_SESSION['msg'] = ['type' => 'error', 'text' => $error];
            reload($_SERVER['HTTP_REFERER']);
        }
    }

    /**
     * Controlla la validità di una password
     *
     * @param string $password1 Password 1
     * @param string $password2 Password 2 (verifica)
     * @return mixed
     */
    public function checkPassword(string $password1, string $password2)
    {
        $error = false;

        $password1 = trim($password1);
        if ($password1 !== $password2) {
            $error = true;
            $message = __('passwords_dont_match');
        }
        if (strlen($password1) < 8) {
            $error = true;
            $message = __('password_should_be_8_chars_or_more');
        }
        if ($error === true) {
            $_SESSION['msg'] = ['type' => 'error', 'text' => $message];
            reload($_SERVER['HTTP_REFERER']);
        }

        return password_hash($password1, PASSWORD_DEFAULT);
    }

    /**
     * Verifica se un utente è duplicato
     *
     * @param array $data Array di items
     * @param int $itemId ID Item
     * @return void
     */
    public function checkDuplicate(array $data, int $itemId = null)
    {
        $fields = [
            'email' => 'Email'
        ];

        $idFilter = '';
        if ($itemId !== null) {
            $idFilter = ' AND id != ' . $itemId;
        }

        $error = '';
        foreach ($fields as $field => $name) {
            $duplicate = getDb(
                "SELECT id
                FROM users
                WHERE $field = :search
                $idFilter",
                [
                    'search' => $data[$field]
                ]
            );

            if (!empty($duplicate)) {
                $error .= __('email_address_already_registered');
            }
        }

        if ($error !== '') {
            $_SESSION['msg'] = ['type' => 'error', 'text' => $error];
            reload($_SERVER['HTTP_REFERER']);
        }
    }
}
