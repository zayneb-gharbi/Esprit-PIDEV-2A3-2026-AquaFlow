<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaFlow — Mon compte</title>
    <link rel="stylesheet" href="../../assets/style.css">
    <link rel="stylesheet" href="security.css">
</head>
<body>
<header class="topbar">
    <div class="brand"><span class="drop">◆</span> AquaFlow</div>
    <nav aria-label="Navigation principale">
        <?php if ($currentUser) { ?>
        <a href="index.php?page=profile">Mon profil</a>
        <a href="index.php?page=security">Sécurité</a>
        <?php if ((int) $currentUser['id_role'] === 3) { ?>
        <a href="index.php?page=citizens">Citoyens</a>
        <a href="index.php?page=plumbers">Plombiers</a>
        <a href="index.php?page=stats">Statistiques</a>
        <?php } ?>
        <form method="post" action="index.php?page=logout"><input type="hidden" name="csrf" value="<?= h($_SESSION['csrf']) ?>"><button class="nav-button" type="submit">Se déconnecter</button></form>
        <?php } else { ?><a href="index.php?page=login">Se connecter</a><?php } ?>
    </nav>
</header>
<main class="container">
    <?php if ($message !== '') { ?><div class="notice" role="status"><?= h($message) ?></div><?php } ?>
    <?php if ($error !== '') { ?><div class="notice error" role="alert"><?= h($error) ?></div><a class="btn-light" href="index.php?page=<?= h(in_array($page, ['login','challenge','security','edit','details'], true) ? ($page === 'details' ? 'citizens' : $page) : 'profile') ?>">Réessayer</a><?php } ?>
    <?= $content ?>
</main>
<footer>© 2026 AquaFlow — Gestion intelligente de l'eau</footer>
<script src="../../assets/script.js"></script>
</body>
</html>
