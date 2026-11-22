<?php
ob_start();
session_start();
require 'models/Utilisateur.php';
require 'controllers/UserController.php';
require 'controllers/AdminController.php';
$model = new Utilisateur(null); // Mode demo: aucune base de donnees requise
$idUser = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$userController = new UserController($model, $idUser);
$adminController = new AdminController($model);
$page = isset($_GET['page']) ? $_GET['page'] : 'profile';
if ($page == 'profile' || $page == 'edit' || $page == 'deactivate') {
    include 'views/includes/header.php';
    if ($page == 'profile') $userController->profile();
    if ($page == 'edit') $userController->edit();
    if ($page == 'deactivate') $userController->deactivate();
    include 'views/includes/footer.php';
} elseif ($page == 'citizens' || $page == 'plumbers' || $page == 'stats') {
    include 'views/includes/header.php';
    if ($page == 'citizens') $adminController->citizens();
    if ($page == 'plumbers') $adminController->plumbers();
    if ($page == 'stats') $adminController->stats();
    include 'views/includes/footer.php';
} elseif ($page == 'deactivated') {
    include 'views/includes/header.php';
    echo '<div class="notice">Compte désactivé (simulation). <a href="index.php">Retour</a></div>';
    include 'views/includes/footer.php';
} else { header('Location: index.php?page=profile'); exit; }
?>
