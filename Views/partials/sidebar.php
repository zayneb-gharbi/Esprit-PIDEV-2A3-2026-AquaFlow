<?php
function navActive($key, $current) {
    return $key === $current ? ' aria-current="page"' : '';
}
?>
<aside class="sidebar">
  <div class="logo-block">
    <div class="logo-mark"><span></span><span></span><span></span><span></span><span></span></div>
    <div class="logo-word">AquaFlow</div>
    <div class="logo-sub">GESTION DE L'EAU</div>
  </div>

  <div class="nav-label">PILOTAGE</div>
  <nav class="nav-list">
    <a class="nav-item" href="index.php"<?php echo navActive('admin-dashboard', $currentPage); ?>><span class="nav-icon">▦</span><span>Tableau de bord</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">✦</span><span>Signalements</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">🔧</span><span>Interventions</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">👤</span><span>Citoyens</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">🛠</span><span>Plombiers</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">▥</span><span>Consommation</span></a>
    <a class="nav-item" href="evenements.php"<?php echo navActive('admin-evenements', $currentPage); ?>><span class="nav-icon">📅</span><span>Événements</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">★</span><span>Récompenses</span></a>
  </nav>

  <div class="nav-label">SYSTÈME</div>
  <nav class="nav-list">
    <a class="nav-item" href="#"><span class="nav-icon">⚙</span><span>Paramètres</span></a>
  </nav>

  <div class="sidebar-bottom">
    <div class="sidebar-footer-text">AquaFlow · Gestion intelligente de l'eau<br>Version de démonstration</div>
  </div>
</aside>