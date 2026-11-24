<?php
require_once __DIR__ . '/../models/SecuriteUtilisateur.php';

class AmirAuth
{
    private $model;

    public function __construct($model) {
        $this->model = $model;
    }

    public static function demarrer()
    {
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('AQUAFLOW_AMIR');
        session_set_cookie_params(['httponly' => true, 'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'samesite' => 'Lax', 'path' => '/']);
        session_start();
        if (isset($_SESSION['last_seen']) && time() - $_SESSION['last_seen'] > 1800) {
            self::effacer();
        }
        $_SESSION['last_seen'] = time();
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }
    }

    public static function csrf()
    {
        $token = $_POST['csrf'] ?? '';
        if (!is_string($token) || !hash_equals($_SESSION['csrf'] ?? '', $token) || $token === '') {
            http_response_code(403);
            throw new DomainException('Formulaire expiré. Recharge la page.');
        }
    }

    public static function limiter($cle, $maximum = 5)
    {
        $securite = new SecuriteUtilisateur(AmirDatabase::connexion());
        $securite->limiter($cle, $maximum);
    }

    public function utilisateur()
    {
        if (!isset($_SESSION['auth'])) {
            return null;
        }
        $session = $_SESSION['auth'];
        $user = $this->model->getById($session['id']);
        if (!$user || $user['statut'] !== 'Actif' || (int) $user['version'] !== $session['version']) {
            self::effacer();
            return null;
        }
        return $user;
    }

    public static function effacer()
    {
        $_SESSION = [];
        session_regenerate_id(true);
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    public function connecter($post)
    {
        $email = strtolower(champ($post, 'email', 190));
        $password = $post['password'] ?? '';
        self::limiter('login-ip-' . ($_SERVER['REMOTE_ADDR'] ?? 'local'), 30);
        self::limiter('login-email-' . $email);
        $user = $this->model->getByEmail($email);
        $hash = $user['password'] ?? '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi.';
        if (!is_string($password) || strlen($password) > 72 || !password_verify($password, $hash) || !$user || $user['statut'] !== 'Actif') {
            throw new DomainException('Email ou mot de passe incorrect, ou compte indisponible.');
        }
        return $this->premierFacteur($user);
    }

    public function premierFacteur($user)
    {
        if (!$user || $user['statut'] !== 'Actif') {
            throw new DomainException('Ce compte est indisponible.');
        }
        self::effacer();
        if ($user['totp_secret']) {
            $_SESSION['pending'] = ['id' => (int) $user['id'], 'version' => (int) $user['version'], 'expires' => time() + 300];
            return 'challenge';
        }
        $this->terminer($user);
        return 'profile';
    }

    public function terminer($user)
    {
        session_regenerate_id(true);
        unset($_SESSION['pending']);
        $_SESSION['auth'] = ['id' => (int) $user['id'], 'version' => (int) $user['version'], 'verified_at' => time()];
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    public function terminer2fa($code)
    {
        $pending = $_SESSION['pending'] ?? [];
        $user = $this->model->getById($pending['id'] ?? 0);
        if (!$user || $user['statut'] !== 'Actif' || ($pending['expires'] ?? 0) < time() || (int) $user['version'] !== ($pending['version'] ?? 0)) {
            self::effacer();
            throw new DomainException('Connexion expirée. Reconnecte-toi.');
        }
        AmirTotp::verifier($user, $code);
        $this->terminer($user);
    }

    public static function recente()
    {
        if (time() - ($_SESSION['auth']['verified_at'] ?? 0) > 300) {
            throw new DomainException('Pour cette action, déconnecte-toi puis reconnecte-toi afin de confirmer ton identité.');
        }
    }

    public static function administrateur($user)
    {
        if (!$user || (int) $user['id_role'] !== 3) {
            http_response_code(403);
            throw new DomainException('Cette page est réservée aux administrateurs.');
        }
    }
}
