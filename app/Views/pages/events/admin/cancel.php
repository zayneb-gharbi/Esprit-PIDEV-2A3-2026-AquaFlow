<?php
// Confirmation d'annulation avec motif obligatoire.
// Variables : $event, $reason, $errors, $flash
?>
<div class="card narrow-card">

    <div class="table-head">
        <div>
            <a class="back-link" href="index.php?page=admin-event-detail&amp;id=<?= (int) $event['id'] ?>">← Détail de l'événement</a>
            <h1>Annuler l'événement</h1>
        </div>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <div class="message error">
        Vous êtes sur le point d'annuler <strong><?= e($event['title']) ?></strong>.
        L'événement sera conservé dans l'historique avec son motif d'annulation.
    </div>

    <form method="post" action="index.php?page=admin-event-cancel&amp;id=<?= (int) $event['id'] ?>" class="event-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>
            Motif d'annulation * (500 caractères maximum)
            <textarea name="cancel_reason" rows="5" required><?= e($reason) ?></textarea>
            <?php if (isset($errors['cancel_reason'])) { ?><span class="field-error"><?= e($errors['cancel_reason']) ?></span><?php } ?>
        </label>

        <div class="actions">
            <button class="btn btn-danger" type="submit">Confirmer l'annulation</button>
            <a class="btn btn-secondary" href="index.php?page=admin-events">Retour</a>
        </div>
    </form>

</div>
