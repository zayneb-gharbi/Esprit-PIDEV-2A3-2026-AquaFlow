<?php
ob_start();
session_start();
require 'models/Utilisateur.php';
require 'controllers/UserController.php';
require 'controllers/AdminController.php';
$model = new Utilisateur(null); // Demo sans base de donnees
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Connexion de demonstration: aucune authentification reelle
if ($page == 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = isset($_POST['type']) ? $_POST['type'] : 'user';
    if ($type == 'admin') {
        $_SESSION['user_id'] = 6;
        $_SESSION['demo_role'] = 'admin';
        header('Location: index.php?page=stats'); exit;
    }
    $role = isset($_POST['role']) ? $_POST['role'] : 'citoyen';
    $_SESSION['demo_role'] = $role;
    $_SESSION['user_id'] = ($role == 'plombier') ? 4 : 1;
    header('Location: index.php?page=profile'); exit;
}
if ($page == 'logout') { session_destroy(); header('Location: index.php'); exit; }

if ($page == 'home') {
    include 'views/auth/home.php'; exit;
}
$roleSession = isset($_SESSION['demo_role']) ? $_SESSION['demo_role'] : '';
$userPages = ['profile','edit','deactivate','deactivated'];
$adminPages = ['citizens','plumbers','stats'];
if (in_array($page, $userPages) && !in_array($roleSession, ['citoyen','plombier'])) { header('Location: index.php'); exit; }
if (in_array($page, $adminPages) && $roleSession != 'admin') { header('Location: index.php'); exit; }
$idUser = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$userController = new UserController($model, $idUser);
$adminController = new AdminController($model);
include 'views/includes/header.php';
if ($page == 'profile') $userController->profile();
elseif ($page == 'edit') $userController->edit();
elseif ($page == 'deactivate') $userController->deactivate();
elseif ($page == 'citizens') $adminController->citizens();
elseif ($page == 'plumbers') $adminController->plumbers();
elseif ($page == 'stats') $adminController->stats();
elseif ($page == 'deactivated') echo '<div class="notice">Compte désactivé (simulation). <a href="index.php">Retour à l’accueil</a></div>';
else { header('Location: index.php'); exit; }
include 'views/includes/footer.php';
?>
