<section class="page-title"><span class="eyebrow">ADMINISTRATION</span><h1>Historique global des utilisateurs</h1><p>Consultez les activités enregistrées sur l’ensemble de la plateforme.</p></section>
<div class="card table-card"><form class="filters" method="get"><input type="hidden" name="page" value="global-history">
<input type="search" name="q" placeholder="Nom, prénom, email ou action" value="<?= htmlspecialchars($filters['q']) ?>">
<select name="action"><option value="">Toutes les actions</option><?php foreach ($actions as $a): ?><option value="<?= htmlspecialchars($a) ?>" <?= $filters['action']===$a?'selected':'' ?>><?= htmlspecialchars($a) ?></option><?php endforeach; ?></select>
<input type="date" name="date" value="<?= htmlspecialchars($filters['date']) ?>"><button class="btn" type="submit">Filtrer</button><a class="btn-light" href="index.php?page=global-history">Réinitialiser</a></form>
<div class="table-head"><div><h2>Journal d’activité</h2><p class="muted"><?= count($historique) ?> résultat(s)</p></div></div>
<?php if (!$historique): ?><div class="empty">Aucune activité ne correspond à votre recherche.</div><?php else: ?>
<table><thead><tr><th>Utilisateur</th><th>Rôle</th><th>Action</th><th>Description</th><th>Date et heure</th></tr></thead><tbody>
<?php foreach ($historique as $h): ?><tr><td><strong><?= htmlspecialchars($h['prenom'].' '.$h['nom']) ?></strong><small class="subline"><?= htmlspecialchars($h['email']) ?></small></td><td><span class="role"><?= htmlspecialchars($h['nom_role']) ?></span></td><td><?= htmlspecialchars($h['action']) ?></td><td><?= htmlspecialchars($h['description'] ?? '') ?></td><td><?= htmlspecialchars(date('d/m/Y à H:i', strtotime($h['date_action']))) ?></td></tr><?php endforeach; ?>
</tbody></table><?php endif; ?></div>
