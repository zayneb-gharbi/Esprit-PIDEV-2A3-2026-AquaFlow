<?php
/** View/FrontOffice/login.php — Attend : $errors, $csrfToken */
ob_start();
?>
<div class="card card-auth">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Connexion</h1>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php foreach ($errors as $err): ?><div><?= e($err) ?></div><?php endforeach; ?></div>
        <?php endif; ?>
        <?php if (!empty($_GET['reset'])): ?>
            <div class="alert alert-success">Mot de passe réinitialisé. Vous pouvez vous connecter.</div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=login">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
            <label class="form-label" for="email">Adresse email</label>
            <input class="form-control mb-3" type="email" id="email" name="email" required autocomplete="email">
            <label class="form-label" for="password">Mot de passe</label>
            <input class="form-control mb-3" type="password" id="password" name="password" required autocomplete="current-password">
            <button class="btn btn-aqua w-100" type="submit">Se connecter</button>
        </form>
        <div class="text-center text-muted-aqua my-3">ou</div>
        <?php integrationSocialButtons(); ?>
        <div class="text-center mt-3"><a href="index.php?action=forgot_password">Mot de passe oublié ?</a></div>
    </div>
</div>
<?php
$content = ob_get_clean();
$title = 'Connexion - AquaFlow';
require __DIR__ . '/layout.php';
