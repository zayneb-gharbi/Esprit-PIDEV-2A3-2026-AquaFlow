<?php
// Views/pages/users_list.php : infos personnelles des citoyens ou des plombiers (HTML pur)
// variables : $users, $role ('C'|'P'), $heading, $page, $flash
$statutLabels = [
    'actif'      => 'Actif',
    'en_attente' => 'En attente',
    'bloque'     => 'Bloqué',
    'desactive'  => 'Désactivé',
];
?>
<div class="card">

    <div class="table-head">
        <h1><?= e($heading) ?></h1>
        <a class="btn" href="index.php?page=export-users&amp;role=<?= e($role) ?>">Exporter en Excel</a>
        <input type="text" id="search" placeholder="Rechercher un utilisateur...">
    </div>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <?php if (empty($users)) { ?>
        <div class="empty">Aucun utilisateur.</div>
    <?php } else { ?>
        <div class="table-wrap">
            <table class="list" id="usersTable">
                <thead>
                    <tr>
                        <th>Nom complet</th>
                        <th>E-mail</th>
                        <th>Téléphone</th>
                        <th>Statut</th>
                        <?php if ($role == 'P') { ?>
                            <th>Zone de service</th>
                            <th>Expérience</th>
                        <?php } ?>
                        <th>Inscrit le</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u) { ?>
                        <tr>
                            <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['telephone']) ?></td>
                            <td>
                                <span class="status <?= $u['statut'] == 'actif' ? '' : 'inactive' ?>">
                                    <?= e($statutLabels[$u['statut']] ?? $u['statut']) ?>
                                </span>
                            </td>
                            <?php if ($role == 'P') { ?>
                                <td><?= e($u['zone_service'] ?? '') ?></td>
                                <td><?= e($u['experience'] ?? '') ?> ans</td>
                            <?php } ?>
                            <td><?= e($u['date_creation'] ?? '') ?></td>
                            <td>
                                <div class="actions">
                                    <?php if ($u['statut'] == 'actif') { ?>
                                        <button type="button" class="btn-outline-danger block-trigger"
                                                data-action="block"
                                                data-id="<?= (int) $u['id_user'] ?>"
                                                data-name="<?= e($u['prenom'] . ' ' . $u['nom']) ?>">Bloquer</button>
                                    <?php } elseif ($u['statut'] == 'bloque') { ?>
                                        <button type="button" class="btn-outline-teal block-trigger"
                                                data-action="unblock"
                                                data-id="<?= (int) $u['id_user'] ?>"
                                                data-name="<?= e($u['prenom'] . ' ' . $u['nom']) ?>">Débloquer</button>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <!-- Modale Bloquer / Débloquer : remplace l'ancien confirm() du navigateur -->
    <div class="modal" id="blockModal" aria-hidden="true">
        <div class="modal-backdrop" data-close-modal></div>
        <div class="modal-dialog" role="dialog" aria-modal="true" aria-labelledby="blockModalTitle">
            <h2 id="blockModalTitle">Bloquer ce compte</h2>
            <p class="modal-text" id="blockModalText"></p>
            <form method="POST" action="index.php?page=<?= e($page) ?>" id="blockModalForm">
                <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                <input type="hidden" name="id_user" id="blockUserId" value="">
                <input type="hidden" name="action" id="blockAction" value="block">
                <div class="modal-field" id="blockReasonField">
                    <label for="blockReason">Motif (facultatif, 5 à 255 caractères)</label>
                    <textarea id="blockReason" name="reason" rows="3" maxlength="255"></textarea>
                    <p class="field-error" id="blockReasonError"></p>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-light" data-close-modal>Annuler</button>
                    <button type="submit" class="btn" id="blockConfirmBtn">Bloquer</button>
                </div>
            </form>
        </div>
    </div>

</div>
