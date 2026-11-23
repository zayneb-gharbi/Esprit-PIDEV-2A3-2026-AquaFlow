<?php
// Views/pages/profile_edit.php : formulaire "Modifier mes informations" (PR 4)
// variables : $user, $values, $errors, $flash
$governorates = [
    'Tunis', 'Ariana', 'Ben Arous', 'Manouba', 'Nabeul', 'Zaghouan', 'Bizerte',
    'Beja', 'Jendouba', 'Le Kef', 'Siliana', 'Sousse', 'Monastir', 'Mahdia',
    'Sfax', 'Kairouan', 'Kasserine', 'Sidi Bouzid', 'Gabes', 'Medenine',
    'Tataouine', 'Gafsa', 'Tozeur', 'Kebili'
];
?>
<div class="card narrow">

    <h1>Modifier mes informations</h1>
    <p class="sub">Mettez à jour vos coordonnées. Votre rôle et votre mot de passe restent inchangés.</p>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>
    <?php if (!empty($errors['general'])) { ?>
        <div class="message error"><?= e($errors['general']) ?></div>
    <?php } ?>

    <form method="post" action="index.php?page=profile-edit" id="profileEditForm" novalidate>
        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">

        <div class="form-group">
            <label for="prenom">Prénom</label>
            <input type="text" id="prenom" name="prenom" maxlength="50"
                   value="<?= e($values['prenom']) ?>">
            <p class="field-error" id="prenomError"><?= e($errors['prenom'] ?? '') ?></p>
        </div>

        <div class="form-group">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" maxlength="50"
                   value="<?= e($values['nom']) ?>">
            <p class="field-error" id="nomError"><?= e($errors['nom'] ?? '') ?></p>
        </div>

        <div class="form-group">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" maxlength="150"
                   value="<?= e($values['email']) ?>">
            <p class="field-error" id="emailError"><?= e($errors['email'] ?? '') ?></p>
        </div>

        <div class="form-group">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" maxlength="14"
                   value="<?= e($values['telephone']) ?>">
            <p class="field-error" id="telephoneError"><?= e($errors['telephone'] ?? '') ?></p>
        </div>

        <?php if ($user['role'] === 'P') { ?>
            <div class="form-group">
                <label for="zone_service">Zone de service</label>
                <select id="zone_service" name="zone_service">
                    <option value="">Sélectionnez une zone</option>
                    <?php foreach ($governorates as $governorate) { ?>
                        <option value="<?= e($governorate) ?>"
                            <?= $values['zone_service'] === $governorate ? 'selected' : '' ?>>
                            <?= e($governorate) ?>
                        </option>
                    <?php } ?>
                </select>
                <p class="field-error" id="zone_serviceError"><?= e($errors['zone_service'] ?? '') ?></p>
            </div>

            <div class="form-group">
                <label for="experience">Années d'expérience</label>
                <input type="number" id="experience" name="experience" min="0" max="70"
                       value="<?= e($values['experience']) ?>">
                <p class="field-error" id="experienceError"><?= e($errors['experience'] ?? '') ?></p>
            </div>
        <?php } ?>

        <div class="modal-actions" style="margin-top:8px;">
            <a class="btn-light" href="index.php?page=profile">Annuler</a>
            <button type="submit" class="btn">Enregistrer</button>
        </div>
    </form>

</div>
