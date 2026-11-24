<h1>Utilisateurs</h1>
<table class="table"><thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Action</th></tr></thead><tbody>
<?php foreach ($users as $person) { ?>
<tr><td><?= h($person['nom']) ?></td><td><?= h($person['email']) ?></td><td><?= h($person['nom_role']) ?></td><td><a href="index.php?page=details&amp;id=<?= (int)$person['id'] ?>">Voir les détails</a></td></tr>
<?php } ?></tbody></table>
