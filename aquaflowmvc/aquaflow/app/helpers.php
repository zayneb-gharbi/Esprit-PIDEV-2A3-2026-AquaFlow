<?php
// helpers.php : session securisee (workshop) + petites fonctions utilitaires

ini_set('session.use_strict_mode', '1');
ini_set('session.cookie_httponly', '1');
ini_set('session.cookie_samesite', 'Lax');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// echappement XSS
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

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
