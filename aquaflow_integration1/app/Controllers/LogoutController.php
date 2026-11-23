<?php
namespace App\Controllers;

// LogoutController.php : "Log out"
class LogoutController extends Controller
{
    public function index(): void
    {
        if (!empty($_SESSION['id_user'])) {
            logActivity((int)$_SESSION['id_user'], 'Déconnexion', 'Déconnexion du compte AquaFlow');
        }
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        redirect('signin');
    }
}
