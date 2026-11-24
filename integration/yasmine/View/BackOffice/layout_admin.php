<?php
if (integrationMode()==='groupe') { integrationRender($title ?? 'AquaFlow',$content);return; }
/**
 * View/BackOffice/layout_admin.php  (Back Office, style adminHMD)
 * Attend : $title, $content, $activePage, $user
 * Pour intégrer le vrai template adminHMD : remplace le <head> et la <aside> par ceux de ton fichier
 * (assets/css/..., sidebar), et garde seulement <?= $content ?> au centre.
 */
$activePage = $activePage ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'Admin') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy:#073C72; --teal:#00BDB5; --bg:#F3F9FB; }
        body { background: var(--bg); }
        .sidebar { width:240px; min-height:100vh; background: var(--navy); }
        .sidebar a { color:#c9dbee; text-decoration:none; display:block; padding:10px 24px; }
        .sidebar a:hover, .sidebar a.active { background:#0a4d90; color:#fff; }
        .btn-aqua { background: var(--teal); color:#fff; border:0; }
        .btn-aqua:hover { background:#00a39c; color:#fff; }
    </style>
<link rel="stylesheet" href="security.css">
</head>
<body>
<div class="d-flex">
    <aside class="sidebar d-flex flex-column py-3">
        <div class="px-4 pb-3 text-white fw-bold">AquaFlow Admin</div>
        <a href="index.php?action=admin" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
        <a href="index.php?action=change_password" class="<?= $activePage === 'password' ? 'active' : '' ?>">Mot de passe</a>
        <a href="index.php?page=users">Utilisateurs</a><a href="index.php?page=stats">Statistiques</a><a href="index.php?page=security">Sécurité</a>
        <div class="mt-auto px-4 pt-3 border-top border-secondary">
            <div class="text-white small"><?= e($user->getUsername()) ?></div>
            <a class="px-0 small" href="index.php?action=logout">Déconnexion</a>
        </div>
    </aside>
    <div class="flex-fill">
        <div class="bg-white border-bottom px-4 py-3 d-flex justify-content-between">
            <h1 class="h5 m-0"><?= e($title ?? 'Admin') ?></h1>
            <span class="badge text-bg-danger align-self-center">BACKOFFICE</span>
        </div>
        <div class="p-4"><?= $content ?></div>
    </div>
</div>
</body>
</html>
