<?php
/**
 * AquaFlow — point d'entree unique (front controller).
 * Toutes les pages passent par index.php?page=... et par un controller MVC.
 */
$config = require __DIR__ . '/config/config.php';
date_default_timezone_set($config['app']['timezone']);

require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/routes.php';

// Accueil : dashboard si connecte, connexion sinon.
$default = !empty($_SESSION['id_user']) ? 'dashboard' : 'signin';
$page = (isset($_GET['page']) && is_string($_GET['page'])) ? $_GET['page'] : $default;

if (!isset($routes[$page])) {
    http_response_code(404);
    require_once __DIR__ . '/app/Views/pages/errors/404.php';
    exit;
}

$postOnly = [
    'event-register', 'event-unregister', 'admin-event-delete', 'admin-event-remove-participant',
];
if (in_array($page, $postOnly, true) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Méthode non autorisée.');
}

$route = $routes[$page];
$controllerFile = $route[0];
$action         = $route[1] ?? 'index';

require_once __DIR__ . '/app/Controllers/Controller.php';
require_once __DIR__ . '/app/Controllers/' . $controllerFile . '.php';

$class = 'App\\Controllers\\' . $controllerFile;
$controller = new $class();

if (!method_exists($controller, $action)) {
    http_response_code(404);
    require_once __DIR__ . '/app/Views/pages/errors/404.php';
    exit;
}

$controller->{$action}();
