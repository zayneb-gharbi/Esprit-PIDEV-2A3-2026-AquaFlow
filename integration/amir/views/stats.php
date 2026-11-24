<h1>Statistiques des utilisateurs</h1>
<table class="table"><tbody>
<?php foreach (['users'=>'Tous les utilisateurs','citizens'=>'Citoyens','plumbers'=>'Plombiers','active'=>'Comptes actifs'] as $key=>$label) { ?>
<tr><th><?= h($label) ?></th><td><?= (int)$stats[$key] ?></td></tr>
<?php } ?></tbody></table>
