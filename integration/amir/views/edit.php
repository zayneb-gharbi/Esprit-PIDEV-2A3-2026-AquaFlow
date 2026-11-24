<h1>Modifier mon profil</h1>
<form method="post" action="index.php?page=edit" class="card form-card">
<?php foreach (['nom'=>'Nom','prenom'=>'Prénom','email'=>'Email','telephone'=>'Téléphone','adresse'=>'Adresse'] as $key=>$label) {
    if ($key==='prenom' && integrationMode()!=='groupe') { continue; }
    $value=$currentUser[$key] ?? '';
    if ($key==='nom' && integrationMode()==='groupe') { $value=trim(substr($currentUser['nom'],strlen($currentUser['prenom'] ?? ''))); }
?>
<label><?= h($label) ?><input name="<?= h($key) ?>" value="<?= h($value) ?>" <?= in_array($key,['nom','email'],true)?'required':'' ?> type="<?= $key==='email'?'email':'text' ?>"></label>
<?php } ?><button class="btn" type="submit">Enregistrer</button></form>
