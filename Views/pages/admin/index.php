<?php
$title = 'Tableau de bord | AquaFlow';
$currentPage = 'admin-dashboard';
$base = '../..';
require_once __DIR__ . '/../../partials/head.php';
require_once __DIR__ . '/../../partials/sidebar.php';
?>
<main class="main">
  <div class="main-inner">
    <div class="page-header">
      <div>
        <div class="eyebrow">VUE D'ENSEMBLE</div>
        <h1>Tableau de bord</h1>
        <p class="subtitle">Pilotez les signalements, les interventions et les actions de sensibilisation.</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-outline">Exporter</button>
        <button class="btn btn-primary">+ Nouveau signalement</button>
      </div>
    </div>

    <div class="grid-4 section-gap">
      <div class="card"><div class="kpi-label">Signalements reçus</div><div class="kpi-value">128</div><div class="kpi-delta positive">↑ 12 % ce mois-ci</div></div>
      <div class="card"><div class="kpi-label">À traiter</div><div class="kpi-value">24</div><div class="kpi-delta warning">8 en attente depuis plus de 24 h</div></div>
      <div class="card"><div class="kpi-label">Interventions en cours</div><div class="kpi-value">17</div><div class="kpi-delta">11 plombiers disponibles</div></div>
      <div class="card"><div class="kpi-label">Fuites résolues</div><div class="kpi-value">86</div><div class="kpi-delta positive">↑ 9 % vs mois dernier</div></div>
    </div>

    <div class="grid-2 section-gap">
      <section class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Signalements récents</div>
            <div class="card-subtitle">Dernières demandes citoyennes</div>
          </div>
          <a class="link-arrow" href="#">Tout voir →</a>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>Référence</th><th>Emplacement</th><th>Date</th><th>Statut</th></tr></thead>
            <tbody>
              <tr><td class="cell-main">#NV-0248</td><td>Rue Habib Bourguiba<div class="cell-sub">Tunis Centre</div></td><td>10 nov. · 09:42</td><td><span class="pill pill-en-cours">En cours</span></td></tr>
              <tr><td class="cell-main">#NV-0247</td><td>Avenue de la Liberté<div class="cell-sub">Lafayette</div></td><td>10 nov. · 08:15</td><td><span class="pill pill-a-venir" style="background:var(--st-pending-bg);color:var(--st-pending-fg);">À traiter</span></td></tr>
              <tr><td class="cell-main">#NV-0246</td><td>Rue de Marseille<div class="cell-sub">Mutuelleville</div></td><td>9 nov. · 16:30</td><td><span class="pill pill-termine">Résolu</span></td></tr>
            </tbody>
          </table>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Gestion des événements</div>
            <div class="card-subtitle">Campagnes de sensibilisation et inscriptions</div>
          </div>
          <a class="link-arrow teal" href="evenements.php">Gérer →</a>
        </div>
        <div class="list-row"><div class="tile">📅</div><div style="flex:1"><div class="cell-main">3 événements à venir</div><div class="cell-sub">Prochain : Journée de l'eau · 22/03/2027</div></div></div>
        <div class="list-row"><div class="tile">👥</div><div style="flex:1"><div class="cell-main">157 participants inscrits</div><div class="cell-sub">Aux événements à venir</div></div></div>
        <div class="list-row"><div class="tile">%</div><div style="flex:1"><div class="cell-main">Taux de remplissage 80 %</div><div class="cell-sub" style="color:var(--warning); font-weight:600;">1 événement complet</div></div></div>
        <div style="margin-top:14px;"><a class="btn btn-primary" href="evenements.php">Gérer les événements — 3 à venir →</a></div>
      </section>
    </div>

    <?php require_once __DIR__ . '/../../partials/footer.php'; ?>
  </div>
</main>
<?php require_once __DIR__ . '/../../partials/scripts.php'; ?>