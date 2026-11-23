<?php
// Formulaire de création / modification d'un événement.
// Variables : $isEdit, $event, $values, $errors, $types, $zones, $flash
$action = $isEdit
    ? 'index.php?page=admin-event-edit&amp;id=' . (int) $event['id']
    : 'index.php?page=admin-event-create';
?>
<div class="card narrow-card">

    <div class="table-head">
        <div>
            <a class="back-link" href="index.php?page=admin-events">← Gérer les événements</a>
            <h1><?= $isEdit ? 'Modifier l\'événement' : 'Nouvel événement' ?></h1>
        </div>
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <?php if (!empty($errors)) { ?>
        <div class="message error">Veuillez corriger les champs indiqués.</div>
    <?php } ?>

    <form method="post" action="<?= $action ?>" class="event-form">
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <label>
            Titre *
            <input type="text" name="title" value="<?= e($values['title']) ?>" required>
            <?php if (isset($errors['title'])) { ?><span class="field-error"><?= e($errors['title']) ?></span><?php } ?>
        </label>

        <label>
            Description *
            <textarea name="description" rows="5" required><?= e($values['description']) ?></textarea>
            <?php if (isset($errors['description'])) { ?><span class="field-error"><?= e($errors['description']) ?></span><?php } ?>
        </label>

        <div class="form-row">
            <label>
                Type *
                <select name="event_type" required>
                    <?php foreach ($types as $type) { ?>
                        <option value="<?= e($type) ?>" <?= $values['event_type'] === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                    <?php } ?>
                </select>
                <?php if (isset($errors['event_type'])) { ?><span class="field-error"><?= e($errors['event_type']) ?></span><?php } ?>
            </label>

            <label>
                Public *
                <select name="audience" required>
                    <option value="all" <?= $values['audience'] === 'all' ? 'selected' : '' ?>>Tous</option>
                    <option value="citizen" <?= $values['audience'] === 'citizen' ? 'selected' : '' ?>>Citoyens</option>
                    <option value="plumber" <?= $values['audience'] === 'plumber' ? 'selected' : '' ?>>Plombiers</option>
                </select>
                <?php if (isset($errors['audience'])) { ?><span class="field-error"><?= e($errors['audience']) ?></span><?php } ?>
            </label>
        </div>

        <div class="form-row">
            <label>
                Début *
                <input type="datetime-local" name="starts_at" value="<?= e($values['starts_at']) ?>" required>
                <?php if (isset($errors['starts_at'])) { ?><span class="field-error"><?= e($errors['starts_at']) ?></span><?php } ?>
            </label>

            <label>
                Fin *
                <input type="datetime-local" name="ends_at" value="<?= e($values['ends_at']) ?>" required>
                <?php if (isset($errors['ends_at'])) { ?><span class="field-error"><?= e($errors['ends_at']) ?></span><?php } ?>
            </label>
        </div>

        <div class="form-row">
            <label>
                Lieu *
                <input type="text" name="location" value="<?= e($values['location']) ?>" required>
                <?php if (isset($errors['location'])) { ?><span class="field-error"><?= e($errors['location']) ?></span><?php } ?>
            </label>

            <label>
                Gouvernorat *
                <select name="zone" required>
                    <?php foreach ($zones as $zone) { ?>
                        <option value="<?= e($zone) ?>" <?= $values['zone'] === $zone ? 'selected' : '' ?>><?= e($zone) ?></option>
                    <?php } ?>
                </select>
                <?php if (isset($errors['zone'])) { ?><span class="field-error"><?= e($errors['zone']) ?></span><?php } ?>
            </label>
        </div>

        <div class="form-row">
            <label>
                Capacité * (1 à 500)
                <input type="number" name="capacity" min="1" max="500" value="<?= e($values['capacity']) ?>" required>
                <?php if (isset($errors['capacity'])) { ?><span class="field-error"><?= e($errors['capacity']) ?></span><?php } ?>
            </label>

            <label>
                Statut *
                <select name="status" required>
                    <option value="draft" <?= $values['status'] === 'draft' ? 'selected' : '' ?>>Brouillon</option>
                    <option value="published" <?= $values['status'] === 'published' ? 'selected' : '' ?>>Publié</option>
                </select>
                <?php if (isset($errors['status'])) { ?><span class="field-error"><?= e($errors['status']) ?></span><?php } ?>
            </label>
        </div>

        <div class="actions">
            <button class="btn" type="submit"><?= $isEdit ? 'Enregistrer' : 'Créer l\'événement' ?></button>
            <a class="btn btn-secondary" href="index.php?page=admin-events">Annuler</a>
        </div>
    </form>

</div>
