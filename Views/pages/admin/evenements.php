<?php
$title = 'Gestion des événements | AquaFlow';
$currentPage = 'admin-evenements';
$base = '../..';
require_once __DIR__ . '/../../partials/head.php';
require_once __DIR__ . '/../../partials/sidebar.php';
?>
<main class="main">
  <div class="main-inner">
    <div class="page-header">
      <div>
        <div class="eyebrow">ÉVÉNEMENTS</div>
        <h1>Gestion des événements</h1>
        <p class="subtitle">Organisez les campagnes de sensibilisation et suivez les inscriptions.</p>
      </div>
      <div class="header-actions">
        <button class="btn btn-outline">Exporter</button>
        <a class="btn btn-primary" href="evenement-form.php">+ Nouvel événement</a>
      </div>
    </div>

    <div class="grid-4 section-gap">
      <div class="card"><div class="kpi-label">Total événements</div><div class="kpi-value">6</div><div class="kpi-delta">3 à venir</div></div>
      <div class="card"><div class="kpi-label">Événements à venir</div><div class="kpi-value">3</div><div class="kpi-delta">Prochain : 22/03/2027</div></div>
      <div class="card"><div class="kpi-label">Participants inscrits</div><div class="kpi-value">157</div><div class="kpi-delta">aux événements à venir</div></div>
      <div class="card"><div class="kpi-label">Taux de remplissage</div><div class="kpi-value">80 %</div><div class="kpi-delta warning">1 événement complet</div></div>
    </div>

    <section class="card section-gap">
      <div class="card-header">
        <div><div class="card-title">Événements</div><div class="card-subtitle">Liste, filtres et gestion des inscriptions</div></div>
      </div>
      <div class="tabs">
        <span class="tab active">Tous (6)</span>
        <span class="tab">À venir (3)</span>
        <span class="tab">En cours (1)</span>
        <span class="tab">Terminés (1)</span>
        <span class="tab">Annulés (1)</span>
      </div>
      <div class="toolbar">
        <input class="input" type="text" placeholder="Rechercher un événement…" style="flex:1; min-width:220px;" />
        <select class="select"><option>Ville</option><option>Tunis</option><option>Ariana</option><option>Nabeul</option></select>
        <select class="select"><option>Catégorie</option><option>Sensibilisation</option><option>Atelier</option><option>Nettoyage</option><option>Conférence</option></select>
        <input class="input" type="text" placeholder="Date" />
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Événement</th>
              <th>Date &amp; heure</th>
              <th>Lieu</th>
              <th>Catégorie</th>
              <th>Participants</th>
              <th>Statut</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                <div class="event-cell">
                  <div class="tile tile-sm">📅</div>
                  <div>
                    <div class="cell-main">Journée de l'eau <span style="font-size:11px;color:var(--ink-400);">▾</span></div>
                    <div class="cell-sub">SONEDDE</div>
                  </div>
                </div>
                <div style="margin:10px 0 0 44px; padding:10px 12px; background:var(--bg-page); border:1px solid var(--border-soft); border-radius:10px;">
                  <div style="font-size:11.5px; font-weight:700; color:var(--ink-900); margin-bottom:6px;">Participants inscrits (aperçu)</div>
                  <div class="cell-sub">Ahmed Trabelsi · Inscrit le 02/11/2026</div>
                  <div class="cell-sub">Mariem Gharbi · Inscrit le 03/11/2026</div>
                  <div class="cell-sub">Sami Jlassi · Inscrit le 04/11/2026</div>
                  <a class="link-arrow teal" href="evenement-details.php" style="display:inline-block; margin-top:6px;">Voir les 85 participants →</a>
                </div>
              </td>
              <td>22/03/2027 · 09:00</td>
              <td>Parc du Belvédère<div class="cell-sub">Tunis</div></td>
              <td>Sensibilisation</td>
              <td><div>85 / 120</div><div class="progress" style="margin-top:6px;"><div class="progress-bar" style="width:71%"></div></div></td>
              <td><span class="pill pill-a-venir">À venir</span></td>
              <td><div class="actions"><a class="icon-action" href="evenement-details.php" aria-label="Voir">👁</a><a class="icon-action" href="evenement-form.php?mode=edit" aria-label="Modifier">✏</a><button class="icon-action" aria-label="Supprimer">🗑</button></div></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="tile tile-sm">📅</div><div><div class="cell-main">Nettoyage des plages</div><div class="cell-sub">Association partenaire</div></div></div></td>
              <td>25/03/2027 · 08:30</td>
              <td>Plage de Nabeul<div class="cell-sub">Nabeul</div></td>
              <td>Nettoyage</td>
              <td><div>42 / 60</div><div class="progress" style="margin-top:6px;"><div class="progress-bar" style="width:70%"></div></div></td>
              <td><span class="pill pill-a-venir">À venir</span></td>
              <td><div class="actions"><a class="icon-action" href="evenement-details.php" aria-label="Voir">👁</a><a class="icon-action" href="evenement-form.php?mode=edit" aria-label="Modifier">✏</a><button class="icon-action" aria-label="Supprimer">🗑</button></div></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="tile tile-sm">📅</div><div><div class="cell-main">Atelier économie d'eau</div><div class="cell-sub">Administration AquaFlow</div></div></div></td>
              <td>02/04/2027 · 14:00</td>
              <td>Salle municipale<div class="cell-sub">Ariana</div></td>
              <td>Atelier</td>
              <td><div>30 / 30</div><div class="progress" style="margin-top:6px;"><div class="progress-bar full" style="width:100%"></div></div></td>
              <td><span class="pill pill-complet">Complet</span> <span class="pill pill-a-venir">À venir</span></td>
              <td><div class="actions"><a class="icon-action" href="evenement-details.php" aria-label="Voir">👁</a><a class="icon-action" href="evenement-form.php?mode=edit" aria-label="Modifier">✏</a><button class="icon-action" aria-label="Supprimer">🗑</button></div></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="tile tile-sm">📅</div><div><div class="cell-main">Sensibilisation dans les écoles</div><div class="cell-sub">Partenaire municipal</div></div></div></td>
              <td>10/11/2026 · 09:00</td>
              <td>École primaire<div class="cell-sub">Tunis</div></td>
              <td>Sensibilisation</td>
              <td><div>48 / 50</div><div class="progress" style="margin-top:6px;"><div class="progress-bar" style="width:96%"></div></div></td>
              <td><span class="pill pill-termine">Terminé</span></td>
              <td><div class="actions"><a class="icon-action" href="evenement-details.php" aria-label="Voir">👁</a><a class="icon-action" href="evenement-form.php?mode=edit" aria-label="Modifier">✏</a><button class="icon-action" aria-label="Supprimer">🗑</button></div></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="tile tile-sm">📅</div><div><div class="cell-main">Randonnée des sources</div><div class="cell-sub">Association partenaire</div></div></div></td>
              <td>05/12/2026 · 09:30</td>
              <td>Zaghouan<div class="cell-sub">Zaghouan</div></td>
              <td>Nettoyage</td>
              <td><div>12 / 40</div><div class="progress" style="margin-top:6px;"><div class="progress-bar" style="width:30%"></div></div></td>
              <td><span class="pill pill-annule">Annulé</span></td>
              <td><div class="actions"><a class="icon-action" href="evenement-details.php" aria-label="Voir">👁</a><a class="icon-action" href="evenement-form.php?mode=edit" aria-label="Modifier">✏</a><button class="icon-action" aria-label="Supprimer">🗑</button></div></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="tile tile-sm">📅</div><div><div class="cell-main">Atelier détection de fuites</div><div class="cell-sub">Administration AquaFlow</div></div></div></td>
              <td>16/11/2026 · 10:00</td>
              <td>Centre de formation<div class="cell-sub">Tunis</div></td>
              <td>Atelier</td>
              <td><div>18 / 25</div><div class="progress" style="margin-top:6px;"><div class="progress-bar" style="width:72%"></div></div></td>
              <td><span class="pill pill-en-cours">En cours</span></td>
              <td><div class="actions"><a class="icon-action" href="evenement-details.php" aria-label="Voir">👁</a><a class="icon-action" href="evenement-form.php?mode=edit" aria-label="Modifier">✏</a><button class="icon-action" aria-label="Supprimer">🗑</button></div></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer"><span>1–6 sur 6 événements</span><span>Pagination : 1</span></div>
    </section>

    <div class="grid-2 section-gap">
      <div class="modal-box">
        <div class="modal-title">Supprimer cet événement ?</div>
        <p class="muted">L'événement « Journée de l'eau » et ses 85 inscriptions seront définitivement supprimés. Cette action est irréversible.</p>
        <div class="modal-actions">
          <button class="btn btn-outline">Annuler</button>
          <button class="btn btn-danger">Supprimer</button>
        </div>
      </div>
      <div class="modal-box">
        <div class="modal-title">Annuler cet événement ?</div>
        <p class="muted" style="margin-bottom:10px;">Les participants seront informés de l'annulation.</p>
        <label class="label" for="motif">Motif de l'annulation (visible par les participants)</label>
        <textarea id="motif" class="textarea" placeholder="Saisissez le motif…"></textarea>
        <div class="modal-actions">
          <button class="btn btn-outline">Retour</button>
          <button class="btn btn-primary">Confirmer l'annulation</button>
        </div>
      </div>
    </div>

    <?php require_once __DIR__ . '/../../partials/footer.php'; ?>
  </div>
</main>
<?php require_once __DIR__ . '/../../partials/scripts.php'; ?>