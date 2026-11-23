<header class="topbar">
    <a class="brand-link" href="../" title="Retour au portail Aqua Flow">Aqua Flow</a>
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
        <a href="docs.php" class="<?= $currentPage == 'docs' ? 'active' : '' ?>">Documents</a>
        <a href="http://localhost/aquaflow-events/aquaflow-events/dev_stubs/login_stub.php">Événements</a>
        <a href="../">Portail</a>
        <a href="index.php?page=logout">Déconnexion</a>
    </nav>
</header>
