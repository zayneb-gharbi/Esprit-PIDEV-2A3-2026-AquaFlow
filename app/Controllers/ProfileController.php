<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// ProfileController.php : "Consulter info perso"
class ProfileController extends Controller
{
    protected string $page = 'profile';
    protected string $title = 'Mon profil | Aqua Flow';

    public function index(): void
    {
        requireLogin();

        // on relit l'utilisateur dans la base a chaque visite
        $uc = new UtilisateurC();
        $user = $uc->getUtilisateur($_SESSION['id_user']);

        // Desactivation du compte citoyen/plombier depuis son profil
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['deactivate'])) {
            if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
                setFlash('Requete invalide.', 'error');
                redirect('profile');
            }
            if ($user && in_array($user['role'], ['C', 'P'], true) && $user['statut'] === 'actif') {
                // Desactivation volontaire : statut 'desactive', jamais 'bloque' (reserve a l'admin).
                if ($uc->desactiverMonCompte($_SESSION['id_user']) === 1) {
                    logActivity((int)$_SESSION['id_user'], 'Désactivation compte', 'Désactivation volontaire du compte personnel');
                    setFlash('Votre compte a été désactivé.');
                    redirect('logout');
                }
            }
            setFlash('La desactivation du compte n’a pas pu etre effectuee.', 'error');
            redirect('profile');
        }

        // compte supprime, bloque ou plus actif entre-temps : deconnexion
        if (!$user || $user['statut'] != 'actif') {
            redirect('logout');
        }

        if ($user['role'] == 'A') {
            $roleLabel = 'Administrateur';
        } elseif ($user['role'] == 'P') {
            $roleLabel = 'Plombier';
        } else {
            $roleLabel = 'Citoyen';
        }

        $this->render('profile/show', [
            'user'      => $user,
            'roleLabel' => $roleLabel,
            'flash'     => takeFlash()
        ]);
    }
}
