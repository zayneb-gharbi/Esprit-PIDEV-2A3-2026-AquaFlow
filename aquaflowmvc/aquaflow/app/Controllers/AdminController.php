<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// AdminController.php : approuver ou refuser les comptes plombiers en attente
class AdminController extends Controller
{
    protected string $page = 'admin';
    protected string $title = 'Admin | Aqua Flow';

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

        $this->render('admin', [
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
            setFlash("Invalid request.", 'error');
        } elseif ($id <= 0) {
            setFlash("Invalid account.", 'error');
        } elseif ($action == 'approve') {
            if ($uc->approuverUtilisateur($id) == 1) {
                setFlash("Account approved.");
            } else {
                setFlash("Nothing changed (the account is no longer pending).", 'error');
            }
        } elseif ($action == 'reject') {
            if ($uc->refuserUtilisateur($id) == 1) {
                setFlash("Account rejected and deleted.");
            } else {
                setFlash("Nothing changed (the account is no longer pending).", 'error');
            }
        } else {
            setFlash("Unknown action.", 'error');
        }

        // on recharge la page : evite de renvoyer le formulaire avec F5
        redirect('admin');
    }
}
