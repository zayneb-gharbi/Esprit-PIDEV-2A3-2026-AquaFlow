<?php
/**
 * View/FrontOffice/layout.php  (AquaFlow - Front Office)
 * Attend : $title (string), $content (string HTML déjà préparé)
 * Pour utiliser les assets du template : remplace le <link> Bootstrap et le <style> ci-dessous.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'AquaFlow') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root { --navy:#073C72; --teal:#00BDB5; --bg:#F3F9FB; --muted:#74869A; }
        body { background: var(--bg); color: var(--navy); }
        .navbar { background: var(--navy); }
        .navbar .navbar-brand { color:#fff; font-weight:700; }
        .navbar .nav-link { color:#d7e6f5; }
        .btn-aqua { background: var(--teal); color:#fff; border:0; }
        .btn-aqua:hover { background:#00a39c; color:#fff; }
        .card-auth { max-width: 460px; margin: 60px auto; border:0; box-shadow:0 2px 12px rgba(7,60,114,.08); }
        .text-muted-aqua { color: var(--muted); }
        a { color: var(--teal); }
    </style>
</head>
<body>
<nav class="navbar navbar-expand">
    <div class="container">
        <a class="navbar-brand" href="index.php">AquaFlow</a>
        <div class="navbar-nav ms-auto">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a class="nav-link" href="index.php?action=dashboard">Mon espace</a>
                <a class="nav-link" href="index.php?action=change_password">Mot de passe</a>
                <a class="nav-link" href="index.php?action=logout">Déconnexion</a>
            <?php else: ?>
                <a class="nav-link" href="index.php?action=login">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container">
    <?= $content ?>
</main>
</body>
</html>
