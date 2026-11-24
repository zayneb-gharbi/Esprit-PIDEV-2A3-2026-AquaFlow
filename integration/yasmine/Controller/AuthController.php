<?php

require_once __DIR__ . '/../../amir/models/UtilisateurPersistant.php';
require_once __DIR__ . '/../config/mail.php';

require_once __DIR__ . '/../Model/UserModel.php';

class AuthController
{
    private PDO $db;

    public function __construct()
    {
        $this->db = AmirDatabase::connexion();
    }
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
    private function convertir($row): ?UserModel
    {
        if (!$row) { return null; }
        return UserModel::fromArray(['id'=>$row['id'],'username'=>$row['nom'],'email'=>$row['email'],
            'password'=>$row['password'],'role'=>[1=>'citoyen',2=>'plombier',3=>'admin'][$row['id_role']],
            'status'=>$row['statut_code'],'created_at'=>$row['created_at']]);
    }
    private function findByEmail(string $email): ?UserModel
    {
        return $this->convertir((new UtilisateurPersistant($this->db))->getByIdentifier($email));
    }
    public function findById(int $id): ?UserModel
    {
        return $this->convertir((new UtilisateurPersistant($this->db))->getById($id));
    }
    private function updatePassword(int $userId, string $plainPassword): void
    {
        (new UtilisateurPersistant($this->db))->changerPassword($userId,$plainPassword);
    }
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
        return $errors;
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
    private function validateNewPassword(string $new, string $confirm): array
    {
        $errors = [];
        if (strlen($new)>72) { $errors[]='Le mot de passe ne doit pas dépasser 72 caractères.'; }
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
        $this->db->prepare('UPDATE password_resets SET used_at = CURRENT_TIMESTAMP WHERE user_id = :id AND used_at IS NULL')
                 ->execute(['id' => $userId]);

        $updated=(new UtilisateurPersistant($this->db))->getById($userId);
        (new AmirAuth(new UtilisateurPersistant($this->db)))->terminer($updated);
        Mailer::send(
            $user->getEmail(),
            'Votre mot de passe a été modifié',
            '<p>Bonjour ' . htmlspecialchars($user->getUsername(), ENT_QUOTES, 'UTF-8') . ',</p>'
            . '<p>Le mot de passe de votre compte AquaFlow vient d\'être modifié le ' . date('d/m/Y à H:i') . '.</p>'
            . '<p>Si ce n\'est pas vous, utilisez immédiatement « Mot de passe oublié » et contactez l\'administrateur.</p>'
        );
        return [];
    }
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
        if ($user && $user->getStatus() !== 'bloque') {
            $this->db->prepare('DELETE FROM password_resets WHERE user_id = :id')->execute(['id' => $user->getId()]);

            $token = bin2hex(random_bytes(32));
            $stmt = $this->db->prepare(
                'INSERT INTO password_resets (user_id, token_hash, expires_at)
                 VALUES (:uid, :h, :expires)'
            );
            $stmt->execute(['uid' => $user->getId(), 'h' => hash('sha256', $token), 'expires'=>gmdate('Y-m-d H:i:s',time()+3600)]);

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
    
    public function findValidReset(string $token): ?array
    {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        $stmt = $this->db->prepare(
            'SELECT * FROM password_resets
             WHERE token_hash = :h AND used_at IS NULL AND expires_at > CURRENT_TIMESTAMP LIMIT 1'
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
            $consume=$this->db->prepare('UPDATE password_resets SET used_at=CURRENT_TIMESTAMP WHERE id=:id AND used_at IS NULL AND expires_at>CURRENT_TIMESTAMP');
            $consume->execute(['id'=>$reset['id']]);
            if ($consume->rowCount()!==1) { throw new RuntimeException('Lien déjà utilisé.'); }
            $this->updatePassword((int) $reset['user_id'], $post['new_password']);
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
    private function startSession(UserModel $user): void
    {
        $model = new UtilisateurPersistant($this->db);
        (new AmirAuth($model))->premierFacteur($model->getById($user->getId()));
    }
}
