<?php
function integrationMode()
{
    return require __DIR__ . '/../../mode.php';
}
function amirConfig()
{
    $config = [
        'db_host' => '127.0.0.1', 'db_port' => 3306,
        'db_name' => 'aquaflow_integration_' . integrationMode(), 'db_user' => 'root', 'db_password' => '',
        'base_url' => 'http://127.0.0.1:8000/amir/public',
        'google_id' => '', 'google_secret' => '',
        'facebook_id' => '', 'facebook_secret' => '', 'facebook_version' => 'v26.0'
    ];
    if (is_file(__DIR__ . '/local.php')) {
        $config = array_replace($config, require __DIR__ . '/local.php');
    }
    return $config;
}
function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function champ($source, $name, $maximum = 200)
{
    $value = $source[$name] ?? '';
    if (!is_string($value) || strlen($value) > $maximum) {
        throw new DomainException('Une valeur du formulaire est invalide.');
    }
    return trim($value);
}
function rediriger($page)
{
    header('Location: index.php?page=' . $page, true, 303);
    exit;
}
