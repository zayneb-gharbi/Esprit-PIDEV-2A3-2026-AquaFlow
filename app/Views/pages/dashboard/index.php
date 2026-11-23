<?php
// Views/pages/dashboard.php : accueil apres connexion (admin ou utilisateur).
// variables : $user, $isAdmin, $roleLabel, $stats, $pendingCount, $flash
?>
<div class="card dashboard-card">

    <div class="dashboard-hero">
        <div>
            <div class="eyebrow">ESPACE <?= e(strtoupper($roleLabel)) ?></div>
            <h1>Bonjour, <?= e($user['prenom'] ?? '') ?> 👋</h1>
            <p class="sub">
                <?php if ($isAdmin) { ?>
                    Voici la vue d'ensemble de la gestion des utilisateurs Aqua Flow.
                <?php } else { ?>
                    Retrouvez vos informations, votre historique et les événements de votre ville.
                <?php } ?>
            </p>
        </div>
        <span class="status status-<?= e($user['statut'] ?? '') ?>">
            <?= e(['actif' => 'Actif', 'en_attente' => 'En attente', 'bloque' => 'Bloqué', 'desactive' => 'Désactivé'][$user['statut'] ?? ''] ?? ($user['statut'] ?? '')) ?>
        </span>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <?php if ($isAdmin) { ?>
        <h2 class="dashboard-section-title">Vue d'ensemble</h2>
        <div class="stats-grid">
            <div class="stat-card"><p>Total utilisateurs</p><h2><?= (int) ($stats['total'] ?? 0) ?></h2></div>
            <div class="stat-card"><p>Citoyens</p><h2><?= (int) ($stats['citoyens'] ?? 0) ?></h2></div>
            <div class="stat-card"><p>Plombiers</p><h2><?= (int) ($stats['plombiers'] ?? 0) ?></h2></div>
            <div class="stat-card"><p>Comptes actifs</p><h2><?= (int) ($stats['actifs'] ?? 0) ?></h2></div>
            <div class="stat-card"><p>Bloqués</p><h2><?= (int) ($stats['bloques'] ?? 0) ?></h2></div>
            <div class="stat-card"><p>Désactivés</p><h2><?= (int) ($stats['desactives'] ?? 0) ?></h2></div>
        </div>

        <h2 class="dashboard-section-title">Gestion des utilisateurs</h2>
        <div class="quick-grid">
            <a class="quick-card" href="index.php?page=citoyens">
                <span class="quick-num">C</span>
                <span><strong>Citoyens</strong><small>Bloquer, débloquer, exporter</small></span>
            </a>
            <a class="quick-card" href="index.php?page=plombiers">
                <span class="quick-num">P</span>
                <span><strong>Plombiers</strong><small>Bloquer, débloquer, exporter</small></span>
            </a>
            <a class="quick-card" href="index.php?page=admin">
                <span class="quick-num">A</span>
                <span><strong>Approbations</strong><small><?= (int) $pendingCount ?> compte(s) en attente</small></span>
            </a>
            <a class="quick-card" href="index.php?page=stats">
                <span class="quick-num">S</span>
                <span><strong>Statistiques</strong><small>Chiffres détaillés</small></span>
            </a>
            <a class="quick-card" href="index.php?page=global-history">
                <span class="quick-num">H</span>
                <span><strong>Historique global</strong><small>Toutes les actions</small></span>
            </a>
            <a class="quick-card" href="index.php?page=profile">
                <span class="quick-num">M</span>
                <span><strong>Mon profil</strong><small>Mes informations</small></span>
            </a>
        </div>
    <?php } else { ?>
        <h2 class="dashboard-section-title">Mes accès</h2>
        <div class="quick-grid">
            <a class="quick-card" href="index.php?page=profile">
                <span class="quick-num">M</span>
                <span><strong>Mon profil</strong><small>Voir mes informations</small></span>
            </a>
            <a class="quick-card" href="index.php?page=profile-edit">
                <span class="quick-num">E</span>
                <span><strong>Modifier mes informations</strong><small>Coordonnées et zone de service</small></span>
            </a>
            <a class="quick-card" href="index.php?page=personal-history">
                <span class="quick-num">H</span>
                <span><strong>Mon historique</strong><small>Mes actions récentes</small></span>
            </a>
        </div>
    <?php } ?>

    <h2 class="dashboard-section-title">Module Événements & documentation</h2>
    <div class="quick-grid">
        <?php if ($isAdmin) { ?>
            <a class="quick-card" href="index.php?page=admin-events">
                <span class="quick-num">G</span>
                <span><strong>Gérer les événements</strong><small>Créer, publier, annuler</small></span>
            </a>
        <?php } else { ?>
            <a class="quick-card" href="index.php?page=events">
                <span class="quick-num">É</span>
                <span><strong>Événements</strong><small>Consulter et s'inscrire</small></span>
            </a>
            <a class="quick-card" href="index.php?page=my-events">
                <span class="quick-num">I</span>
                <span><strong>Mes participations</strong><small>Suivre mes inscriptions</small></span>
            </a>
        <?php } ?>
        <a class="quick-card" href="docs.php">
            <span class="quick-num">D</span>
            <span><strong>Documents Isra</strong><small>Audit, brief, design system, to-do</small></span>
        </a>
    </div>

</div>
