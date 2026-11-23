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
                                <?php if ($u['statut'] == 'actif') { ?>
                                    <form method="POST" action="index.php?page=<?= e($page) ?>"
                                          onsubmit="return confirm('Désactiver ce compte ?');">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                        <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                        <button type="submit" class="btn-reject">Désactiver</button>
                                    </form>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

</div>
