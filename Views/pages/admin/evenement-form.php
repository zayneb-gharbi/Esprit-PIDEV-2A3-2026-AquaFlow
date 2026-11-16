<?php
$isEdit = isset($_GET['mode']) && $_GET['mode'] === 'edit';
$title = ($isEdit ? 'Modifier l\'événement' : 'Nouvel événement') . ' | AquaFlow';
$currentPage = 'admin-evenements';
$base = '../..';
require_once __DIR__ . '/../../partials/head.php';
require_once __DIR__ . '/../../partials/sidebar.php';
?>
<main class="main">
  <div class="main-inner">
    <a class="back-link" href="evenements.php">← Retour aux événements</a>
    <div class="page-header">
      <div>
        <div class="eyebrow">ÉVÉNEMENTS</div>
        <h1><?php echo $isEdit ? 'Modifier l\'événement' : 'Nouvel événement'; ?></h1>
        <p class="subtitle">Renseignez les informations de l'événement.</p>
      </div>
      <?php if ($isEdit): ?><span class="chip">Statut actuel : À venir</span><?php endif; ?>
    </div>

    <div class="grid-2 section-gap" style="grid-template-columns: 1.6fr 1fr; align-items:start;">
      <section class="card">
        <div class="card-header"><div class="card-title">Informations générales</div></div>
        <div class="field">
          <label class="label">Titre *</label>
          <input class="input" type="text" value="<?php echo $isEdit ? 'Journée de l\'eau' : ''; ?>" placeholder="Journée de l'eau" />
        </div>
        <div class="field">
          <label class="label">Description *</label>
          <textarea class="textarea" placeholder="Une journée de sensibilisation à la préservation de l'eau…"><?php echo $isEdit ? 'Une journée de sensibilisation pour apprendre les gestes simples qui préservent l\'eau.' : ''; ?></textarea>
        </div>
        <div class="grid-2">
          <div class="field">
            <label class="label">Catégorie *</label>
            <select class="select"><option>Sensibilisation</option><option>Atelier</option><option>Nettoyage</option><option>Conférence</option></select>
          </div>
          <div class="field">
            <label class="label">Organisateur *</label>
            <select class="select"><option>SONEDDE</option><option>Administration AquaFlow</option><option>Partenaire municipal</option><option>Association partenaire</option></select>
          </div>
        </div>
        <div class="grid-2">
          <div class="field">
            <label class="label">Date *</label>
            <input class="input" type="text" value="<?php echo $isEdit ? '22/03/2027' : ''; ?>" placeholder="JJ/MM/AAAA" />
          </div>
          <div class="field">
            <label class="label">Heure de début *</label>
            <input class="input" type="text" value="<?php echo $isEdit ? '09:00' : ''; ?>" placeholder="09:00" />
          </div>
        </div>
        <div class="grid-2">
          <div class="field">
            <label class="label">Durée (heures)</label>
            <input class="input" type="number" value="<?php echo $isEdit ? '4' : ''; ?>" placeholder="4" />
          </div>
          <div class="field">
            <label class="label">Ville *</label>
            <select class="select"><option>Tunis</option><option>Ariana</option><option>Nabeul</option></select>
          </div>
        </div>
        <div class="field">
          <label class="label">Lieu *</label>
          <input class="input <?php echo !$isEdit ? 'error' : ''; ?>" type="text" value="<?php echo $isEdit ? 'Parc du Belvédère' : ''; ?>" placeholder="Parc du Belvédère" />
          <?php if (!$isEdit): ?><div class="error-text">Ce champ est obligatoire</div><?php endif; ?>
        </div>
        <div class="field">
          <label class="label">Capacité *</label>
          <input class="input" type="number" value="<?php echo $isEdit ? '120' : ''; ?>" placeholder="120" />
          <div class="help"><?php echo $isEdit ? 'Minimum 85 : nombre d\'inscrits actuel' : 'Nombre maximum de participants.'; ?></div>
        </div>

        <div style="display:flex; gap:10px; margin-top:8px;">
          <button class="btn btn-primary"><?php echo $isEdit ? 'Enregistrer les modifications' : 'Enregistrer l\'événement'; ?></button>
          <a class="btn btn-outline" href="evenements.php">Annuler</a>
        </div>
      </section>

      <aside>
        <section class="card section-gap">
          <div class="card-header"><div class="card-title">Image</div></div>
          <div style="border:1px dashed var(--border); border-radius:14px; padding:24px; text-align:center; background:var(--bg-page);">
            <div class="tile" style="margin:0 auto 10px;">🖼</div>
            <div class="cell-main" style="margin-bottom:4px;">Déposez une image ici</div>
            <div class="cell-sub">ou cliquez pour sélectionner un fichier</div>
            <?php if ($isEdit): ?><div class="chip" style="margin-top:12px;">journee-eau.jpg</div><?php endif; ?>
          </div>
        </section>

        <section class="card">
          <div class="card-header"><div class="card-title">Organisation</div></div>
          <div class="field"><label class="label">Statut</label><div class="cell-sub"><?php echo $isEdit ? 'Modifiable depuis la liste ou la page de détails (Annuler l\'événement).' : 'L\'événement sera créé avec le statut À venir.'; ?></div></div>
          <div class="field" style="margin-bottom:0;"><label class="label">Rappel</label><div class="cell-sub">Vérifiez la capacité, la date et le lieu avant l'enregistrement. Un événement complet affiche automatiquement l'état « Complet » côté citoyen.</div></div>
        </section>
      </aside>
    </div>

    <?php require_once __DIR__ . '/../../partials/footer.php'; ?>
  </div>
</main>
<?php require_once __DIR__ . '/../../partials/scripts.php'; ?>