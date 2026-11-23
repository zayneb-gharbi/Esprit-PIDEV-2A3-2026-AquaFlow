<?php
// Liste front-office des événements à venir.
// Variables : $events, $filters, $types, $zones, $flash
?>
<div class="card">

    <div class="table-head">
        <div>
            <h1>Événements à venir</h1>
            <p class="sub">Découvrez les actions Aqua Flow ouvertes aux citoyens et aux plombiers.</p>
        </div>
        <a class="btn" href="index.php?page=my-events">Mes participations</a>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <form class="event-filters" method="get" action="index.php">
        <input type="hidden" name="page" value="events">
        <input type="text" name="q" placeholder="Rechercher un événement ou un lieu..." value="<?= e($filters['q']) ?>">
        <select name="event_type">
            <option value="">Tous les types</option>
            <?php foreach ($types as $type) { ?>
                <option value="<?= e($type) ?>" <?= $filters['event_type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
            <?php } ?>
        </select>
        <select name="zone">
            <option value="">Tous les gouvernorats</option>
            <?php foreach ($zones as $zone) { ?>
                <option value="<?= e($zone) ?>" <?= $filters['zone'] === $zone ? 'selected' : '' ?>><?= e($zone) ?></option>
            <?php } ?>
        </select>
        <button class="btn" type="submit">Filtrer</button>
    </form>

    <?php if (empty($events)) { ?>
        <div class="empty">Aucun événement ne correspond à ces filtres.</div>
    <?php } else { ?>
        <div class="event-grid">
            <?php foreach ($events as $event) { ?>
                <article class="event-card">
                    <div class="event-card-top">
                        <span class="event-type"><?= e($event['event_type']) ?></span>
                        <span class="status status-<?= e($event['computed_status']) ?>"><?= e($event['status_label']) ?></span>
                    </div>
                    <h2><?= e($event['title']) ?></h2>
                    <p class="event-meta">📅 <?= e($event['starts_display']) ?> &nbsp;·&nbsp; 📍 <?= e($event['location']) ?> (<?= e($event['zone']) ?>)</p>
                    <p class="event-meta">👥 Public : <?= e($event['audience_label']) ?> &nbsp;·&nbsp; <?= (int) $event['remaining'] ?> place(s) restante(s)</p>
                    <p class="event-desc"><?= e(mb_strimwidth($event['description'], 0, 180, '…', 'UTF-8')) ?></p>
                    <a class="btn" href="index.php?page=event-detail&amp;id=<?= (int) $event['id'] ?>">Voir le détail</a>
                </article>
            <?php } ?>
        </div>
    <?php } ?>

</div>
