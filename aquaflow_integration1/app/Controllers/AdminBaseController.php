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

    // actions "Bloquer" / "Debloquer" (listes des citoyens / plombiers)
    protected function handleBlockToggle(UtilisateurC $uc, string $page, ?string $forcedAction = null): void
    {
        $action = $forcedAction ?? ($_POST['action'] ?? '');
        $id     = (int) ($_POST['id_user'] ?? 0);
        $reason = trim((string) ($_POST['reason'] ?? ''));

        if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
            setFlash("Requête invalide.", 'error');
        } elseif ($id <= 0) {
            setFlash("Compte invalide.", 'error');
        } elseif ($id === (int) ($_SESSION['id_user'] ?? 0)) {
            setFlash("Vous ne pouvez pas bloquer votre propre compte.", 'error');
        } elseif ($action !== 'block' && $action !== 'unblock') {
            setFlash("Action inconnue.", 'error');
        } elseif ($action === 'block' && $reason !== ''
                  && (mb_strlen($reason) < 5 || mb_strlen($reason) > 255)) {
            setFlash("Le motif doit contenir entre 5 et 255 caractères.", 'error');
        } elseif ($action === 'block' && $uc->bloquerUtilisateur($id) === 1) {
            $description = 'Compte #' . $id;
            if ($reason !== '') {
                $description .= ' — Motif : ' . $reason;
            }
            logActivity((int) $_SESSION['id_user'], 'Blocage utilisateur', $description);
            setFlash("Compte bloqué.");
        } elseif ($action === 'unblock' && $uc->debloquerUtilisateur($id) === 1) {
            logActivity((int) $_SESSION['id_user'], 'Déblocage utilisateur', 'Compte #' . $id);
            setFlash("Compte débloqué.");
        } else {
            setFlash("Aucun changement (statut ou rôle non éligible).", 'error');
        }

        redirect($page);
    }

    // Ancien nom conserve pendant la transition (PR 3 mettra a jour les vues).
    protected function handleDeactivation(UtilisateurC $uc, string $page): void
    {
        $this->handleBlockToggle($uc, $page, 'block');
    }
}
