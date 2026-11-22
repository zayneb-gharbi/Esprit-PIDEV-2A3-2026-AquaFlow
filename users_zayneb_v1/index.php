<?php
ob_start();
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Historique.php';
require_once __DIR__ . '/models/Utilisateur.php';
require_once __DIR__ . '/controllers/HistoriqueController.php';
require_once __DIR__ . '/controllers/ExportController.php';

function require_admin(): void {
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'admin') {
        http_response_code(403);
        exit('Accès interdit. Connectez-vous avec le compte administrateur de démonstration.');
    }
}

$historiqueModel = new Historique($pdo);
$userModel = new Utilisateur($pdo);
$historiqueController = new HistoriqueController($historiqueModel);
$exportController = new ExportController($userModel);
$page = $_GET['page'] ?? 'demo-login';

if ($page === 'demo-login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT u.id, u.nom, u.prenom, u.email, r.nom_role AS role FROM utilisateur u JOIN role r ON r.id_role=u.id_role WHERE u.id=:id AND u.statut='actif'");
    $stmt->execute([':id' => (int)($_POST['user_id'] ?? 0)]);
    $user = $stmt->fetch();
    if ($user) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>(int)$user['id'], 'nom'=>$user['nom'], 'prenom'=>$user['prenom'], 'email'=>$user['email'], 'role'=>$user['role']];
        $historiqueModel->ajouter((int)$user['id'], 'Connexion (démo)', 'Ouverture d’une session de démonstration');
        header('Location: index.php?page=' . ($user['role'] === 'admin' ? 'global-history' : 'personal-history'));
        exit;
    }
}

if ($page === 'logout') {
    if (!empty($_SESSION['user'])) {
        $historiqueModel->ajouter((int)$_SESSION['user']['id'], 'Déconnexion (démo)', 'Fermeture de la session de démonstration');
    }
    unset($_SESSION['user']);
    session_regenerate_id(true);
    header('Location: index.php?page=demo-login'); exit;
}

if ($page === 'export-plumbers') { $exportController->plombiers(); }

include __DIR__ . '/views/includes/header.php';
switch ($page) {
    case 'demo-login':
        $demoUsers = $pdo->query("SELECT u.id, u.nom, u.prenom, r.nom_role AS role FROM utilisateur u JOIN role r ON r.id_role=u.id_role ORDER BY u.id")->fetchAll();
        include __DIR__ . '/views/demo_login.php';
        break;
    case 'personal-history': $historiqueController->personnel(); break;
    case 'global-history': $historiqueController->global(); break;
    case 'plumbers':
        require_admin();
        $plombiers = $userModel->getPlombiers();
        include __DIR__ . '/views/admin/plumbers.php';
        break;
    default:
        header('Location: index.php?page=demo-login'); exit;
}
include __DIR__ . '/views/includes/footer.php';
