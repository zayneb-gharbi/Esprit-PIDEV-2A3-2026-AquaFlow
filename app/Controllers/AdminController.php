<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// AdminController.php : approuver ou refuser les comptes plombiers en attente
class AdminController extends Controller
{
    protected string $page = 'admin';
    protected string $title = 'Approbations | Aqua Flow';

    public function index(): void
    {
        requireLogin();

        // page reservee aux administrateurs : on relit le role dans la base
        $uc = new UtilisateurC();
        $admin = $uc->getUtilisateur($_SESSION['id_user']);

        if (!$admin || $admin['statut'] != 'actif') {
            redirect('logout');
        }
        if ($admin['role'] != 'A') {
            redirect('profile');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleAction($uc);       // met un message flash puis redirige
        }

        $this->render('admin/approvals', [
            'admin'     => $admin,
            'enAttente' => $uc->getUtilisateursEnAttente(),
            'flash'     => takeFlash()
        ]);
    }

    // bouton Approve / Reject
    private function handleAction(UtilisateurC $uc): void
    {
        $action = $_POST['action'] ?? '';
        $id     = (int) ($_POST['id_user'] ?? 0);

        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            setFlash("Requête invalide.", 'error');
        } elseif ($id <= 0) {
            setFlash("Compte invalide.", 'error');
        } elseif ($action == 'approve') {
            if ($uc->approuverUtilisateur($id) == 1) {
                logActivity((int)$_SESSION['id_user'], 'Approbation compte', 'Approbation du compte utilisateur #' . $id);
                setFlash("Compte approuvé.");
            } else {
                setFlash("Aucun changement (le compte n'est plus en attente).", 'error');
            }
        } elseif ($action == 'reject') {
            if ($uc->refuserUtilisateur($id) == 1) {
                logActivity((int)$_SESSION['id_user'], 'Refus compte', 'Refus et suppression du compte utilisateur #' . $id);
                setFlash("Compte refusé et supprimé.");
            } else {
                setFlash("Aucun changement (le compte n'est plus en attente).", 'error');
            }
        } else {
            setFlash("Action inconnue.", 'error');
        }

        // on recharge la page : evite de renvoyer le formulaire avec F5
        redirect('admin');
    }
}
