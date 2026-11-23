<?php
/** View/FrontOffice/user_space.php — Attend : $user (UserModel) */
ob_start();
?>
<div class="card border-0 shadow-sm mt-5">
    <div class="card-body p-4">
        <h1 class="h4">Bienvenue, <?= e($user->getUsername()) ?> 👋</h1>
        <p><span class="badge" style="background:#00BDB5">Rôle : <?= e($user->getRole()) ?></span></p>
        <table class="table">
            <tr><th>Email</th><td><?= e($user->getEmail()) ?></td></tr>
            <tr><th>Membre depuis</th><td><?= e($user->getCreatedAt()) ?></td></tr>
        </table>
        <a class="btn btn-outline-secondary btn-sm" href="index.php?action=change_password">Changer mon mot de passe</a>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Mon espace - AquaFlow';
require __DIR__ . '/layout.php';
