<?php
/**
 * View/FrontOffice/change_password.php — Attend : $user, $errors, $csrfToken
 * Utilisée par tous les rôles : le layout dépend du rôle (admin = BackOffice).
 */
$isAdmin = ($_SESSION['role'] ?? '') === 'admin';
ob_start();
?>
<div>
    <div class="card border-0 shadow-sm" style="max-width:460px;<?= $isAdmin ? '' : 'margin:60px auto;' ?>">
        <div class="card-body p-4">
            <h1 class="h4 mb-3">Changer mon mot de passe</h1>

            <?php if (!empty($_GET['changed'])): ?>
                <div class="alert alert-success">Mot de passe modifié. Un email de confirmation vous a été envoyé.</div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=change_password">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <label class="form-label" for="current_password">Mot de passe actuel</label>
                <input class="form-control mb-3" type="password" id="current_password" name="current_password" required autocomplete="current-password">
                <label class="form-label" for="new_password">Nouveau mot de passe (8 caractères min., lettre + chiffre)</label>
                <input class="form-control mb-3" type="password" id="new_password" name="new_password" required autocomplete="new-password">
                <label class="form-label" for="confirm_password">Confirmer le nouveau mot de passe</label>
                <input class="form-control mb-3" type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                <button class="btn btn-aqua w-100" type="submit">Enregistrer</button>
            </form>
            <div class="mt-3"><a href="index.php?action=forgot_password">J'ai oublié mon mot de passe actuel</a></div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Changer le mot de passe';
if ($isAdmin) {
    $activePage = 'password';
    require __DIR__ . '/../BackOffice/layout_admin.php';
} else {
    require __DIR__ . '/layout.php';
}
