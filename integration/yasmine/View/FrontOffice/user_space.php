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
$content .= '<p><a href="index.php?page=edit">Modifier mes informations</a> · <a href="index.php?page=security">Sécurité du compte</a></p>';
if (($_SESSION['role'] ?? '')!=='admin') { $content .= '<form method="post" action="index.php?page=deactivate"><input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'"><button type="submit" class="btn">Désactiver mon compte</button></form>'; }

$title = 'Mon espace - AquaFlow';
require __DIR__ . '/layout.php';
