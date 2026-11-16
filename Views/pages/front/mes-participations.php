<?php
$title = 'Mes participations | AquaFlow';
$currentPage = 'evenements';
$breadcrumbParent = 'Espace citoyen';
$breadcrumbCurrent = 'Mes participations';
$base = '../..';
require_once __DIR__ . '/../../includes/head.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<main class="main">
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>
  <div class="main-inner">
    <div class="page-header">
      <div>
        <div class="eyebrow">ÉVÉNEMENTS</div>
        <h1>Mes participations</h1>
        <p class="subtitle">Retrouvez les événements auxquels vous êtes inscrit(e).</p>
      </div>
    </div>

    <div class="card section-gap">
      <div class="tabs">
        <span class="tab active">À venir (2)</span>
        <span class="tab">Passés (1)</span>
        <span class="tab">Annulés (0)</span>
      </div>

      <div class="list-row">
        <div class="tile">📅</div>
        <div style="flex:1">
          <div class="cell-main">Journée de l'eau</div>
          <div class="cell-sub">22/03/2027 · 09:00 • Parc du Belvédère, Tunis</div>
          <div class="cell-sub">Inscrit le 02/03/2027</div>
        </div>
        <span class="pill pill-inscrit">Inscrit</span>
        <button class="btn btn-outline">Se désinscrire</button>
      </div>

      <div class="list-row">
        <div class="tile">📅</div>
        <div style="flex:1">
          <div class="cell-main">Nettoyage des plages</div>
          <div class="cell-sub">25/03/2027 · 08:30 • Plage de Nabeul, Nabeul</div>
          <div class="cell-sub">Inscrit le 05/03/2027</div>
        </div>
        <span class="pill pill-inscrit">Inscrit</span>
        <button class="btn btn-outline">Se désinscrire</button>
      </div>

      <div class="list-row">
        <div class="tile">📅</div>
        <div style="flex:1">
          <div class="cell-main">Sensibilisation dans les écoles</div>
          <div class="cell-sub">10/11/2026 · 09:00 • École primaire, Tunis</div>
          <div class="cell-sub">Inscrit le 20/10/2026</div>
        </div>
        <span class="pill pill-present">Présent</span>
        <a class="btn btn-outline" href="evenement-details.php">Voir</a>
      </div>
    </div>

    <div class="card section-gap">
      <div class="card-header"><div class="card-title">État vide (variante)</div></div>
      <div class="empty-state">
        <div class="tile">📅</div>
        <div class="empty-title">Vous n'êtes inscrit(e) à aucun événement.</div>
        <p class="muted" style="margin-bottom:14px;">Découvrez les prochaines actions pour préserver l'eau de notre ville.</p>
        <a class="btn btn-primary" href="evenements.php">Découvrir les événements</a>
      </div>
    </div>

    <div class="modal-box section-gap">
      <div class="modal-title">Se désinscrire de cet événement ?</div>
      <p class="muted">Vous ne serez plus inscrit(e) à « Journée de l'eau ». Vous pourrez vous réinscrire tant que des places sont disponibles.</p>
      <div class="modal-actions">
        <button class="btn btn-outline">Retour</button>
        <button class="btn btn-primary">Confirmer</button>
      </div>
    </div>

    <footer class="footer-note">AquaFlow · Une ville attentive à chaque goutte d'eau</footer>
  </div>
</main>
<?php require_once __DIR__ . '/../../includes/scripts.php'; ?>