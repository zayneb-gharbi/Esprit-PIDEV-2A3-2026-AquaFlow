<section class="page-title"><p class="eyebrow">MON COMPTE</p><h1>Sécurité du compte</h1><p>Choisissez vos moyens de connexion et protégez votre compte.</p></section>
<?php if ($codes) { ?>
<div class="card form-card security-card"><h2>Vos codes de secours</h2><p>Conservez-les dans un endroit privé. Ils sont affichés une seule fois et chacun peut être utilisé une fois.</p><ul class="recovery"><?php foreach ($codes as $code) { ?><li><code><?= h($code) ?></code></li><?php } ?></ul></div>
<?php } ?>
<div class="card form-card security-card">
    <h2>Google Authenticator</h2><span class="status"><?= $currentUser['totp_secret'] ? 'Activée' : 'Désactivée' ?></span>
    <?php if ($currentUser['totp_secret']) { ?>
    <p>Un deuxième code sera demandé après votre mot de passe ou votre connexion Google/Facebook.</p>
    <form method="post" action="index.php?page=security"><input type="hidden" name="action" value="disable"><label for="disable-code">Code actuel ou code de secours</label><input id="disable-code" name="code" autocomplete="one-time-code" maxlength="16" pattern="([0-9]{6}|[a-f0-9]{16})" required><div class="form-actions"><button class="btn-danger" type="submit">Désactiver la double authentification</button></div></form>
    <?php } elseif ($setup) { ?>
    <ol><li>Dans Google Authenticator, appuyez sur <strong>+</strong>, puis <strong>Saisir une clé de configuration</strong>.</li><li>Nom du compte : <strong>AquaFlow — <?= h($currentUser['email'] ?? $currentUser['nom']) ?></strong>.</li><li>Clé : <code class="secret"><?= h($setup['secret']) ?></code></li><li>Choisissez <strong>Basé sur l’heure</strong>, puis saisissez le code obtenu.</li></ol>
    <form method="post" action="index.php?page=security"><input type="hidden" name="action" value="enable"><label for="enable-code">Code à 6 chiffres</label><input id="enable-code" name="code" inputmode="numeric" autocomplete="one-time-code" maxlength="6" pattern="[0-9]{6}" required><div class="form-actions"><button class="btn" type="submit">Confirmer l’activation</button></div></form>
    <?php } else { ?>
    <p>Ajoutez une vérification avec votre téléphone lors de chaque connexion.</p>
    <form method="post" action="index.php?page=security"><input type="hidden" name="action" value="prepare"><div class="form-actions"><button class="btn" type="submit">Configurer Google Authenticator</button></div></form>
    <?php } ?>
</div>
<div class="card form-card security-card"><h2>Comptes associés</h2><p>Associez un service à votre compte pour retrouver le même profil lors de vos prochaines connexions.</p>
<?php foreach (['google' => 'Google', 'facebook' => 'Facebook'] as $provider => $label) { ?>
    <div class="provider-row"><strong><?= h($label) ?></strong>
    <?php if (in_array($provider, $services, true)) { ?><span class="status">Associé</span><?php } else { ?>
    <form method="post" action="index.php?page=oauth_start"><input type="hidden" name="provider" value="<?= h($provider) ?>"><button class="btn-light social" type="submit" <?= AmirSocial::disponible($provider) ? '' : 'disabled' ?>>Associer <?= h($label) ?></button></form>
    <?php } ?></div>
<?php } ?></div>
