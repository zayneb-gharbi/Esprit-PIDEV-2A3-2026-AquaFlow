<section class="page-title"><span class="eyebrow">ENVIRONNEMENT DE TEST</span><h1>Choisir un compte de démonstration</h1><p>Cette sélection simule une connexion. Elle sera remplacée par l’authentification réelle lors de l’intégration.</p></section>
<div class="card form-card"><form method="post" action="index.php?page=demo-login">
<label for="user_id">Se connecter en tant que</label><select id="user_id" name="user_id">
<?php foreach ($demoUsers as $u): ?><option value="<?= (int)$u['id'] ?>"><?= htmlspecialchars($u['prenom'].' '.$u['nom'].' — '.$u['role']) ?></option><?php endforeach; ?>
</select><div class="form-actions"><button class="btn" type="submit">Ouvrir la session de test</button></div></form>
<p class="hint">Aucun mot de passe n’est demandé : ce mode est uniquement destiné aux tests locaux.</p></div>
