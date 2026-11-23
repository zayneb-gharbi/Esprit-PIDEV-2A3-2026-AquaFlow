<header class="topbar">
    <a class="brand-link" href="index.php" title="Accueil Aqua Flow">Aqua Flow</a>
    <nav>
        <a href="index.php?page=dashboard" class="<?= $currentPage == 'dashboard' ? 'active' : '' ?>">
            <?= ($_SESSION['role'] ?? '') == 'A' ? 'Tableau de bord' : 'Accueil' ?>
        </a>
        <?php if (($_SESSION['role'] ?? '') == 'A') { ?>
            <a href="index.php?page=stats" class="<?= $currentPage == 'stats' ? 'active' : '' ?>">Statistiques</a>
            <a href="index.php?page=citoyens" class="<?= $currentPage == 'citoyens' ? 'active' : '' ?>">Citoyens</a>
            <a href="index.php?page=plombiers" class="<?= $currentPage == 'plombiers' ? 'active' : '' ?>">Plombiers</a>
            <a href="index.php?page=admin" class="<?= $currentPage == 'admin' ? 'active' : '' ?>">Approbations</a>
        <?php } ?>
        <a href="index.php?page=personal-history" class="<?= $currentPage == 'personal-history' ? 'active' : '' ?>">Mon historique</a>
        <?php if (($_SESSION['role'] ?? '') == 'A') { ?>
            <a href="index.php?page=global-history" class="<?= $currentPage == 'global-history' ? 'active' : '' ?>">Historique global</a>
        <?php } ?>
        <a href="index.php?page=profile"  class="<?= $currentPage == 'profile' ? 'active' : '' ?>">Mon profil</a>
        <?php if (($_SESSION['role'] ?? '') == 'A') { ?>
            <a href="index.php?page=admin-events" class="<?= str_starts_with((string) $currentPage, 'admin-event') ? 'active' : '' ?>">Gérer les événements</a>
        <?php } else { ?>
            <a href="index.php?page=events" class="<?= str_starts_with((string) $currentPage, 'event') ? 'active' : '' ?>">Événements</a>
        <?php } ?>
        <a href="docs.php">Documents</a>
        <a href="index.php?page=logout">Déconnexion</a>
    </nav>
</header>
