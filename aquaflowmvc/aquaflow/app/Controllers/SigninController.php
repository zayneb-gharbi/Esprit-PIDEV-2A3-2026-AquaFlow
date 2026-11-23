<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// SigninController.php : page de connexion (GET = formulaire, POST = connexion)
class SigninController extends Controller
{
    protected string $page = 'signin';
    protected string $title = 'Aqua Flow | Water Management';
    protected bool $withNav = false;

    public function index(): void
    {
        // deja connecte : on va directement a la bonne page
        if (!empty($_SESSION['id_user'])) {
            redirect(($_SESSION['role'] ?? '') == 'A' ? 'admin' : 'profile');
        }

        $message = '';
        $messageType = 'error';
        $oldIdentifier = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $oldIdentifier = trim($_POST['identifiant'] ?? '');
            $message = $this->login();       // renvoie un message d'erreur (ou redirige)
        } else {
            $flash = takeFlash();
            if ($flash) {
                $message = $flash['text'];
                $messageType = $flash['type'];
            }
        }

        $this->render('signin', [
            'activeView'    => 'login',
            'message'       => $message,
            'messageType'   => $messageType,
            'oldIdentifier' => $oldIdentifier
        ], 'signin_scripts');
    }

    // verifie les identifiants ; en cas de succes : session + redirection
    private function login(): string
    {
        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            return "Invalid request. Please try again.";
        }

        $identifiant = trim($_POST['identifiant'] ?? '');
        $password    = $_POST['password'] ?? '';

        if ($identifiant === '' || $password === '') {
            return "Please enter your email or phone and your password.";
        }

        $uc = new UtilisateurC();
        $user = $uc->getUtilisateurByIdentifiant($identifiant);

        // message generique : on ne revele jamais si c'est l'identifiant ou le mot de passe
        if (!$user || !password_verify($password, $user['mot_de_passe'])) {
            return "Incorrect email/phone or password.";
        }
        if ($user['statut'] == 'en_attente') {
            return "Your account is waiting for administrator approval.";
        }
        if ($user['statut'] == 'bloque') {
            return "Your account is blocked.";
        }

        // tout est bon : on ouvre la session
        session_regenerate_id(true);
        $_SESSION['id_user'] = $user['id_user'];
        $_SESSION['nom']     = $user['nom'];
        $_SESSION['prenom']  = $user['prenom'];
        $_SESSION['role']    = $user['role'];

        redirect($user['role'] == 'A' ? 'admin' : 'profile');
        return '';
    }
}
