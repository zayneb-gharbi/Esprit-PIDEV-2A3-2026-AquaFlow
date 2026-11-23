<?php
// Back-office : détail d'un événement et liste des participants.
// Variables : $event, $participants, $flash
?>
<div class="card">

    <div class="table-head">
        <div>
            <a class="back-link" href="index.php?page=admin-events">← Gérer les événements</a>
            <h1><?= e($event['title']) ?></h1>
        </div>
        <span class="status status-<?= e($event['computed_status']) ?>"><?= e($event['status_label']) ?></span>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <div class="event-detail-grid">
        <div>
            <h2>Description</h2>
            <p class="event-description"><?= nl2br(e($event['description'])) ?></p>
            <?php if (!empty($event['cancel_reason'])) { ?>
                <div class="message error"><strong>Motif d'annulation :</strong> <?= e($event['cancel_reason']) ?></div>
            <?php } ?>
        </div>
        <aside class="event-side">
            <h2>Informations</h2>
            <p><strong>Type :</strong> <?= e($event['event_type']) ?></p>
            <p><strong>Public :</strong> <?= e($event['audience_label']) ?></p>
            <p><strong>Début :</strong> <?= e($event['starts_display']) ?></p>
            <p><strong>Fin :</strong> <?= e($event['ends_display']) ?></p>
            <p><strong>Lieu :</strong> <?= e($event['location']) ?> — <?= e($event['zone']) ?></p>
            <p><strong>Capacité :</strong> <?= (int) $event['registered_count'] ?> / <?= (int) $event['capacity'] ?></p>
            <p><strong>Créé par :</strong> <?= e($event['creator_name']) ?></p>
            <div class="actions" style="margin-top:16px;">
                <a class="btn btn-sm" href="index.php?page=admin-event-edit&amp;id=<?= (int) $event['id'] ?>">Modifier</a>
                <?php if ($event['status'] !== 'cancelled') { ?>
                    <a class="btn btn-sm btn-danger" href="index.php?page=admin-event-cancel&amp;id=<?= (int) $event['id'] ?>">Annuler</a>
                <?php } ?>
            </div>
        </aside>
    </div>

    <h2 class="dashboard-section-title">Participants (<?= count($participants) ?>)</h2>

    <?php if (empty($participants)) { ?>
        <div class="empty">Aucun participant inscrit.</div>
    <?php } else { ?>
        <div class="table-wrap">
            <table class="list">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>E-mail</th>
                        <th>Rôle</th>
                        <th>Inscrit le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($participants as $participant) { ?>
                        <tr>
                            <td><?= e($participant['name']) ?></td>
                            <td><?= e($participant['email']) ?></td>
                            <td><?= e($participant['role_label']) ?></td>
                            <td><?= e($participant['registered_at_display']) ?></td>
                            <td>
                                <form method="post" action="index.php?page=admin-event-remove-participant" onsubmit="return confirm('Retirer ce participant ?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                                    <input type="hidden" name="registration_id" value="<?= (int) $participant['registration_id'] ?>">
                                    <button class="btn btn-sm btn-danger" type="submit">Retirer</button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</div>
