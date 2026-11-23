<?php
// Détail d'un événement + inscription / désinscription (CSRF).
// Variables : $event, $variant, $reasons, $canUnregister, $flash
?>
<div class="card event-detail">

    <div class="table-head">
        <div>
            <a class="back-link" href="index.php?page=events">← Tous les événements</a>
            <h1><?= e($event['title']) ?></h1>
        </div>
        <span class="status status-<?= e($event['computed_status']) ?>"><?= e($event['status_label']) ?></span>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <div class="event-detail-grid">
        <div>
            <h2>À propos</h2>
            <p class="event-description"><?= nl2br(e($event['description'])) ?></p>
        </div>
        <aside class="event-side">
            <h2>Informations pratiques</h2>
            <p><strong>Début :</strong> <?= e($event['starts_display']) ?></p>
            <p><strong>Fin :</strong> <?= e($event['ends_display']) ?></p>
            <p><strong>Lieu :</strong> <?= e($event['location']) ?> — <?= e($event['zone']) ?></p>
            <p><strong>Type :</strong> <?= e($event['event_type']) ?></p>
            <p><strong>Public :</strong> <?= e($event['audience_label']) ?></p>
            <p><strong>Places :</strong> <?= (int) $event['registered_count'] ?> / <?= (int) $event['capacity'] ?> (<?= (int) $event['remaining'] ?> restantes)</p>
            <p><strong>Organisé par :</strong> <?= e($event['creator_name']) ?></p>
        </aside>
    </div>

    <div class="event-action">
        <?php if ($variant === 'register') { ?>
            <form method="post" action="index.php?page=event-register">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                <button class="btn" type="submit">Je m'inscris</button>
            </form>
        <?php } elseif ($variant === 'registered') { ?>
            <div class="message success">Vous êtes inscrit à cet événement.</div>
            <?php if ($canUnregister) { ?>
                <form method="post" action="index.php?page=event-unregister" style="margin-top:12px;">
                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                    <input type="hidden" name="event_id" value="<?= (int) $event['id'] ?>">
                    <input type="hidden" name="return_to" value="event-detail">
                    <button class="btn btn-danger" type="submit">Me désinscrire</button>
                </form>
            <?php } else { ?>
                <p class="hint">La désinscription n'est plus possible moins de 24 heures avant le début.</p>
            <?php } ?>
        <?php } else { ?>
            <div class="message error">
                <strong>Inscription impossible.</strong>
                <ul>
                    <?php foreach ($reasons as $reason) { ?>
                        <li><?= e($reason) ?></li>
                    <?php } ?>
                </ul>
            </div>
        <?php } ?>
    </div>

</div>
