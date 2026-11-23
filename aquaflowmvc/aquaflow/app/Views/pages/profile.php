<?php
// Views/pages/profile.php : informations personnelles (HTML pur)
// variables : $user, $roleLabel
?>
<div class="card narrow">
    <h1>Welcome, <?= e($user['prenom']) ?></h1>
    <span class="badge"><?= e($roleLabel) ?></span>

    <table class="info">
        <tr><th>First name</th><td><?= e($user['prenom']) ?></td></tr>
        <tr><th>Last name</th><td><?= e($user['nom']) ?></td></tr>
        <tr><th>Email</th><td><?= e($user['email']) ?></td></tr>
        <tr><th>Phone</th><td><?= e($user['telephone']) ?></td></tr>
        <?php if ($user['role'] == 'P') { ?>
            <tr><th>Service area</th><td><?= e($user['zone_service'] ?? '') ?></td></tr>
            <tr><th>Experience</th><td><?= e($user['experience'] ?? '') ?> years</td></tr>
        <?php } ?>
        <tr><th>Member since</th><td><?= e($user['date_creation'] ?? '') ?></td></tr>
    </table>
</div>
