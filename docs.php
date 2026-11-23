<?php
declare(strict_types=1);

/**
 * docs.php — index lisible dans le navigateur pour les documents de reference
 * d'Isra. Les fichiers .md restent a la racine du projet et ne sont pas modifies ici.
 */

if (!function_exists('e')) {
    function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

$docs = [
    'START_HERE.md'                  => 'START HERE — quel fichier lire, dans quel ordre',
    'ISRA_REPO_AUDIT_AND_PLAN.md'    => 'Audit du depot + plan PR1–PR5 (prioritaire)',
    'ISRA_USER_MANAGEMENT_BRIEF.md'  => 'Cahier des charges Isra — blocage / deblocage / profil',
    'NOVA_DESIGN_SYSTEM.md'          => 'Design system NOVA — couleurs, layout, composants',
    'AQUAFLOW_MODULES_SPEC.md'       => 'Specification des 6 modules (dont Gestion des evenements)',
    'TODO_Semaine_Prochaine.md'      => 'To-Do de la semaine — GitHub, README, templates, diagrams',
];

$requested = isset($_GET['doc']) && is_string($_GET['doc']) ? $_GET['doc'] : '';
$current   = array_key_exists($requested, $docs) ? $requested : '';
$docsDir   = __DIR__;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documents de reference — AquaFlow</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="app/Views/assets/css/user.css">
    <link rel="stylesheet" href="app/Views/assets/css/app.css">
    <link rel="stylesheet" href="app/Views/assets/css/nova.css">
    <style>
        .doc-content {
            margin: 0;
            padding: 20px 22px;
            max-height: 68vh;
            overflow: auto;
            background: var(--bg-page);
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, monospace;
            font-size: 12.5px;
            line-height: 1.65;
            color: var(--ink-700);
            white-space: pre-wrap;
            word-break: break-word;
        }
        .doc-index { margin: 0; padding: 0; list-style: none; }
        .doc-index li { margin-bottom: 10px; }
        .doc-index a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: var(--r-md);
            color: var(--ink-900);
            font-weight: 700;
        }
        .doc-index a:hover { border-color: var(--teal-500); background: var(--teal-50); }
        .doc-index code {
            color: var(--teal-700);
            font-size: 11px;
            font-weight: 600;
        }
        .doc-actions { display: flex; gap: 10px; flex-wrap: wrap; margin: 16px 0 20px; }
    </style>
</head>
<body>
<header class="topbar">
    <a class="brand-link" href="index.php?page=signin">Aqua Flow</a>
    <nav>
        <a href="docs.php" class="<?= $current === '' ? 'active' : '' ?>">Index</a>
        <?php foreach ($docs as $file => $label) { ?>
            <a href="docs.php?doc=<?= e($file) ?>" class="<?= $current === $file ? 'active' : '' ?>">
                <?= e($file === 'START_HERE.md' ? 'Start here' : ($file === 'TODO_Semaine_Prochaine.md' ? 'To-Do' : substr($file, 0, 22))) ?>
            </a>
        <?php } ?>
        <a href="index.php?page=signin">Retour a l'app</a>
    </nav>
</header>

<main class="page-main">
    <div class="card">
        <?php if ($current === '') { ?>
            <h1>Documents de reference</h1>
            <p class="sub">Les documents qu'Isra voulait garder a cote du projet. Cliquez pour les lire ici, ou utilisez le lien brut.</p>
            <ul class="doc-index">
                <?php foreach ($docs as $file => $label) { ?>
                    <li>
                        <a href="docs.php?doc=<?= e($file) ?>">
                            <span><?= e($label) ?></span>
                            <code><?= e($file) ?></code>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php
        } else {
            $path = $docsDir . DIRECTORY_SEPARATOR . $current;
            $content = is_file($path) ? (string) file_get_contents($path) : '(fichier introuvable : ' . $current . ')';
            ?>
            <h1><?= e($docs[$current]) ?></h1>
            <p class="sub"><?= e($current) ?></p>
            <div class="doc-actions">
                <a class="btn" href="<?= e($current) ?>" target="_blank" rel="noopener">Ouvrir le .md brut</a>
                <a class="btn-light" href="docs.php">Tous les documents</a>
            </div>
            <pre class="doc-content"><?= e($content) ?></pre>
        <?php } ?>
    </div>
</main>
</body>
</html>
