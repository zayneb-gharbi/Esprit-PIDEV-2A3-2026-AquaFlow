<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

use App\Models\Utilisateur;

// SignupController.php : creation de compte (POST envoye par le formulaire "Create account")
class SignupController extends Controller
{
    protected string $page = 'signin';
    protected string $title = 'Aqua Flow | Water Management';
    protected bool $withNav = false;

    public function index(): void
    {
        // on n'arrive ici normalement que par POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('signin');
        }

        $error = $this->register();   // renvoie un message d'erreur (ou redirige)

        // erreur : on reaffiche la page avec le formulaire d'inscription ouvert
        $this->render('signin', [
            'activeView'    => 'register',
            'message'       => $error,
            'messageType'   => 'error',
            'oldIdentifier' => ''
        ], 'signin_scripts');
    }

    private function register(): string
    {
        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            return "Invalid request. Please try again.";
        }

        $prenom   = trim($_POST['first_name'] ?? '');
        $nom      = trim($_POST['last_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = str_replace([' ', '-'], '', trim($_POST['phone'] ?? ''));
        $password = $_POST['password'] ?? '';
        $roleForm = $_POST['role'] ?? '';

        // ---------- validation cote serveur (meme regles que script.js) ----------
        if ($prenom === '' || $nom === '' || $email === '' || $phone === '' || $password === '') {
            return "Please fill in all required fields.";
        }
        if (!preg_match('/^[a-zA-Z]+$/', $prenom) || !preg_match('/^[a-zA-Z]+$/', $nom)) {
            return "First and last names must contain English letters only (A-Z).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Please enter a valid email address.";
        }
        if (!preg_match('/^(?:\+216|00216)?[2-9][0-9]{7}$/', $phone)) {
            return "Please enter a valid Tunisian phone number.";
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
            return "Password must contain at least 8 characters, one uppercase letter, one lowercase letter, one number and one special character.";
        }
        if ($roleForm != 'civilian' && $roleForm != 'plumber') {
            return "Invalid role.";
        }

        if ($roleForm == 'plumber') {
            $role   = 'P';
            $statut = 'en_attente';          // le plombier attend l'approbation de l'admin
            $zone   = trim($_POST['service_area'] ?? '');
            $exp    = filter_var($_POST['experience'] ?? '', FILTER_VALIDATE_INT,
                                 ['options' => ['min_range' => 0, 'max_range' => 70]]);
            if ($zone === '') {
                return "Please select your service area.";
            }
            if ($exp === false) {
                return "Experience must be a whole number between 0 and 70.";
            }
        } else {
            $role   = 'C';
            $statut = 'actif';
            $zone   = null;
            $exp    = null;
        }

        // ---------- email / telephone deja utilises ? ----------
        $uc = new UtilisateurC();
        if ($uc->getUtilisateurByIdentifiant($email) || $uc->getUtilisateurByIdentifiant($phone)) {
            return "This email or phone number is already used.";
        }

        // ---------- insertion (mot de passe hache) ----------
        $u = new Utilisateur(
            null,
            $nom,
            $prenom,
            $email,
            $phone,
            password_hash($password, PASSWORD_DEFAULT),
            $role,
            $zone,
            $exp,
            $statut
        );

        if (!$uc->addUtilisateur($u)) {
            return "Registration failed. Please try again.";
        }

        if ($role == 'P') {
            setFlash("Account created. An administrator must approve it before you can sign in.");
        } else {
            setFlash("Account created. You can now sign in.");
        }
        redirect('signin');
        return '';
    }
}
