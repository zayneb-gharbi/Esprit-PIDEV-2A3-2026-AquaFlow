<section class="page-title"><p class="eyebrow">ADMINISTRATION</p><h1>Liste des plombiers</h1><p>Consultez les comptes des plombiers de la plateforme.</p></section>
<div class="card table-card"><div class="table-head"><h2>Plombiers</h2><input type="search" class="table-search" placeholder="Rechercher..." data-search="plumbers"></div>
<table><thead><tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Statut</th></tr></thead><tbody id="plumbers">
<?php foreach ($users as $u) { ?><tr><td><?php echo htmlspecialchars($u['nom']); ?></td><td><?php echo htmlspecialchars($u['email']); ?></td><td><?php echo htmlspecialchars($u['telephone'] ?? '-'); ?></td><td><span class="status"><?php echo htmlspecialchars($u['statut']); ?></span></td></tr><?php } ?>
</tbody></table></div>
