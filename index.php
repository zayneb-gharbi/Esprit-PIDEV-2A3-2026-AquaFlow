<?php
/**
 * Portail d'entree unique AquaFlow / NOVA.
 * Rend tous les livrables accessibles depuis une seule page et clarifie
 * quels comptes demandent un mot de passe et lesquels n'en demandent pas.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AquaFlow — Portail des espaces</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="aquaflow_integration1/app/Views/assets/css/nova.css">
    <style>
        .portal-topbar {
            background: var(--navy-900);
            color: #fff;
            padding: 18px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .portal-brand { font-size: 22px; font-weight: 800; letter-spacing: .08em; }
        .portal-brand small {
            display: block;
            font-size: 10px;
            letter-spacing: .2em;
            color: #7FA6C9;
            text-transform: uppercase;
        }
        .portal-topbar nav { display: flex; gap: 8px; flex-wrap: wrap; }
        .portal-topbar nav a {
            color: #fff;
            padding: 8px 12px;
            border-radius: var(--r-md);
            font-size: 13px;
            font-weight: 600;
        }
        .portal-topbar nav a:hover { background: rgba(255,255,255,.08); }

        .portal-main { max-width: 1180px; margin: 0 auto; padding: 32px 24px 48px; }

        .portal-hero {
            background: var(--hero-gradient);
            color: #fff;
            border-radius: var(--r-xl);
            padding: 36px 40px;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
        }
        .portal-hero::before {
            content: "";
            position: absolute;
            width: 320px; height: 320px;
            right: -60px; top: -90px;
            border: 1px solid rgba(255,255,255,.08);
            border-radius: 50%;
        }
        .portal-hero h1 { color: #fff; font-size: 32px; font-weight: 800; margin: 8px 0; }
        .portal-hero p { color: rgba(255,255,255,.85); max-width: 640px; font-size: 14px; line-height: 1.7; }
        .portal-eyebrow { color: var(--teal-300); font-size: 11px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }
        .portal-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            box-shadow: var(--shadow-card);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            color: var(--ink-700);
            transition: border-color .15s ease, transform .15s ease;
        }
        .portal-card:hover { border-color: var(--teal-500); transform: translateY(-2px); }
        .portal-card .num {
            width: 34px; height: 34px;
            border-radius: 10px;
            background: var(--teal-100);
            color: var(--teal-700);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800;
        }
        .portal-card h2 { font-size: 18px; font-weight: 800; color: var(--ink-900); }
        .portal-card p { font-size: 13px; line-height: 1.6; color: var(--ink-500); flex: 1; }
        .portal-card .cta { color: var(--teal-700); font-weight: 800; font-size: 13px; }
        .portal-card .tag {
            display: inline-block;
            padding: 4px 10px;
            border-radius: var(--r-pill);
            background: var(--teal-50);
            color: var(--teal-700);
            font-size: 11px;
            font-weight: 700;
        }
        .portal-card .tag.warn { background: var(--st-pending-bg); color: var(--st-pending-fg); }

        .portal-note {
            background: var(--surface);
            border: 1px solid var(--border);
            border-left: 4px solid var(--teal-500);
            border-radius: var(--r-md);
            padding: 20px 24px;
        }
        .portal-note h3 { font-size: 16px; color: var(--ink-900); margin-bottom: 10px; }
        .portal-note ul { margin: 0; padding-left: 18px; color: var(--ink-700); font-size: 13px; line-height: 1.8; }
        .portal-note code {
            background: var(--teal-50);
            color: var(--teal-700);
            padding: 2px 6px;
            border-radius: 6px;
            font-size: 12px;
        }
        @media (max-width: 768px) {
            .portal-hero { padding: 28px 24px; }
            .portal-hero h1 { font-size: 26px; }
        }
    </style>
</head>
<body>
<header class="portal-topbar">
    <div class="portal-brand">NOVA<small>Ville &amp; Eau · AquaFlow</small></div>
    <nav>
        <a href="aquaflow_integration1/index.php?page=signin">Connexion</a>
        <a href="aquaflow_integration1/docs.php">Documents Isra</a>
        <a href="http://localhost/aquaflow-events/aquaflow-events/dev_stubs/login_stub.php">Événements</a>
    </nav>
</header>

<main class="portal-main">
    <section class="portal-hero">
        <div class="portal-eyebrow">Portail de démonstration</div>
        <h1>Bienvenue dans AquaFlow</h1>
        <p>
            Un seul point de départ pour tester la gestion des utilisateurs, le module Événements,
            les maquettes NOVA d'origine et les documents de référence d'Isra. Choisissez un espace ci-dessous.
        </p>
    </section>

    <section class="portal-grid">
        <a class="portal-card" href="aquaflow_integration1/index.php?page=dashboard">
            <span class="num">1</span>
            <span class="tag">Mot de passe requis</span>
            <h2>Tableau de bord admin</h2>
            <p>Connectez-vous en admin, puis gérez citoyens, plombiers, historiques, statistiques et export.</p>
            <span class="cta">Ouvrir le dashboard →</span>
        </a>

        <a class="portal-card" href="aquaflow_integration1/index.php?page=signin">
            <span class="num">2</span>
            <span class="tag">admin@aquaflow.tn / Test1234!</span>
            <h2>Gestion des utilisateurs</h2>
            <p>Connexion réelle avec mot de passe : blocage, déblocage, édition de profil, désactivation et historique.</p>
            <span class="cta">Se connecter →</span>
        </a>

        <a class="portal-card" href="http://localhost/aquaflow-events/aquaflow-events/dev_stubs/login_stub.php">
            <span class="num">3</span>
            <span class="tag warn">Aucun mot de passe</span>
            <h2>Module Événements</h2>
            <p>Faux login : choisissez directement un profil (Sonia = admin, Mariem/Karim = utilisateurs) puis gérez les événements.</p>
            <span class="cta">Ouvrir les événements →</span>
        </a>

        <a class="portal-card" href="http://localhost/AquwaFlowMd/Views/pages/admin/index.php">
            <span class="num">4</span>
            <span class="tag">Maquettes statiques</span>
            <h2>Templates NOVA d'origine</h2>
            <p>Les pages event task d'origine : dashboard admin, écrans evenements, formulaire, détails et espace citoyen.</p>
            <span class="cta">Voir les maquettes →</span>
        </a>

        <a class="portal-card" href="aquaflow_integration1/docs.php">
            <span class="num">5</span>
            <span class="tag">Référence</span>
            <h2>Documents Isra</h2>
            <p>START HERE, audit, brief, design system NOVA, spécification des modules et to-do de la semaine.</p>
            <span class="cta">Lire les documents →</span>
        </a>
    </section>

    <section class="portal-note">
        <h3>Comptes de test — important</h3>
        <ul>
            <li><strong>Application Utilisateurs :</strong> mot de passe <code>Test1234!</code> obligatoire. Admin : <code>admin@aquaflow.tn</code> ou <code>20000001</code>.</li>
            <li><strong>Module Événements :</strong> <strong>aucun mot de passe</strong>. Le faux login demande seulement de choisir un profil dans la liste.</li>
            <li><strong>Maquettes NOVA :</strong> aucune connexion, ce sont des pages statiques de démonstration.</li>
            <li><strong>Dashboard admin direct :</strong> <code>http://localhost/AquwaFlow/aquaflow_integration1/index.php?page=dashboard</code>.</li>
        </ul>
    </section>
</main>
</body>
</html>
