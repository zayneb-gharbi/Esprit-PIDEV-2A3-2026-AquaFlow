<header class="topbar">
    <a class="brand-link" href="index.php?page=profile">Aqua Flow</a>
    <nav>
        <a href="index.php?page=profile" class="<?= $currentPage == 'profile' ? 'active' : '' ?>">My profile</a>
        <?php if (($_SESSION['role'] ?? '') == 'A') { ?>
            <a href="index.php?page=admin" class="<?= $currentPage == 'admin' ? 'active' : '' ?>">Admin</a>
        <?php } ?>
        <a href="index.php?page=logout">Log out</a>
    </nav>
</header>
