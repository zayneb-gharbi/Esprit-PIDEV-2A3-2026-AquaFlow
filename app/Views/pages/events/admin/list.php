<?php
// Back-office : liste et statistiques des événements.
// Variables : $events, $stats, $filters, $statuses, $types, $flash
?>
<div class="card">

    <div class="table-head">
        <div>
            <h1>Gérer les événements</h1>
            <p class="sub">Créez, publiez, annulez et suivez les inscriptions.</p>
        </div>
        <a class="btn" href="index.php?page=admin-event-create">+ Nouvel événement</a>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <div class="stats-grid event-stats">
        <div class="stat-card"><p>Total</p><h2><?= (int) ($stats['total'] ?? 0) ?></h2></div>
        <div class="stat-card"><p>Publiés</p><h2><?= (int) ($stats['published'] ?? 0) ?></h2></div>
        <div class="stat-card"><p>Brouillons</p><h2><?= (int) ($stats['drafts'] ?? 0) ?></h2></div>
        <div class="stat-card"><p>Inscriptions actives</p><h2><?= (int) ($stats['registrations'] ?? 0) ?></h2></div>
    </div>

    <form class="event-filters" method="get" action="index.php">
        <input type="hidden" name="page" value="admin-events">
        <input type="text" name="q" placeholder="Rechercher..." value="<?= e($filters['q']) ?>">
        <select name="status">
            <option value="">Tous les statuts</option>
            <?php foreach ($statuses as $key => $label) { ?>
                <option value="<?= e($key) ?>" <?= $filters['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php } ?>
        </select>
        <select name="event_type">
            <option value="">Tous les types</option>
            <?php foreach ($types as $type) { ?>
                <option value="<?= e($type) ?>" <?= $filters['event_type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
            <?php } ?>
        </select>
        <button class="btn" type="submit">Filtrer</button>
    </form>

    <?php if (empty($events)) { ?>
        <div class="empty">Aucun événement.</div>
    <?php } else { ?>
        <div class="table-wrap">
            <table class="list">
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>Date</th>
                        <th>Statut</th>
                        <th>Inscriptions</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event) { ?>
                        <tr>
                            <td>
                                <a href="index.php?page=admin-event-detail&amp;id=<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a>
                                <small><?= e($event['event_type']) ?> · <?= e($event['zone']) ?></small>
                            </td>
                            <td><?= e($event['starts_display']) ?></td>
                            <td><span class="status status-<?= e($event['computed_status']) ?>"><?= e($event['status_label']) ?></span></td>
                            <td><?= (int) $event['registered_count'] ?> / <?= (int) $event['capacity'] ?></td>
                            <td>
                                <div class="actions">
                                    <a class="btn btn-sm" href="index.php?page=admin-event-edit&amp;id=<?= (int) $event['id'] ?>">Modifier</a>
                                    <?php if ($event['status'] !== 'cancelled') { ?>
                                        <a class="btn btn-sm btn-danger" href="index.php?page=admin-event-cancel&amp;id=<?= (int) $event['id'] ?>">Annuler</a>
                                    <?php } ?>
                                    <?php if ($event['status'] === 'draft') { ?>
                                        <form method="post" action="index.php?page=admin-event-delete" onsubmit="return confirm('Supprimer ce brouillon ?');">
                                            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                            <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                                            <button class="btn btn-sm btn-danger" type="submit">Supprimer</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</div>
