<?php
declare(strict_types=1);

// 1. Session sécurisée
ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
// ini_set('session.cookie_secure', '1'); // à activer en HTTPS
session_start();

// 2. Expiration après 30 min d'inactivité
$sessionLifetime = 1800;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $sessionLifetime)) {
    $_SESSION = [];
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// 3. En-têtes de sécurité (jsdelivr autorisé pour Bootstrap ; retire-le si tu utilises les assets locaux du template)
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; script-src 'self' https://cdn.jsdelivr.net; font-src 'self' https://cdn.jsdelivr.net; img-src 'self' data:");

// 4. Contrôleur + helper d'échappement pour les vues
require_once __DIR__ . '/Controller/AuthController.php';
$authController = new AuthController();

function e(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

// 5. Routage
$action = $_GET['action'] ?? 'home';

switch ($action) {

    case 'home':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=' . (($_SESSION['role'] ?? '') === 'admin' ? 'admin' : 'dashboard'));
        } else {
            header('Location: index.php?action=login');
        }
        exit;

    // ---------------- LOGIN ----------------
    case 'login':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=home');
            exit;
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->login($_POST);
            if (empty($errors)) {
                header('Location: index.php?action=home');
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/View/FrontOffice/login.php';
        break;

    // ---------------- CONNEXION GOOGLE ----------------
    case 'google_login':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=home');
            exit;
        }
        if (!$authController->isGoogleConfigured()) {
            $errors = ["La connexion Google n'est pas configurée (config/google.local.php)."];
            $csrfToken = $authController->getCsrfToken();
            require __DIR__ . '/View/FrontOffice/login.php';
            break;
        }
        header('Location: ' . $authController->getGoogleAuthUrl());
        exit;

    case 'google_callback':
        if (AuthController::isAuthenticated()) {
            header('Location: index.php?action=home');
            exit;
        }
        $errors = $authController->loginWithGoogle($_GET);
        if (empty($errors)) {
            header('Location: index.php?action=home');
            exit;
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/View/FrontOffice/login.php';
        break;

    // ---------------- LOGOUT ----------------
    case 'logout':
        $authController->logout();
        header('Location: index.php?action=login');
        exit;

    // ---------------- MOT DE PASSE OUBLIÉ ----------------
    case 'forgot_password':
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->requestPasswordReset($_POST);
            if (empty($errors)) {
                header('Location: index.php?action=forgot_password&sent=1');
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/View/FrontOffice/forgot_password.php';
        break;

    // ---------------- RESET (lien reçu par email) ----------------
    case 'reset_password':
        $token = (string) ($_GET['token'] ?? $_POST['token'] ?? '');
        $tokenValid = $authController->findValidReset($token) !== null;
        $errors = [];
        if ($tokenValid && $_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->resetPassword($_POST);
            if (empty($errors)) {
                header('Location: index.php?action=login&reset=1');
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/View/FrontOffice/reset_password.php';
        break;

    // ---------------- CHANGER MOT DE PASSE (connecté) ----------------
    case 'change_password':
        AuthController::requireAuth();
        $user = $authController->findById((int) $_SESSION['user_id']);
        if (!$user) {
            $authController->logout();
            header('Location: index.php?action=login');
            exit;
        }
        $errors = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = $authController->changePassword($user->getId(), $_POST);
            if (empty($errors)) {
                header('Location: index.php?action=change_password&changed=1');
                exit;
            }
        }
        $csrfToken = $authController->getCsrfToken();
        require __DIR__ . '/View/FrontOffice/change_password.php';
        break;

    // ---------------- FRONT OFFICE ----------------
    case 'dashboard':
        AuthController::requireAuth();
        $user = $authController->findById((int) $_SESSION['user_id']);
        if (!$user) {
            $authController->logout();
            header('Location: index.php?action=login');
            exit;
        }
        require __DIR__ . '/View/FrontOffice/user_space.php';
        break;

    // ---------------- BACK OFFICE (admin) ----------------
    case 'admin':
        AuthController::requireRole('admin');
        $user = $authController->findById((int) $_SESSION['user_id']);
        require __DIR__ . '/View/BackOffice/admin_space.php';
        break;

    default:
        http_response_code(404);
        echo '404 - Page not found';
        break;
}
