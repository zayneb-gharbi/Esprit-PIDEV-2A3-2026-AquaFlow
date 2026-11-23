<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

use App\Models\Utilisateur;

// SignupController.php : creation de compte (POST envoye par le formulaire "Create account")
class SignupController extends Controller
{
    protected string $page = 'signin';
    protected string $title = 'Aqua Flow | Gestion de l’eau';
    protected bool $withNav = false;

    public function index(): void
    {
        // on n'arrive ici normalement que par POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect('signin');
        }

        $error = $this->register();   // renvoie un message d'erreur (ou redirige)

        // erreur : on reaffiche la page avec le formulaire d'inscription ouvert
        $this->render('auth/signin', [
            'activeView'    => 'register',
            'message'       => $error,
            'messageType'   => 'error',
            'oldIdentifier' => ''
        ], 'auth/signin_scripts');
    }

    private function register(): string
    {
        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            return "Requête invalide. Veuillez réessayer.";
        }

        $prenom   = trim($_POST['first_name'] ?? '');
        $nom      = trim($_POST['last_name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $phone    = str_replace([' ', '-'], '', trim($_POST['phone'] ?? ''));
        $password = $_POST['password'] ?? '';
        $roleForm = $_POST['role'] ?? '';

        // ---------- validation cote serveur (meme regles que script.js) ----------
        if ($prenom === '' || $nom === '' || $email === '' || $phone === '' || $password === '') {
            return "Veuillez remplir tous les champs obligatoires.";
        }
        if (!preg_match('/^[a-zA-Z]+$/', $prenom) || !preg_match('/^[a-zA-Z]+$/', $nom)) {
            return "Le prénom et le nom doivent contenir uniquement des lettres anglaises (A-Z).";
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Veuillez saisir une adresse e-mail valide.";
        }
        if (!preg_match('/^(?:\+216|00216)?[2-9][0-9]{7}$/', $phone)) {
            return "Veuillez saisir un numéro de téléphone tunisien valide.";
        }
        if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[^a-zA-Z0-9]).{8,}$/', $password)) {
            return "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }
        if ($roleForm != 'civilian' && $roleForm != 'plumber') {
            return "Rôle invalide.";
        }

        if ($roleForm == 'plumber') {
            $role   = 'P';
            $statut = 'en_attente';          // le plombier attend l'approbation de l'admin
            $zone   = trim($_POST['service_area'] ?? '');
            $exp    = filter_var($_POST['experience'] ?? '', FILTER_VALIDATE_INT,
                                 ['options' => ['min_range' => 0, 'max_range' => 70]]);
            if ($zone === '') {
                return "Veuillez sélectionner votre zone de service.";
            }
            if ($exp === false) {
                return "L'expérience doit être un nombre entier entre 0 et 70.";
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
            return "Cet e-mail ou ce numéro de téléphone est déjà utilisé.";
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
            return "L'inscription a échoué. Veuillez réessayer.";
        }

        $createdUser = $uc->getUtilisateurByIdentifiant($email);
        if ($createdUser) {
            logActivity((int)$createdUser['id_user'], 'Création de compte', 'Création d’un compte ' . ($role === 'P' ? 'plombier' : 'citoyen'));
        }

        if ($role == 'P') {
            setFlash("Compte créé. Un administrateur doit l'approuver avant que vous puissiez vous connecter.");
        } else {
            setFlash("Compte créé. Vous pouvez maintenant vous connecter.");
        }
        redirect('signin');
        return '';
    }
}
