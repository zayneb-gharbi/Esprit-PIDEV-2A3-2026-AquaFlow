<section class="page-title"><p class="eyebrow">ADMINISTRATION</p><h1>Liste des <?= $page === 'citizens' ? 'citoyens' : 'plombiers' ?></h1><p>Consultez les informations d’un compte.</p></section>
<div class="card table-card"><div class="table-head"><h2>Comptes</h2><input type="search" class="table-search" placeholder="Rechercher..." aria-label="Rechercher un utilisateur" data-search="<?= h($page) ?>"></div>
<table><thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Statut</th><th>Détails</th></tr></thead><tbody id="<?= h($page) ?>">
<?php foreach ($users as $u) { ?><tr><td><?= h($u['nom']) ?></td><td><?= h($u['email'] ?? 'Non renseigné') ?></td><td><?= h($u['telephone'] ?: '-') ?></td><td><span class="status"><?= h($u['statut']) ?></span></td><td><a class="btn-light" href="index.php?page=details&amp;id=<?= (int) $u['id'] ?>">Consulter</a></td></tr><?php } ?>
<?php if (!$users) { ?><tr><td colspan="5">Aucun compte à afficher.</td></tr><?php } ?>
</tbody></table></div>
