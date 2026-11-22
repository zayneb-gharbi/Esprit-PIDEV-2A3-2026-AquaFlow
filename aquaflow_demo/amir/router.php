<?php
if (PHP_SAPI !== 'cli-server') { http_response_code(404); exit; }
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($path === '/') { header('Location: /amir/public/index.php', true, 302); exit; }
if ($path === '/amir/public/index.php') { require __DIR__ . '/public/index.php'; return true; }
if (in_array($path, ['/assets/style.css','/assets/script.js','/amir/public/security.css'], true)) {
    header('Content-Type: ' . (str_ends_with($path, '.css') ? 'text/css' : 'application/javascript'));
    header('X-Content-Type-Options: nosniff');
    readfile(dirname(__DIR__) . $path);
    return true;
}
http_response_code(404);
echo 'Page introuvable.';
