<div class="card">
    <h1>Historique global</h1>
    <p class="sub">Suivez les activités enregistrées sur l’ensemble de la plateforme.</p>
    <div class="stats-grid">
        <div class="stat-card"><p>Total des activités</p><h2><?= (int)$stats['total'] ?></h2></div>
        <div class="stat-card"><p>Activités aujourd’hui</p><h2><?= (int)$stats['today'] ?></h2></div>
        <div class="stat-card"><p>Utilisateurs concernés</p><h2><?= (int)$stats['users'] ?></h2></div>
    </div>
    <form method="get" action="index.php" class="filters">
        <input type="hidden" name="page" value="global-history">
        <input type="search" name="q" placeholder="Nom, prénom, email ou activité..." value="<?= e($filters['q']) ?>">
        <select name="role"><option value="">Tous les rôles</option>
            <option value="A" <?= $filters['role']==='A'?'selected':'' ?>>Administrateur</option>
            <option value="C" <?= $filters['role']==='C'?'selected':'' ?>>Citoyen</option>
            <option value="P" <?= $filters['role']==='P'?'selected':'' ?>>Plombier</option>
        </select>
        <select name="action"><option value="">Toutes les actions</option>
            <?php foreach ($actions as $a): ?><option value="<?= e($a) ?>" <?= $filters['action']===$a?'selected':'' ?>><?= e($a) ?></option><?php endforeach; ?>
        </select>
        <label>Date <select name="date_mode">
            <option value="before" <?= $filters['date_mode']==='before'?'selected':'' ?>>Avant</option>
            <option value="after" <?= $filters['date_mode']==='after'?'selected':'' ?>>Après</option>
            <option value="during" <?= $filters['date_mode']==='during'?'selected':'' ?>>Pendant</option>
        </select><input type="date" name="date" value="<?= e($filters['date']) ?>"></label>
        <button class="btn" type="submit">Appliquer</button>
        <a class="btn-light" href="index.php?page=global-history">Réinitialiser</a>
    </form>
    <p class="sub"><?= (int)$total ?> résultat(s)</p>
    <?php if (!$historique): ?><div class="empty">Aucune activité trouvée.</div><?php else: ?>
    <div class="table-wrap"><table class="list">
        <thead><tr><th>Utilisateur</th><th>Rôle</th><th>Action</th><th>Description</th><th>Date et heure</th></tr></thead>
        <tbody><?php foreach ($historique as $h): ?>
        <tr><td><?= e($h['prenom'].' '.$h['nom']) ?><br><small><?= e($h['email']) ?></small></td>
            <td><?= e(['A'=>'Administrateur','C'=>'Citoyen','P'=>'Plombier'][$h['role']] ?? $h['role']) ?></td>
            <td><?= e($h['action']) ?></td><td><?= e($h['description'] ?? '—') ?></td>
            <td><?= e(date('d/m/Y à H:i', strtotime($h['date_action']))) ?></td></tr>
        <?php endforeach; ?></tbody>
    </table></div><?php endif; ?>
    <?php if ($pages > 1): ?><div class="pagination">
        <?php for ($i=1; $i<=$pages; $i++): ?><a class="<?= $i===$page?'active':'' ?>" href="index.php?<?= e(http_build_query(array_merge(['page'=>'global-history'], $filters, ['p'=>$i]))) ?>"><?= $i ?></a><?php endfor; ?>
    </div><?php endif; ?>
</div>
