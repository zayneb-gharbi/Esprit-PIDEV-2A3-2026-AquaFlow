<?php
// helpers.php : session securisee (workshop) + petites fonctions utilitaires




if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// echappement XSS


// redirection vers une page du routeur
function redirect($page)
{
    header('Location: index.php?page=' . $page);
    exit;
}

// ---------- CSRF (workshop) ----------
function csrfToken()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf($token)
{
    return hash_equals(csrfToken(), (string) $token);
}

// ---------- Messages flash (affiches une seule fois) ----------
function setFlash($text, $type = 'success')
{
    $_SESSION['flash'] = ['text' => $text, 'type' => $type];
}

function takeFlash()
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

// ---------- Protection des pages ----------
function requireLogin()
{
    if (empty($_SESSION['id_user'])) {
        redirect('signin');
    }
}

// Journalisation centralisée des actions utilisateur.
function logActivity($idUser, $action, $description = null)
{
    if (!$idUser) return false;
    require_once __DIR__ . '/Models/Model.php';
    require_once __DIR__ . '/Models/Historique.php';
    try {
        $history = new \App\Models\Historique();
        return $history->ajouter((int)$idUser, (string)$action, $description);
    } catch (\Throwable $e) {
        error_log('Journalisation impossible: ' . $e->getMessage());
        return false;
    }
}
