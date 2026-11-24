<?php
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if (in_array($path,['/','/amir/public/','/amir/public'],true)) { header('Location: /amir/public/index.php');return true; }
if ($path==='/amir/public/index.php') { require __DIR__.'/amir/public/index.php';return true; }
if (preg_match('~^/(assets/(style\.css|script\.js)|amir/public/security\.css|amir/public/app/Views/assets/[a-zA-Z0-9_./-]+\.(css|js|svg|png|jpg|woff2?))$~',$path) && !str_contains($path,'..') && is_file(__DIR__.$path)) { return false; }
http_response_code(404);echo 'Page introuvable.';return true;
