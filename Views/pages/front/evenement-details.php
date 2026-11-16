<?php
$title = 'Journée de l\'eau | AquaFlow';
$currentPage = 'evenements';
$breadcrumbParent = 'Espace citoyen';
$breadcrumbCurrent = 'Événements / Journée de l\'eau';
$base = '../..';
require_once __DIR__ . '/../../includes/head.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<main class="main">
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>
  <div class="main-inner">
    <a class="back-link" href="evenements.php">← Retour aux événements</a>

    <div class="grid-2 section-gap" style="grid-template-columns: 1.5fr 1fr; align-items:start;">
      <section class="card">
        <div class="event-cover" style="height:220px; border-radius:14px; margin:-4px -4px 18px;"><span class="pill pill-a-venir">À venir</span></div>
        <div class="eyebrow">ÉVÉNEMENTS</div>
        <h1 style="margin-bottom:6px;">Journée de l'eau</h1>
        <div class="event-org" style="margin-bottom:12px;">SONEDDE</div>
        <p style="margin-bottom:18px; color:var(--ink-700); line-height:1.7;">Une journée de sensibilisation pour apprendre les gestes simples qui préservent l'eau. Des ateliers, des démonstrations et des échanges avec des experts vous aideront à adopter des habitudes durables au quotidien.</p>
        <div class="grid-2" style="gap:14px;">
          <div><div class="label">Date</div><div>22/03/2027</div></div>
          <div><div class="label">Heure • Durée</div><div>09:00 • 4 h</div></div>
          <div><div class="label">Lieu</div><div>Parc du Belvédère, Tunis</div></div>
          <div><div class="label">Catégorie</div><div>Sensibilisation</div></div>
        </div>

        <div class="divider"></div>
        <div class="alert alert-success section-gap">Votre inscription est confirmée.</div>
        <div class="alert alert-info">Vous avez annulé votre participation.</div>
      </section>

      <aside>
        <section class="card section-gap">
          <div class="card-header"><div class="card-title">Inscription</div><span class="pill pill-a-venir">À venir</span></div>
          <div style="font-size:24px; font-weight:800; color:var(--ink-900); margin-bottom:8px;">85 sur 120 places</div>
          <div class="progress"><div class="progress-bar" style="width:71%"></div></div>
          <div class="progress-meta" style="margin-bottom:14px;">35 places restantes</div>
          <button class="btn btn-primary btn-full" style="margin-bottom:10px;">S'inscrire à cet événement</button>
          <div class="registered-note" style="margin-bottom:10px;">✓ Vous êtes inscrit(e)</div>
          <button class="btn btn-outline btn-full">Se désinscrire</button>
        </section>

        <section class="card">
          <div class="card-header"><div class="card-title">Autres événements à venir</div></div>
          <div class="list-row">
            <div class="tile">🌊</div>
            <div style="flex:1">
              <div class="cell-main">Nettoyage des plages</div>
              <div class="cell-sub">25/03/2027 · Nabeul</div>
            </div>
            <a class="btn btn-sm" href="evenement-details.php">Voir</a>
          </div>
          <div class="list-row">
            <div class="tile">💧</div>
            <div style="flex:1">
              <div class="cell-main">Atelier économie d'eau</div>
              <div class="cell-sub">02/04/2027 · Ariana · Complet</div>
            </div>
            <a class="btn btn-sm" href="evenement-details.php">Voir</a>
          </div>
        </section>
      </aside>
    </div>

    <footer class="footer-note">AquaFlow · Une ville attentive à chaque goutte d'eau</footer>
  </div>
</main>
<?php require_once __DIR__ . '/../../includes/scripts.php'; ?>