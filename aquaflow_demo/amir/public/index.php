<?php
require_once __DIR__ . '/../config/application.php';
require_once __DIR__ . '/../models/UtilisateurPersistant.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/TotpController.php';
require_once __DIR__ . '/../controllers/SocialController.php';
require_once __DIR__ . '/../controllers/DetailsController.php';
require_once __DIR__ . '/../../controllers/UserController.php';
require_once __DIR__ . '/../../controllers/AdminController.php';
chdir(dirname(__DIR__, 2));
AmirAuth::demarrer();
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-store');
header('Referrer-Policy: no-referrer');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header("Content-Security-Policy: default-src 'self'; base-uri 'none'; frame-ancestors 'none'; form-action 'self' https://accounts.google.com https://www.facebook.com");
$page = 'login';
$currentUser = null;
$error = '';
$content = '';
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
try {
    $page = champ($_GET, 'page', 40) ?: 'profile';
    $routes = ['login','logout','profile','edit','deactivate','deactivated','citizens','plumbers','stats','details','security','challenge','oauth_start','oauth_callback'];
    if (!in_array($page, $routes, true)) { http_response_code(404); throw new DomainException('Page introuvable.'); }
    if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','POST'], true)) { http_response_code(405); throw new DomainException('Méthode non autorisée.'); }
    $post = $_SERVER['REQUEST_METHOD'] === 'POST';
    if ($post) { AmirAuth::csrf(); }
    $model = new UtilisateurPersistant(AmirDatabase::connexion());
    $auth = new AmirAuth($model);
    $social = new AmirSocial($model, $auth);
    $currentUser = $auth->utilisateur();
    $public = ['login','oauth_start','oauth_callback','challenge','deactivated'];
    if (!$currentUser && !in_array($page, $public, true)) { rediriger(isset($_SESSION['pending']) ? 'challenge' : 'login'); }
    if (in_array($page, ['citizens','plumbers','stats','details'], true)) { AmirAuth::administrateur($currentUser); }
    if (in_array($page, ['logout','oauth_start'], true) && !$post) { http_response_code(405); throw new DomainException('Utilise le bouton du formulaire.'); }
    if ($post && !in_array($page, ['login','logout','oauth_start','challenge','security','edit','deactivate'], true)) { http_response_code(405); throw new DomainException('Méthode non autorisée.'); }
    if ($page === 'logout') { AmirAuth::effacer(); rediriger('login'); }
    if ($page === 'oauth_start') {
        $url = $social->commencer(champ($_POST, 'provider', 20), $currentUser);
        header('Location: ' . $url, true, 303);
        exit;
    }
    if ($page === 'oauth_callback') { rediriger($social->retour($_GET, $currentUser)); }
    if ($page === 'login' && $currentUser) { rediriger('profile'); }
    if ($page === 'login' && $post) { rediriger($auth->connecter($_POST)); }
    if ($page === 'challenge') {
        if ($currentUser) { rediriger('profile'); }
        if (!isset($_SESSION['pending'])) { rediriger('login'); }
        if ($post) { $auth->terminer2fa(champ($_POST, 'code', 16)); rediriger('profile'); }
    }
    if ($page === 'security' && $post) {
        $action = champ($_POST, 'action', 30);
        if ($action === 'prepare') { AmirTotp::preparer($currentUser); }
        elseif ($action === 'enable') { AmirTotp::activer($currentUser, champ($_POST, 'code', 6)); $_SESSION['message'] = 'Double authentification activée.'; }
        elseif ($action === 'disable') { AmirTotp::desactiver($currentUser, champ($_POST, 'code', 16)); $_SESSION['message'] = 'Double authentification désactivée.'; }
        else { throw new DomainException('Action inconnue.'); }
        rediriger('security');
    }
    if ($page === 'edit' && $post) {
        foreach (['nom','email','telephone','adresse'] as $field) { $_POST[$field] = champ($_POST, $field, 255); }
        if (strtolower($_POST['email']) !== $currentUser['email']) { AmirAuth::recente(); }
    }
    if ($page === 'deactivate' && $post) { AmirAuth::recente(); }
    $userController = new UserController($model, $currentUser['id'] ?? 0);
    $adminController = new AdminController($model);
    ob_start();
    if ($page === 'profile') { $userController->profile(); }
    elseif ($page === 'edit') { $userController->edit(); }
    elseif ($page === 'deactivate') { $userController->deactivate(); }
    elseif ($page === 'stats') { $adminController->stats(); }
    elseif ($page === 'citizens' || $page === 'plumbers') {
        $users = $page === 'citizens' ? $model->getCitizens() : $model->getPlumbers();
        include __DIR__ . '/../views/liste.php';
    } elseif ($page === 'details') {
        $details = (new AmirDetails($model))->consulter($currentUser, $_GET['id'] ?? '');
        include __DIR__ . '/../views/details.php';
    } elseif ($page === 'security') {
        $setup = $_SESSION['setup_totp'] ?? null;
        if ($setup && ($setup['expires'] < time() || $setup['id'] !== (int) $currentUser['id'] || $setup['version'] !== (int) $currentUser['version'])) { unset($_SESSION['setup_totp']); $setup = null; }
        $codes = $_SESSION['codes_secours'] ?? [];
        unset($_SESSION['codes_secours']);
        $services = $model->identites($currentUser['id']);
        include __DIR__ . '/../views/security.php';
    } elseif ($page === 'deactivated') {
        echo '<div class="notice">Compte désactivé. <a href="index.php?page=login">Retour à la connexion</a></div>';
    } else { include __DIR__ . '/../views/' . $page . '.php'; }
    $content = ob_get_clean();
    $content = preg_replace('/(<form\b[^>]*>)/i', '$1<input type="hidden" name="csrf" value="' . h($_SESSION['csrf']) . '">', $content);
    $content = str_replace(' onsubmit="return confirmDeactivate();"', '', $content);
} catch (DomainException $e) {
    if (ob_get_level()) { ob_end_clean(); }
    if (http_response_code() < 400) { http_response_code(422); }
    $error = $e->getMessage();
} catch (Throwable $e) {
    if (ob_get_level()) { ob_end_clean(); }
    http_response_code(503);
    $error = 'Le service est indisponible. Vérifie l’installation et la configuration avec le responsable du projet.';
    error_log('AquaFlow Amir: ' . get_class($e));
}
include __DIR__ . '/../views/layout.php';
