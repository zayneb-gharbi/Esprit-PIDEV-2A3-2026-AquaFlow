<?php
$title = 'Atelier détection de fuites | AquaFlow';
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
        <h1>Atelier détection de fuites <span class="pill pill-en-cours" style="vertical-align:middle; margin-left:8px;">En cours</span></h1>
        <p class="subtitle">16/11/2026 · 10:00 • Centre de formation, Tunis</p>
      </div>
      <div class="header-actions">
        <a class="btn btn-outline" href="evenement-form.php?mode=edit">Modifier</a>
        <button class="btn btn-outline">Annuler l'événement</button>
        <button class="btn btn-danger">Supprimer</button>
      </div>
    </div>

    <div class="grid-2 section-gap" style="grid-template-columns: 1.4fr 1fr; align-items:start;">
      <section class="card">
        <div class="card-header"><div class="card-title">Informations</div></div>
        <p style="margin-bottom:16px; line-height:1.7;">Atelier pratique pour apprendre à repérer les fuites domestiques, lire un compteur et signaler rapidement les anomalies afin de préserver l'eau.</p>
        <div class="grid-2" style="gap:14px;">
          <div><div class="label">Date &amp; heure</div><div>16/11/2026 · 10:00</div></div>
          <div><div class="label">Durée</div><div>2 h</div></div>
          <div><div class="label">Lieu</div><div>Centre de formation, Tunis</div></div>
          <div><div class="label">Catégorie</div><div>Atelier</div></div>
          <div><div class="label">Organisateur</div><div>Administration AquaFlow</div></div>
          <div><div class="label">Créé le</div><div>01/11/2026</div></div>
        </div>
      </section>

      <section class="card">
        <div class="card-header"><div class="card-title">Remplissage</div></div>
        <div style="font-size:32px; font-weight:800; color:var(--ink-900); line-height:1; margin-bottom:10px;">18 / 25</div>
        <div class="progress" style="margin-bottom:8px;"><div class="progress-bar" style="width:72%"></div></div>
        <div class="progress-meta" style="margin-bottom:14px;">7 places restantes</div>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <span class="chip">Présents 12</span>
          <span class="chip">Absents 2</span>
          <span class="chip">À pointer 4</span>
        </div>
      </section>
    </div>

    <section class="card section-gap">
      <div class="card-header">
        <div>
          <div class="card-title">Participants inscrits <span class="chip" style="margin-left:8px;">18 / 25 inscrits</span></div>
          <div class="card-subtitle">JOIN : Événement → Participation → Utilisateur</div>
        </div>
        <div class="header-actions">
          <input class="input" type="text" placeholder="Rechercher un participant…" style="height:40px; min-width:220px;" />
          <button class="btn btn-outline">Exporter la liste</button>
        </div>
      </div>

      <div class="table-wrap">
        <table>
          <thead>
            <tr>
              <th>Citoyen</th>
              <th>E-mail</th>
              <th>Date d'inscription</th>
              <th>Présence</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><div class="event-cell"><div class="avatar">AT</div><div class="cell-main">Ahmed Trabelsi</div></div></td>
              <td>ahmed@example.com</td>
              <td>02/11/2026</td>
              <td><div class="seg"><span class="active">Présent</span><span>Absent</span></div></td>
              <td><button class="btn btn-sm" style="color:var(--danger); border-color:rgba(214,69,69,.3); background:#fff;">Retirer</button></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="avatar">MG</div><div class="cell-main">Mariem Gharbi</div></div></td>
              <td>mariem@example.com</td>
              <td>03/11/2026</td>
              <td><div class="seg"><span class="active">Présent</span><span>Absent</span></div></td>
              <td><button class="btn btn-sm" style="color:var(--danger); border-color:rgba(214,69,69,.3); background:#fff;">Retirer</button></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="avatar">SJ</div><div class="cell-main">Sami Jlassi</div></div></td>
              <td>sami@example.com</td>
              <td>04/11/2026</td>
              <td><div class="seg"><span>Présent</span><span class="active" style="background:var(--st-pending-bg); color:var(--st-pending-fg);">Absent</span></div></td>
              <td><button class="btn btn-sm" style="color:var(--danger); border-color:rgba(214,69,69,.3); background:#fff;">Retirer</button></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="avatar">SA</div><div class="cell-main">Salma Amri</div></div></td>
              <td>salma@example.com</td>
              <td>06/11/2026</td>
              <td><div class="seg"><span class="active">Présent</span><span>Absent</span></div></td>
              <td><button class="btn btn-sm" style="color:var(--danger); border-color:rgba(214,69,69,.3); background:#fff;">Retirer</button></td>
            </tr>
            <tr>
              <td><div class="event-cell"><div class="avatar">NH</div><div class="cell-main">Nour Hamdi</div></div></td>
              <td>nour@example.com</td>
              <td>09/11/2026</td>
              <td><div class="seg"><span class="disabled" title="Disponible le jour de l'événement">Présent</span><span class="disabled">Absent</span></div> <span class="cell-sub" style="margin-left:6px;">À pointer</span></td>
              <td><button class="btn btn-sm" style="color:var(--danger); border-color:rgba(214,69,69,.3); background:#fff;">Retirer</button></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="table-footer">
        <span>1–5 sur 18</span>
        <strong style="color:var(--ink-700);">Total : 18 participants · 7 places restantes</strong>
      </div>
      <div class="help" style="margin-top:8px;">Lorsque l'événement est À venir, le marquage de présence est désactivé : « Disponible le jour de l'événement ».</div>
    </section>

    <?php require_once __DIR__ . '/../../partials/footer.php'; ?>
  </div>
</main>
<?php require_once __DIR__ . '/../../partials/scripts.php'; ?>