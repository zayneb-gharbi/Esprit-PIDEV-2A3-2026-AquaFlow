<?php
// Views/pages/admin.php : comptes plombiers en attente (HTML pur)
// variables : $admin, $enAttente, $flash
?>
<div class="card">

    <h1>Comptes plombiers en attente</h1>
    <p class="sub">
        Connecté en tant que <?= e($admin['prenom']) ?> <?= e($admin['nom']) ?> (administrateur)
    </p>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <?php if (empty($enAttente)) { ?>
        <div class="empty">Aucun compte n'est en attente d'approbation.</div>
    <?php } else { ?>
        <div class="table-wrap">
            <table class="list">
                <tr>
                    <th>Nom</th>
                    <th>E-mail</th>
                    <th>Téléphone</th>
                    <th>Zone de service</th>
                    <th>Expérience</th>
                    <th>Inscrit le</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($enAttente as $u) { ?>
                    <tr>
                        <td><a href="index.php?page=details&amp;id=<?= (int)$u['id_user'] ?>"><?= e($u['prenom'] . ' ' . $u['nom']) ?></a></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['telephone']) ?></td>
                        <td><?= e($u['zone_service'] ?? '') ?></td>
                        <td><?= e($u['experience'] ?? '') ?> ans</td>
                        <td><?= e($u['date_creation'] ?? '') ?></td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="index.php?page=admin">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-approve">Approuver</button>
                                </form>
                                <form method="POST" action="index.php?page=admin"
                                      onsubmit="return confirm('Refuser et supprimer définitivement ce compte ?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn-reject">Refuser</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>

</div>
