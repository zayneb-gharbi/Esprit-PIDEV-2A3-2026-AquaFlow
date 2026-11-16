<?php
// Layout générique (Back Office) - exemple de structure MVC
// Variables attendues : $title, $currentPage, $content, $extraScripts (optionnel)
$base = $base ?? '../..';
require_once __DIR__ . '/partials/head.php';
require_once __DIR__ . '/partials/sidebar.php';
?>
<main class="main">
  <div class="main-inner">
    <?php echo $content ?? ''; ?>
    <?php require_once __DIR__ . '/partials/footer.php'; ?>
  </div>
</main>
<?php require_once __DIR__ . '/partials/scripts.php'; ?>