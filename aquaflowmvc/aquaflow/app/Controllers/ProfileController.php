<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// ProfileController.php : "Consulter info perso"
class ProfileController extends Controller
{
    protected string $page = 'profile';
    protected string $title = 'My profile | Aqua Flow';

    public function index(): void
    {
        requireLogin();

        // on relit l'utilisateur dans la base a chaque visite
        $uc = new UtilisateurC();
        $user = $uc->getUtilisateur($_SESSION['id_user']);

        // compte supprime, bloque ou plus actif entre-temps : deconnexion
        if (!$user || $user['statut'] != 'actif') {
            redirect('logout');
        }

        if ($user['role'] == 'A') {
            $roleLabel = 'Administrator';
        } elseif ($user['role'] == 'P') {
            $roleLabel = 'Plumber';
        } else {
            $roleLabel = 'Civilian';
        }

        $this->render('profile', [
            'user'      => $user,
            'roleLabel' => $roleLabel
        ]);
    }
}
