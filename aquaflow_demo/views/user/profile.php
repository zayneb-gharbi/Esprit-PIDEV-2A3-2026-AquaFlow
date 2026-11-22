<section class="page-title">
    <p class="eyebrow">ESPACE CITOYEN</p>
    <h1>Mes informations personnelles</h1>
    <p>Consultez les informations liées à votre compte AquaFlow.</p>
</section>
<?php if (isset($_GET['success'])) { ?><div class="notice">Informations modifiées avec succès !</div><?php } ?>
<div class="card profile-card">
    <div class="avatar">👤</div>
    <h2><?php echo htmlspecialchars($user['nom']); ?></h2>
    <span class="status"><?php echo htmlspecialchars($user['statut']); ?></span>
    <div class="info-grid">
        <div><small>Adresse email</small><strong><?php echo htmlspecialchars($user['email']); ?></strong></div>
        <div><small>Téléphone</small><strong><?php echo htmlspecialchars($user['telephone'] ?? 'Non renseigné'); ?></strong></div>
        <div><small>Adresse</small><strong><?php echo htmlspecialchars($user['adresse'] ?? 'Non renseignée'); ?></strong></div>
        <div><small>Rôle</small><strong><?php echo htmlspecialchars($user['nom_role']); ?></strong></div>
    </div>
    <a class="btn" href="index.php?page=edit">Modifier mes informations</a>
    <a class="danger-link" href="index.php?page=deactivate">Désactiver mon compte</a>
</div>
