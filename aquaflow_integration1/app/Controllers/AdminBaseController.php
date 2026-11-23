<?php
namespace App\Controllers;

require_once __DIR__ . '/UtilisateurC.php';

// AdminBaseController.php : verification "administrateur" partagee par les pages admin
abstract class AdminBaseController extends Controller
{
    // page reservee aux administrateurs : on relit le role dans la base
    protected function requireAdmin(UtilisateurC $uc): array
    {
        requireLogin();

        $admin = $uc->getUtilisateur($_SESSION['id_user']);

        if (!$admin || $admin['statut'] != 'actif') {
            redirect('logout');
        }
        if ($admin['role'] != 'A') {
            redirect('profile');
        }
        return $admin;
    }

    // bouton "Desactiver" (liste des citoyens / plombiers)
    protected function handleDeactivation(UtilisateurC $uc, string $page): void
    {
        $id = (int) ($_POST['id_user'] ?? 0);

        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            setFlash("Requête invalide.", 'error');
        } elseif ($id <= 0) {
            setFlash("Compte invalide.", 'error');
        } elseif ($uc->desactiverUtilisateur($id) == 1) {
            logActivity((int)$_SESSION['id_user'], 'Désactivation utilisateur', 'Désactivation du compte utilisateur #' . $id);
            setFlash("Compte désactivé.");
        } else {
            setFlash("Aucun changement (le compte n'est plus actif).", 'error');
        }

        redirect($page);
    }
}
