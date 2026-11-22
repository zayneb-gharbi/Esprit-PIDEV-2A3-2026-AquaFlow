<section class="page-title"><p class="eyebrow">BIENVENUE SUR AQUAFLOW</p><h1>Se connecter</h1><p>Retrouvez votre compte et vos services.</p></section>
<div class="card form-card">
    <form method="post" action="index.php?page=login">
        <label for="email">Adresse email</label><input id="email" type="email" name="email" maxlength="190" autocomplete="username" required>
        <label for="password">Mot de passe</label><input id="password" type="password" name="password" maxlength="72" autocomplete="current-password" required>
        <div class="form-actions"><button class="btn" type="submit">Se connecter</button></div>
    </form>
    <div class="separator">Ou continuer avec</div>
    <div class="social-buttons">
    <?php foreach (['google' => 'Google', 'facebook' => 'Facebook'] as $provider => $label) { ?>
        <form method="post" action="index.php?page=oauth_start"><input type="hidden" name="provider" value="<?= h($provider) ?>"><button class="btn-light social" type="submit" <?= AmirSocial::disponible($provider) ? '' : 'disabled' ?>><?= h($label) ?></button></form>
    <?php } ?>
    </div>
    <?php if (!AmirSocial::disponible('google') || !AmirSocial::disponible('facebook')) { ?><p class="help">Les services grisés ne sont pas encore disponibles.</p><?php } ?>
</div>
