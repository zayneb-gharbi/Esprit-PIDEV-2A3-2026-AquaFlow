<?php
// Views/pages/admin.php : comptes plombiers en attente (HTML pur)
// variables : $admin, $enAttente, $flash
?>
<div class="card">

    <h1>Pending plumber accounts</h1>
    <p class="sub">
        Logged in as <?= e($admin['prenom']) ?> <?= e($admin['nom']) ?> (administrator)
    </p>

    <?php if (!empty($flash)) { ?>
        <div class="message <?= e($flash['type']) ?>"><?= e($flash['text']) ?></div>
    <?php } ?>

    <?php if (empty($enAttente)) { ?>
        <div class="empty">No account is waiting for approval.</div>
    <?php } else { ?>
        <div class="table-wrap">
            <table class="list">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Service area</th>
                    <th>Experience</th>
                    <th>Registered</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($enAttente as $u) { ?>
                    <tr>
                        <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                        <td><?= e($u['email']) ?></td>
                        <td><?= e($u['telephone']) ?></td>
                        <td><?= e($u['zone_service'] ?? '') ?></td>
                        <td><?= e($u['experience'] ?? '') ?> years</td>
                        <td><?= e($u['date_creation'] ?? '') ?></td>
                        <td>
                            <div class="actions">
                                <form method="POST" action="index.php?page=admin">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn-approve">Approve</button>
                                </form>
                                <form method="POST" action="index.php?page=admin"
                                      onsubmit="return confirm('Reject and permanently delete this account?');">
                                    <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
                                    <input type="hidden" name="id_user" value="<?= (int) $u['id_user'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn-reject">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>

</div>
