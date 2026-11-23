<?php
// Mes inscriptions aux événements.
// Variables : $events, $flash
?>
<div class="card">

    <div class="table-head">
        <div>
            <h1>Mes participations</h1>
            <p class="sub">Retrouvez les événements auxquels vous êtes inscrit.</p>
        </div>
        <a class="btn" href="index.php?page=events">Voir les événements</a>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <?php if (empty($events)) { ?>
        <div class="empty">Vous n'êtes inscrit à aucun événement pour le moment.</div>
    <?php } else { ?>
        <div class="table-wrap">
            <table class="list">
                <thead>
                    <tr>
                        <th>Événement</th>
                        <th>Date</th>
                        <th>Lieu</th>
                        <th>Inscrit le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($events as $event) { ?>
                        <tr>
                            <td><a href="index.php?page=event-detail&amp;id=<?= (int) $event['id'] ?>"><?= e($event['title']) ?></a></td>
                            <td><?= e($event['starts_display']) ?></td>
                            <td><?= e($event['location']) ?> (<?= e($event['zone']) ?>)</td>
                            <td><?= e($event['registered_at_display'] ?? $event['date_display']) ?></td>
                            <td>
                                <?php if (!empty($event['can_unregister'])) { ?>
                                    <form method="post" action="index.php?page=event-unregister">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                                        <input type="hidden" name="return_to" value="my-events">
                                        <button class="btn btn-danger" type="submit">Se désinscrire</button>
                                    </form>
                                <?php } else { ?>
                                    <span class="hint">Délai dépassé</span>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</div>
