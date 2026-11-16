<?php
$title = 'Événements et campagnes | AquaFlow';
$currentPage = 'evenements';
$breadcrumbParent = 'Espace citoyen';
$breadcrumbCurrent = 'Événements';
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
        <h1>Événements et campagnes</h1>
        <p class="subtitle">Découvrez et participez aux actions pour préserver l'eau de notre ville.</p>
      </div>
      <div class="location-pill">📍 Tunis, Tunisie</div>
    </div>

    <div class="card section-gap">
      <div class="toolbar">
        <input class="input" type="text" placeholder="Rechercher un événement…" style="flex:1; min-width:240px;" />
        <select class="select"><option>Ville</option><option>Tunis</option><option>Ariana</option><option>Nabeul</option></select>
        <select class="select"><option>Catégorie</option><option>Sensibilisation</option><option>Atelier</option><option>Nettoyage</option><option>Conférence</option></select>
        <input class="input" type="text" placeholder="Date" />
      </div>
      <div class="tabs">
        <span class="tab active">À venir (3)</span>
        <a class="tab" href="mes-participations.php">Mes inscriptions (2)</a>
        <span class="tab">Passés (1)</span>
      </div>
    </div>

    <div class="grid-3">
      <!-- E01 -->
      <article class="card event-card">
        <div class="event-cover"><span class="pill pill-a-venir">À venir</span></div>
        <div class="event-body">
          <div class="event-title">Journée de l'eau</div>
          <div class="event-org">SONEDDE</div>
          <p class="event-desc">Une journée de sensibilisation pour apprendre les gestes simples qui préservent l'eau.</p>
          <div class="event-meta">
            <span>📅 22/03/2027</span>
            <span>🕘 09:00 • 4 h</span>
            <span>📍 Parc du Belvédère, Tunis</span>
            <span>👥 85 sur 120 places</span>
          </div>
          <div>
            <div class="progress"><div class="progress-bar" style="width:71%"></div></div>
            <div class="progress-meta">35 places restantes</div>
          </div>
          <span class="tag">Sensibilisation</span>
          <div class="registered-note">✓ Inscrit</div>
          <div class="event-actions">
            <a class="btn btn-outline" href="evenement-details.php">Voir les détails</a>
            <button class="btn btn-outline">Se désinscrire</button>
          </div>
        </div>
      </article>

      <!-- E02 -->
      <article class="card event-card">
        <div class="event-cover alt1"><span class="pill pill-a-venir">À venir</span></div>
        <div class="event-body">
          <div class="event-title">Nettoyage des plages</div>
          <div class="event-org">Association partenaire</div>
          <p class="event-desc">Rejoignez-nous pour nettoyer le littoral et protéger les ressources en eau.</p>
          <div class="event-meta">
            <span>📅 25/03/2027</span>
            <span>🕘 08:30 • 3 h</span>
            <span>📍 Plage de Nabeul, Nabeul</span>
            <span>👥 42 sur 60 places</span>
          </div>
          <div>
            <div class="progress"><div class="progress-bar" style="width:70%"></div></div>
            <div class="progress-meta">18 places restantes</div>
          </div>
          <span class="tag">Nettoyage</span>
          <div class="event-actions">
            <a class="btn btn-outline" href="evenement-details.php">Voir les détails</a>
            <button class="btn btn-primary">S'inscrire</button>
          </div>
        </div>
      </article>

      <!-- E03 -->
      <article class="card event-card">
        <div class="event-cover alt2"><span class="pill pill-complet">Complet</span></div>
        <div class="event-body">
          <div class="event-title">Atelier économie d'eau</div>
          <div class="event-org">Administration AquaFlow</div>
          <p class="event-desc">Un atelier pratique pour réduire sa consommation d'eau au quotidien.</p>
          <div class="event-meta">
            <span>📅 02/04/2027</span>
            <span>🕘 14:00 • 2 h</span>
            <span>📍 Salle municipale, Ariana</span>
            <span>👥 30 sur 30 places</span>
          </div>
          <div>
            <div class="progress"><div class="progress-bar full" style="width:100%"></div></div>
            <div class="progress-meta">Cet événement est complet.</div>
          </div>
          <span class="tag">Atelier</span>
          <div class="event-actions">
            <a class="btn btn-outline" href="evenement-details.php">Voir les détails</a>
            <button class="btn btn-outline" disabled>Complet</button>
          </div>
        </div>
      </article>
    </div>

    <div style="margin-top:20px;" class="card">
      <div class="card-header"><div class="card-title">Événements passés et autres états</div></div>
      <div class="grid-3">
        <article class="card event-card" style="box-shadow:none;">
          <div class="event-cover" style="height:110px; filter:saturate(.7);"><span class="pill pill-termine">Terminé</span></div>
          <div class="event-body">
            <div class="event-title">Sensibilisation dans les écoles</div>
            <div class="event-org">Partenaire municipal</div>
            <div class="event-meta"><span>📅 10/11/2026 · 09:00</span><span>📍 École primaire, Tunis</span></div>
            <span class="tag">Sensibilisation</span>
          </div>
        </article>
        <article class="card event-card" style="box-shadow:none;">
          <div class="event-cover alt1" style="height:110px; filter:grayscale(.3);"><span class="pill pill-annule">Annulé</span></div>
          <div class="event-body">
            <div class="event-title">Randonnée des sources</div>
            <div class="event-org">Association partenaire</div>
            <div class="event-meta"><span>📅 05/12/2026 · 09:30</span><span>📍 Zaghouan</span></div>
            <span class="tag">Nettoyage</span>
            <button class="btn btn-outline" disabled style="margin-top:8px;">Annulé</button>
          </div>
        </article>
      </div>
    </div>

    <footer class="footer-note">AquaFlow · Une ville attentive à chaque goutte d'eau</footer>
  </div>
</main>
<?php require_once __DIR__ . '/../../includes/scripts.php'; ?>