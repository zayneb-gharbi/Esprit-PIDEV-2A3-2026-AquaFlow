<?php
/** View/FrontOffice/reset_password.php — Attend : $tokenValid, $token, $errors, $csrfToken */
ob_start();
?>
<div class="card card-auth">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Nouveau mot de passe</h1>

        <?php if (!$tokenValid): ?>
            <div class="alert alert-danger">Ce lien est invalide ou a expiré.</div>
            <a href="index.php?action=forgot_password">Refaire une demande</a>
        <?php else: ?>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
            <?php endif; ?>
            <form method="POST" action="index.php?action=reset_password">
                <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
                <input type="hidden" name="token" value="<?= e($token) ?>">
                <label class="form-label" for="new_password">Nouveau mot de passe (8 caractères min., lettre + chiffre)</label>
                <input class="form-control mb-3" type="password" id="new_password" name="new_password" required autocomplete="new-password">
                <label class="form-label" for="confirm_password">Confirmer le mot de passe</label>
                <input class="form-control mb-3" type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                <button class="btn btn-aqua w-100" type="submit">Réinitialiser</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Réinitialisation - AquaFlow';
require __DIR__ . '/layout.php';
