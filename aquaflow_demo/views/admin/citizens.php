<section class="page-title"><p class="eyebrow">ADMINISTRATION</p><h1>Liste des citoyens</h1><p>Comptes des citoyens inscrits sur AquaFlow.</p></section>
<div class="card table-card"><div class="table-head"><h2>Citoyens</h2><input type="search" class="table-search" placeholder="Rechercher..." data-search="citizens"></div>
<table><thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Statut</th></tr></thead><tbody id="citizens">
<?php foreach ($users as $u) { ?><tr><td><?php echo htmlspecialchars($u['nom']); ?></td><td><?php echo htmlspecialchars($u['email']); ?></td><td><?php echo htmlspecialchars($u['telephone'] ?? '-'); ?></td><td><span class="status"><?php echo htmlspecialchars($u['statut']); ?></span></td></tr><?php } ?>
</tbody></table></div>
