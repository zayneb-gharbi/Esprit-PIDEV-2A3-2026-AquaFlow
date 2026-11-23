<?php
/**
 * Controller/AuthController.php
 * Toute la logique métier : SQL préparé, sessions, CSRF, mots de passe, reset par email.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/mail.php';
require_once __DIR__ . '/../config/google.php';
require_once __DIR__ . '/../Model/UserModel.php';

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnexion();
    }

    // ==============================================================
    // SESSION / RÔLES
    // ==============================================================
    public static function isAuthenticated(): bool
    {
        return !empty($_SESSION['user_id']);
    }

    public static function requireAuth(): void
    {
        if (!self::isAuthenticated()) {
            header('Location: index.php?action=login');
            exit;
        }
    }

    public static function requireRole(string $role): void
    {
        self::requireAuth();
        if (($_SESSION['role'] ?? null) !== $role) {
            http_response_code(403);
            die('Access denied: you do not have the required permissions.');
        }
    }

    // ==============================================================
    // CSRF
    // ==============================================================
    public function getCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    private function verifyCsrfToken(?string $token): bool
    {
        if (!$token || empty($_SESSION['csrf_token'])) {
            return false;
        }
        return hash_equals($_SESSION['csrf_token'], $token);
    }

    // ==============================================================
    // DATA ACCESS (requêtes préparées)
    // ==============================================================
    private function findByEmail(string $email): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    public function findById(int $id): ?UserModel
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ? UserModel::fromArray($row) : null;
    }

    private function updatePassword(int $userId, string $plainPassword): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password = :p WHERE id = :id');
        $stmt->execute(['p' => password_hash($plainPassword, PASSWORD_DEFAULT), 'id' => $userId]);
    }

    // ==============================================================
    // LOGIN / LOGOUT  (tâche : s'authentifier)
    // ==============================================================
    public function login(array $post): array
    {
        $errors = [];
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ["Invalid security token. Please try again."];
        }

        $email = trim($post['email'] ?? '');
        $password = $post['password'] ?? '';
        if ($email === '' || $password === '') {
            return ["Veuillez saisir votre email et votre mot de passe."];
        }

        $user = $this->findByEmail($email);

        // Message volontairement générique : ne jamais révéler si l'email existe
        if (!$user || !password_verify($password, $user->getPassword())) {
            return ["Email ou mot de passe incorrect."];
        }
        if ($user->getStatus() === 'bloque') {
            return ["Votre compte est bloqué. Contactez l'administrateur."];
        }
        if ($user->getStatus() === 'en_attente') {
            return ["Votre compte n'a pas encore été approuvé par l'administrateur."];
        }

        $this->startSession($user);
        return $errors; // vide = succès
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
        }
        session_destroy();
    }

    // ==============================================================
    // VALIDATION DU NOUVEAU MOT DE PASSE (partagée)
    // ==============================================================
    private function validateNewPassword(string $new, string $confirm): array
    {
        $errors = [];
        if (strlen($new) < 8) {
            $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
        }
        if (!preg_match('/[A-Za-z]/', $new) || !preg_match('/\d/', $new)) {
            $errors[] = "Le mot de passe doit contenir au moins une lettre et un chiffre.";
        }
        if ($new !== $confirm) {
            $errors[] = "Les mots de passe ne correspondent pas.";
        }
        return $errors;
    }

    // ==============================================================
    // CHANGER MOT DE PASSE (utilisateur connecté)
    // ==============================================================
    public function changePassword(int $userId, array $post): array
    {
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ["Invalid security token. Please try again."];
        }

        $current = $post['current_password'] ?? '';
        $new = $post['new_password'] ?? '';
        $confirm = $post['confirm_password'] ?? '';

        $user = $this->findById($userId);
        if (!$user) {
            return ["Utilisateur introuvable."];
        }
        if (!password_verify($current, $user->getPassword())) {
            return ["Le mot de passe actuel est incorrect."];
        }

        $errors = $this->validateNewPassword($new, $confirm);
        if ($new === $current) {
            $errors[] = "Le nouveau mot de passe doit être différent de l'ancien.";
        }
        if (!empty($errors)) {
            return $errors;
        }

        $this->updatePassword($userId, $new);

        // Invalide les liens de reset encore en attente
        $this->db->prepare('UPDATE password_resets SET used_at = NOW() WHERE user_id = :id AND used_at IS NULL')
                 ->execute(['id' => $userId]);

        session_regenerate_id(true);

        // Notification par email
        Mailer::send(
            $user->getEmail(),
            'Votre mot de passe a été modifié',
            '<p>Bonjour ' . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>Le mot de passe de votre compte AquaFlow vient d\'être modifié le ' . date('d/m/Y à H:i') . '.</p>'
            . '<p>Si ce n\'est pas vous, utilisez immédiatement « Mot de passe oublié » et contactez l\'administrateur.</p>'
        );
        return [];
    }

    // ==============================================================
    // RESET MOT DE PASSE  (étape 1 : demande + email)
    // ==============================================================
    public function requestPasswordReset(array $post): array
    {
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ["Invalid security token. Please try again."];
        }
        $email = trim($post['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ["Adresse email invalide."];
        }

        $user = $this->findByEmail($email);
        // Réponse identique que l'email existe ou non (anti-énumération de comptes)
        if ($user && $user->getStatus() !== 'bloque') {
            // un seul lien valide à la fois
            $this->db->prepare('DELETE FROM password_resets WHERE user_id = :id')->execute(['id' => $user->getId()]);

            $token = bin2hex(random_bytes(32));
            $stmt = $this->db->prepare(
                'INSERT INTO password_resets (user_id, token_hash, expires_at)
                 VALUES (:uid, :h, DATE_ADD(NOW(), INTERVAL 1 HOUR))'
            );
            $stmt->execute(['uid' => $user->getId(), 'h' => hash('sha256', $token)]);

            $link = APP_URL . '/index.php?action=reset_password&token=' . $token;
            Mailer::send(
                $user->getEmail(),
                'Réinitialisation de votre mot de passe',
                '<p>Bonjour ' . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . ',</p>'
                . '<p>Pour choisir un nouveau mot de passe, cliquez sur ce lien (valable 1 heure) :</p>'
                . '<p><a href="' . $link . '">' . $link . '</a></p>'
                . '<p>Si vous n\'êtes pas à l\'origine de cette demande, ignorez ce message.</p>'
            );
        }
        return [];
    }

    // ==============================================================
    // RESET MOT DE PASSE  (étape 2 : vérif du jeton + nouveau mot de passe)
    // ==============================================================
    /** Retourne la ligne password_resets si le jeton est valide, sinon null */
    public function findValidReset(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        $stmt = $this->db->prepare(
            'SELECT * FROM password_resets
             WHERE token_hash = :h AND used_at IS NULL AND expires_at > NOW() LIMIT 1'
        );
        $stmt->execute(['h' => hash('sha256', $token)]);
        return $stmt->fetch() ?: null;
    }

    public function resetPassword(array $post): array
    {
        if (!$this->verifyCsrfToken($post['csrf_token'] ?? null)) {
            return ["Invalid security token. Please try again."];
        }
        $reset = $this->findValidReset((string) ($post['token'] ?? ''));
        if (!$reset) {
            return ["Ce lien est invalide ou a expiré. Refaites une demande."];
        }

        $errors = $this->validateNewPassword($post['new_password'] ?? '', $post['confirm_password'] ?? '');
        if (!empty($errors)) {
            return $errors;
        }

        $this->db->beginTransaction();
        try {
            $this->updatePassword((int) $reset['user_id'], $post['new_password']);
            $this->db->prepare('UPDATE password_resets SET used_at = NOW() WHERE id = :id')
                     ->execute(['id' => $reset['id']]);
            $this->db->commit();
        } catch (Throwable $e) {
            $this->db->rollBack();
            return ["Une erreur est survenue. Réessayez."];
        }

        $user = $this->findById((int) $reset['user_id']);
        if ($user) {
            Mailer::send(
                $user->getEmail(),
                'Votre mot de passe a été réinitialisé',
                '<p>Bonjour,</p><p>Votre mot de passe AquaFlow a bien été réinitialisé le '
                . date('d/m/Y à H:i') . '.</p><p>Si ce n\'est pas vous, contactez l\'administrateur.</p>'
            );
        }
        return [];
    }

    // ==============================================================
    // SESSION (partagée par login classique et login Google)
    // ==============================================================
    private function startSession(UserModel $user): void
    {
        session_regenerate_id(true); // anti fixation de session
        $_SESSION['user_id'] = $user->getId();
        $_SESSION['username'] = $user->getUsername();
        $_SESSION['role'] = $user->getRole();
        $_SESSION['last_activity'] = time();
    }

    // ==============================================================
    // CONNEXION AVEC GOOGLE (OAuth 2.0 - Authorization Code)
    // ==============================================================
    public function isGoogleConfigured(): bool
    {
        return GOOGLE_CLIENT_ID !== '' && GOOGLE_CLIENT_SECRET !== '';
    }

    /** URL de la page Google de choix du compte ; le "state" protège contre le CSRF */
    public function getGoogleAuthUrl(): string
    {
        $_SESSION['google_state'] = bin2hex(random_bytes(16));
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'state'         => $_SESSION['google_state'],
            'prompt'        => 'select_account',
        ]);
    }

    /** Traite le retour de Google. Retourne un tableau d'erreurs (vide = connecté). */
    public function loginWithGoogle(array $get): array
    {
        if (!$this->isGoogleConfigured()) {
            return ["La connexion Google n'est pas configurée (config/google.local.php)."];
        }
        if (!empty($get['error'])) {
            return ["Connexion Google annulée."];
        }

        $state = (string) ($_SESSION['google_state'] ?? '');
        unset($_SESSION['google_state']);
        if ($state === '' || !hash_equals($state, (string) ($get['state'] ?? ''))) {
            return ["Requête Google invalide. Réessayez."];
        }
        $code = (string) ($get['code'] ?? '');
        if ($code === '') {
            return ["Réponse Google incomplète. Réessayez."];
        }

        // 1) échange du code contre un access_token (serveur à serveur, avec le secret)
        $token = $this->httpRequest('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]);
        if (empty($token['access_token'])) {
            return ["Impossible de valider la connexion avec Google."];
        }

        // 2) infos du compte Google
        $info = $this->httpRequest('https://openidconnect.googleapis.com/v1/userinfo', null, $token['access_token']);
        $email = strtolower(trim((string) ($info['email'] ?? '')));
        if ($email === '' || empty($info['email_verified'])) {
            return ["Votre adresse Google n'est pas vérifiée."];
        }

        // 3) compte existant, sinon création d'un compte citoyen (le rôle ne vient JAMAIS de Google)
        $user = $this->findByEmail($email);
        if (!$user) {
            $user = $this->createGoogleUser($email);
            if (!$user) {
                return ["Impossible de créer votre compte. Réessayez."];
            }
        }
        if ($user->getStatus() === 'bloque') {
            return ["Votre compte est bloqué. Contactez l'administrateur."];
        }
        if ($user->getStatus() === 'en_attente') {
            return ["Votre compte n'a pas encore été approuvé par l'administrateur."];
        }

        $this->startSession($user);
        return [];
    }

    private function createGoogleUser(string $email): ?UserModel
    {
        $base = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string) strstr($email, '@', true));
        $base = substr($base !== '' ? $base : 'user', 0, 40);
        if (strlen($base) < 3) {
            $base .= '_usr';
        }

        $username = $base;
        $check = $this->db->prepare('SELECT 1 FROM users WHERE username = :u LIMIT 1');
        while (true) {
            $check->execute(['u' => $username]);
            if (!$check->fetch()) {
                break;
            }
            $username = $base . random_int(100, 9999);
        }

        // mot de passe aléatoire inutilisable : le compte Google se connecte via Google
        // (il pourra définir un mot de passe avec « Mot de passe oublié »)
        $stmt = $this->db->prepare(
            "INSERT INTO users (username, email, password, role, status)
             VALUES (:u, :e, :p, 'citoyen', 'actif')"
        );
        $stmt->execute([
            'u' => $username,
            'e' => $email,
            'p' => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT),
        ]);
        return $this->findByEmail($email);
    }

    /** Appel HTTPS simple (POST si $post, sinon GET) qui retourne le JSON décodé */
    private function httpRequest(string $url, ?array $post = null, ?string $bearer = null): array
    {
        $ch = curl_init($url);
        $opts = [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15];
        if (GOOGLE_CA_BUNDLE !== '') {
            $opts[CURLOPT_CAINFO] = GOOGLE_CA_BUNDLE;
        }
        if ($post !== null) {
            $opts[CURLOPT_POST] = true;
            $opts[CURLOPT_POSTFIELDS] = http_build_query($post);
        }
        if ($bearer !== null) {
            $opts[CURLOPT_HTTPHEADER] = ['Authorization: Bearer ' . $bearer];
        }
        curl_setopt_array($ch, $opts);
        $res = curl_exec($ch);
        if ($res === false) {
            error_log('Google HTTP error: ' . curl_error($ch));
        }
        curl_close($ch);
        $data = json_decode((string) $res, true);
        return is_array($data) ? $data : [];
    }
}
