<?php
// Views/pages/stats.php : statistiques utilisateurs (HTML pur)
// variables : $stats
?>
<div class="card">

    <h1>Statistiques utilisateurs</h1>
    <p class="sub">Vue d'ensemble des comptes citoyens et plombiers sur Aqua Flow.</p>

    <div class="stats-grid">
        <div class="stat-card"><p>Total utilisateurs</p><h2><?= (int) $stats['total'] ?></h2></div>
        <div class="stat-card"><p>Citoyens</p><h2><?= (int) $stats['citoyens'] ?></h2></div>
        <div class="stat-card"><p>Plombiers</p><h2><?= (int) $stats['plombiers'] ?></h2></div>
        <div class="stat-card"><p>Comptes actifs</p><h2><?= (int) $stats['actifs'] ?></h2></div>
        <div class="stat-card"><p>Comptes inactifs</p><h2><?= (int) $stats['inactifs'] ?></h2></div>
    </div>

</div>
