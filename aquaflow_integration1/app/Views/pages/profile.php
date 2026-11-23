<?php
// Views/pages/profile.php : informations personnelles (HTML pur)
// variables : $user, $roleLabel
?>
<div class="card narrow">
    <h1>Bienvenue, <?= e($user['prenom']) ?></h1>
    <span class="badge"><?= e($roleLabel) ?></span>

    <table class="info">
        <tr><th>Prénom</th><td><?= e($user['prenom']) ?></td></tr>
        <tr><th>Nom</th><td><?= e($user['nom']) ?></td></tr>
        <tr><th>E-mail</th><td><?= e($user['email']) ?></td></tr>
        <tr><th>Téléphone</th><td><?= e($user['telephone']) ?></td></tr>
        <?php if ($user['role'] == 'P') { ?>
            <tr><th>Zone de service</th><td><?= e($user['zone_service'] ?? '') ?></td></tr>
            <tr><th>Expérience</th><td><?= e($user['experience'] ?? '') ?> ans</td></tr>
        <?php } ?>
        <tr><th>Membre depuis</th><td><?= e($user['date_creation'] ?? '') ?></td></tr>
    </table>
    <?php if (in_array($user['role'], ['C', 'P'], true) && $user['statut'] === 'actif') { ?>
        <form method="post" action="index.php?page=profile" onsubmit="return confirm('Confirmer la désactivation de votre compte ?');" style="margin-top: 20px;">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>" />
            <button class="btn-reject" type="submit" name="deactivate" value="1">Désactiver mon compte</button>
        </form>
    <?php } ?>
</div>
