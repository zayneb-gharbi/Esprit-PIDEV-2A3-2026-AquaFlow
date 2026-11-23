<?php
namespace App\Controllers;

require_once __DIR__ . '/AdminBaseController.php';

// DashboardController.php : accueil apres connexion (admin OU utilisateur).
// L'admin voit un tableau de bord avec les chiffres et les raccourcis de gestion.
// Le citoyen / plombier voit un accueil simple avec ses actions et les liens utiles.
class DashboardController extends Controller
{
    protected string $page = 'dashboard';
    protected string $title = 'Tableau de bord | Aqua Flow';

    public function index(): void
    {
        requireLogin();

        $uc   = new UtilisateurC();
        $user = $uc->getUtilisateur($_SESSION['id_user']);

        if (!$user || $user['statut'] != 'actif') {
            redirect('logout');
        }

        $isAdmin = ($user['role'] == 'A');

        $this->render('dashboard/index', [
            'user'         => $user,
            'isAdmin'      => $isAdmin,
            'roleLabel'    => $isAdmin ? 'Administrateur' : ($user['role'] == 'P' ? 'Plombier' : 'Citoyen'),
            'stats'        => $isAdmin ? $uc->getStatistiques() : [],
            'pendingCount' => $isAdmin ? count($uc->getUtilisateursEnAttente()) : 0,
            'flash'        => takeFlash(),
        ]);
    }
}
