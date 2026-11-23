<div class="card">
    <h1>Mon historique</h1>
    <p class="sub">Retrouvez les activités effectuées sur votre compte AquaFlow.</p>
    <div class="filters">
        <form method="get" action="index.php">
            <input type="hidden" name="page" value="personal-history">
            <input type="search" name="q" placeholder="Rechercher une action ou description..." value="<?= e($filters['q']) ?>">
            <select name="action"><option value="">Toutes les actions</option>
                <?php foreach ($actions as $a): ?><option value="<?= e($a) ?>" <?= $filters['action'] === $a ? 'selected' : '' ?>><?= e($a) ?></option><?php endforeach; ?>
            </select>
            <label>Date
                <select name="date_mode">
                    <option value="before" <?= $filters['date_mode']==='before'?'selected':'' ?>>Avant</option>
                    <option value="after" <?= $filters['date_mode']==='after'?'selected':'' ?>>Après</option>
                    <option value="during" <?= $filters['date_mode']==='during'?'selected':'' ?>>Pendant</option>
                </select>
                <input type="date" name="date" value="<?= e($filters['date']) ?>">
            </label>
            <button type="submit" class="btn">Rechercher</button>
            <a class="btn-light" href="index.php?page=personal-history">Effacer</a>
        </form>
    </div>
    <p class="sub"><?= (int)$total ?> activité(s) trouvée(s)</p>
    <?php if (!$historique): ?><div class="empty">Aucune activité trouvée.</div><?php else: ?>
    <div class="table-wrap"><table class="list">
        <thead><tr><th>Action</th><th>Description</th><th>Date et heure</th></tr></thead>
        <tbody><?php foreach ($historique as $h): ?><tr>
            <td><?= e($h['action']) ?></td><td><?= e($h['description'] ?? '—') ?></td>
            <td><?= e(date('d/m/Y à H:i', strtotime($h['date_action']))) ?></td>
        </tr><?php endforeach; ?></tbody>
    </table></div><?php endif; ?>
    <?php if ($pages > 1): ?><div class="pagination">
        <?php for ($i=1; $i<=$pages; $i++): ?><a class="<?= $i===$page?'active':'' ?>" href="index.php?<?= e(http_build_query(array_merge(['page'=>'personal-history'], $filters, ['p'=>$i]))) ?>"><?= $i ?></a><?php endfor; ?>
    </div><?php endif; ?>
</div>
