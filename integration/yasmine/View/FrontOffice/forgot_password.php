<?php
/** View/FrontOffice/forgot_password.php — Attend : $errors, $csrfToken */
ob_start();
?>
<div class="card card-auth">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Mot de passe oublié</h1>

        <?php if (!empty($_GET['sent'])): ?>
            <div class="alert alert-success">
                Si un compte correspond à cette adresse, un email contenant un lien de réinitialisation (valable 1 heure) vient d'être envoyé.
            </div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=forgot_password">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <label class="form-label" for="email">Adresse email du compte</label>
            <input class="form-control mb-3" type="email" id="email" name="email" required autocomplete="email">
            <button class="btn btn-aqua w-100" type="submit">Envoyer le lien</button>
        </form>
        <div class="text-center mt-3"><a href="index.php?action=login">Retour à la connexion</a></div>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Mot de passe oublié - AquaFlow';
require __DIR__ . '/layout.php';
