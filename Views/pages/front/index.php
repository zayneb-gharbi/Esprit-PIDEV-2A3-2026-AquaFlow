<?php
$title = 'Accueil | AquaFlow';
$currentPage = 'accueil';
$breadcrumbParent = 'Espace citoyen';
$breadcrumbCurrent = 'Accueil';
$base = '../..';
require_once __DIR__ . '/../../includes/head.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>
<main class="main">
  <?php require_once __DIR__ . '/../../includes/topbar.php'; ?>
  <div class="main-inner">
    <div class="page-header">
      <div>
        <div class="eyebrow">MARDI 10 NOVEMBRE 2026</div>
        <h1>Bonjour, isra 👋</h1>
        <p class="subtitle">Ensemble, prenons soin de l'eau de notre ville.</p>
      </div>
      <div class="location-pill">📍 Tunis, Tunisie</div>
    </div>

    <section class="hero section-gap">
      <div class="hero-content">
        <h2>Vous avez repéré une fuite ?<br>Agissons ensemble.</h2>
        <p>Signalez rapidement un problème d'eau pour aider votre quartier et suivre la prise en charge en temps réel.</p>
        <a class="btn btn-primary" href="#">Signaler une fuite →</a>
      </div>
      <div class="hero-illus"><div class="drop-shape"></div></div>
    </section>

    <div class="grid-2 section-gap">
      <section class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Mes signalements récents</div>
            <div class="card-subtitle">Suivez l'avancement de vos demandes</div>
          </div>
          <a class="link-arrow" href="#">Tout voir →</a>
        </div>
        <div class="list-row">
          <div class="tile">✦</div>
          <div style="flex:1">
            <div class="cell-main">Fuite sur trottoir</div>
            <div class="cell-sub">Rue Habib Bourguiba · Signalée le 8 nov.</div>
          </div>
          <span class="pill pill-en-cours">En cours</span>
        </div>
        <div class="list-row">
          <div class="tile">✦</div>
          <div style="flex:1">
            <div class="cell-main">Canalisation endommagée</div>
            <div class="cell-sub">Lafayette · Signalée le 2 nov.</div>
          </div>
          <span class="pill pill-termine">Résolue</span>
        </div>
      </section>

      <section class="card">
        <div class="card-header">
          <div>
            <div class="card-title">Prochain événement</div>
            <div class="card-subtitle">Participez aux actions pour préserver l'eau</div>
          </div>
          <a class="link-arrow teal" href="evenements.php">Voir tout →</a>
        </div>
        <div style="display:flex; gap:14px; align-items:flex-start;">
          <div class="tile">📅</div>
          <div style="flex:1">
            <div class="cell-main">Journée de l'eau</div>
            <div class="cell-sub">22/03/2027 · 09:00 • 4 h</div>
            <div class="cell-sub" style="margin-top:4px;">📍 Parc du Belvédère, Tunis</div>
            <div style="margin-top:10px;">
              <div class="progress"><div class="progress-bar" style="width:71%"></div></div>
              <div class="progress-meta">85 sur 120 places · 35 places restantes</div>
            </div>
            <div style="margin-top:12px;"><a class="btn btn-primary" href="evenement-details.php">S'inscrire →</a></div>
          </div>
        </div>
      </section>
    </div>

    <div class="grid-3">
      <div class="card">
        <div style="display:flex; gap:14px;">
          <div class="tile">✦</div>
          <div>
            <div class="card-title" style="font-size:13.5px;">Signaler une fuite</div>
            <div class="card-subtitle">Localisez le problème en quelques étapes</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div style="display:flex; gap:14px;">
          <div class="tile">◷</div>
          <div>
            <div class="card-title" style="font-size:13.5px;">Suivre une réparation</div>
            <div class="card-subtitle">Consultez son statut en temps réel</div>
          </div>
        </div>
      </div>
      <div class="card">
        <div style="display:flex; gap:14px;">
          <div class="tile">★</div>
          <div>
            <div class="card-title" style="font-size:13.5px;">Mes points : 120</div>
            <div class="card-subtitle">Voir mes récompenses</div>
          </div>
        </div>
      </div>
    </div>

    <footer class="footer-note">AquaFlow · Une ville attentive à chaque goutte d'eau</footer>
  </div>
</main>
<?php require_once __DIR__ . '/../../includes/scripts.php'; ?>