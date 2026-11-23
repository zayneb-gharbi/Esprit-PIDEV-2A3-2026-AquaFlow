<?php
/**
 * Layout principal - inclut tous les blocs communs et injecte le contenu
 * propre a chaque page ($content) au milieu.
 *
 * Variables attendues (fournies par Controller::render()) :
 *   $title        string  - titre de l'onglet <title>
 *   $currentPage  string  - cle de la page courante (pour le menu)
 *   $content      string  - HTML de la page (deja rendu)
 *   $extraScripts string  - JS specifique a la page (optionnel)
 *   $withNav      bool    - afficher la navigation + footer ?
 */
require_once __DIR__ . '/partials/head.php';

if ($withNav) {
    require_once __DIR__ . '/partials/header.php';
    ?>
    <main class="page-main">
        <?= $content ?>
    </main>
    <?php
    require_once __DIR__ . '/partials/footer.php';
} else {
    echo $content;
}

require_once __DIR__ . '/partials/scripts.php';
