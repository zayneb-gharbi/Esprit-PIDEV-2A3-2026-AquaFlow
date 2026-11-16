<?php
$breadcrumbParent = $breadcrumbParent ?? 'Espace citoyen';
$breadcrumbCurrent = $breadcrumbCurrent ?? 'Accueil';
?>
<div class="topbar">
  <div class="breadcrumb"><?php echo htmlspecialchars($breadcrumbParent); ?> <span>/</span> <strong><?php echo htmlspecialchars($breadcrumbCurrent); ?></strong></div>
  <div class="topbar-right">
    <button class="icon-btn" aria-label="Notifications">🔔</button>
    <div class="user-chip">
      <div class="avatar">SA</div>
      <div class="user-meta">
        <div class="user-name">Salma Amri</div>
        <div class="user-role">Compte citoyen</div>
      </div>
    </div>
  </div>
</div>