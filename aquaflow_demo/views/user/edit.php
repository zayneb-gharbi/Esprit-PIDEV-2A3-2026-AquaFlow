<section class="page-title"><p class="eyebrow">ESPACE CITOYEN</p><h1>Modifier mes informations</h1><p>Gardez vos informations personnelles à jour.</p></section>
<div class="card form-card">
    <?php if ($message != '') { ?><div class="notice"><?php echo htmlspecialchars($message); ?></div><?php } ?>
    <form method="POST" action="index.php?page=edit">
        <label>Nom complet</label><input type="text" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
        <label>Email</label><input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        <label>Téléphone</label><input type="text" name="telephone" value="<?php echo htmlspecialchars($user['telephone'] ?? ''); ?>">
        <label>Adresse</label><input type="text" name="adresse" value="<?php echo htmlspecialchars($user['adresse'] ?? ''); ?>">
        <div class="form-actions"><a class="btn-light" href="index.php?page=profile">Annuler</a><button class="btn" type="submit">Enregistrer</button></div>
    </form>
</div>
