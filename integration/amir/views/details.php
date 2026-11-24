<section class="page-title"><p class="eyebrow">ADMINISTRATION</p><h1>Détails de l’utilisateur</h1><p>Fiche du compte n° <?= (int) $details['id'] ?>.</p></section>
<div class="card profile-card"><div class="avatar">👤</div><h2><?= h($details['nom']) ?></h2><span class="status"><?= h($details['statut']) ?></span>
<div class="info-grid">
<?php foreach (['email' => 'Adresse email','telephone' => 'Téléphone','adresse' => 'Adresse','nom_role' => 'Rôle','created_at' => 'Date de création','double_auth' => 'Double authentification','zone_service'=>'Zone de service','experience'=>'Expérience (années)'] as $key => $label) { ?><div><small><?= h($label) ?></small><strong><?= h(isset($details[$key]) && $details[$key]!=='' ? $details[$key] : 'Non renseigné') ?></strong></div><?php } ?>
<div><small>Services associés</small><strong><?= h($details['services'] ? implode(', ', $details['services']) : 'Aucun') ?></strong></div>
</div><a class="btn-light" href="index.php?page=<?= integrationMode()==='groupe' ? ($details['nom_role']==='Plombier' ? 'plombiers':'citoyens') : 'users' ?>">Retour à la liste</a></div>
