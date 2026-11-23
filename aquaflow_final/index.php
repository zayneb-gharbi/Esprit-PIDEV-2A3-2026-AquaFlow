<?php
session_start();
require_once 'models/UtilisateurModel.php';
require_once 'controllers/UserController.php';
require_once 'controllers/AdminController.php';

$model = new UtilisateurModel();
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

if ($page == 'home') {
    include 'views/home.php';
} elseif ($page == 'user') {
    $controller = new UserController($model);
    $controller->index();
} elseif ($page == 'deactivate') {
    $controller = new UserController($model);
    $controller->deactivate();
} elseif ($page == 'citizens' || $page == 'plumbers' || $page == 'stats') {
    $controller = new AdminController($model);
    $controller->index($page);
} else {
    header('Location: index.php');
    exit;
}
?>
