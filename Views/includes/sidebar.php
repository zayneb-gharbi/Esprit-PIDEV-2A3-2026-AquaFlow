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

  <div class="nav-label">ESPACE CITOYEN</div>
  <nav class="nav-list">
    <a class="nav-item" href="index.php"<?php echo navActive('accueil', $currentPage); ?>><span class="nav-icon">⌂</span><span>Accueil</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">✦</span><span>Signaler une fuite</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">◷</span><span>Mes signalements</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">▥</span><span>Consommation</span></a>
    <a class="nav-item" href="evenements.php"<?php echo navActive('evenements', $currentPage); ?>><span class="nav-icon">📅</span><span>Événements</span></a>
    <a class="nav-item" href="#"><span class="nav-icon">★</span><span>Mes récompenses</span></a>
  </nav>

  <div class="sidebar-bottom">
    <div class="sidebar-promo">
      <h4>Chaque goutte compte</h4>
      <p>Un signalement peut aider tout votre quartier.</p>
      <a class="btn btn-primary btn-full" href="#">Signaler une fuite →</a>
    </div>
  </div>
</aside>