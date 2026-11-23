<?php
// index.php : point d'entree unique  ->  index.php?page=signin
require_once __DIR__ . '/app/helpers.php';
require_once __DIR__ . '/app/routes.php';

$page = (isset($_GET['page']) && is_string($_GET['page'])) ? $_GET['page'] : 'signin';

if (!isset($routes[$page])) {
    http_response_code(404);
    echo 'Page not found. <a href="index.php?page=signin">Back</a>';
    exit;
}

require_once __DIR__ . '/app/Controllers/Controller.php';
require_once __DIR__ . '/app/Controllers/' . $routes[$page] . '.php';

$class      = 'App\\Controllers\\' . $routes[$page];
$controller = new $class();
$controller->index();
