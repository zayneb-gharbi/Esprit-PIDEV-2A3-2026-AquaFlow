<section class="page-title"><span class="eyebrow">ESPACE UTILISATEUR</span><h1>Mon historique</h1><p>Retrouvez les actions effectuées sur votre compte AquaFlow.</p></section>
<div class="card table-card"><div class="table-head"><div><h2>Mes activités</h2><p class="muted"><?= count($historique) ?> activité(s) enregistrée(s)</p></div><span class="pill"><?= htmlspecialchars($_SESSION['user']['prenom'].' '.$_SESSION['user']['nom']) ?></span></div>
<?php if (!$historique): ?><div class="empty">Aucune activité n’est encore enregistrée.</div><?php else: ?>
<table><thead><tr><th>Action</th><th>Description</th><th>Date et heure</th></tr></thead><tbody>
<?php foreach ($historique as $h): ?><tr><td><strong><?= htmlspecialchars($h['action']) ?></strong></td><td><?= htmlspecialchars($h['description'] ?? '') ?></td><td><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($h['date_action']))) ?></td></tr><?php endforeach; ?>
</tbody></table><?php endif; ?></div>
