# ✅ To-Do List — Projet Technologies Web 2A (2026-2027)

> **Période :** semaine prochaine · **Échéance exacte :** `____ / ____ / 2026`
> **Groupe :** `__________` · **Classe :** `__________` · **Tuteurs :** `__________`

Cette liste regroupe les tâches demandées par les tuteurs et les consignes des trois documents joints :

| Document | Utilisé pour |
|---|---|
| `Atelier-Intégration_Template.pdf` | Intégration des templates Front Office et Back Office (MVC) |
| `Guide_pour_Rédiger_un_README.pdf` | Rédaction et publication du README (Markdown + Git) |
| `Standardisation_des_dépôts_GitHub___Projets_PI.pdf` | Nommage, description, topics, structure du README, évaluation |

---

## 📌 Vue d'ensemble

| # | Tâche | Qui ? | Statut |
|---|---|---|---|
| 1 | Corriger le diagramme de cas d'utilisation (remarques des tuteurs) | Groupe | ☐ |
| 2 | Préparer le diagramme de classes | Groupe | ☐ |
| 3 | Créer les dépôts GitHub et inviter les tuteurs | Groupe | ☐ |
| 4 | Préparer les pages HTML de chaque partie | Chaque étudiant | ☐ |
| 5 | Intégrer les templates Back Office et Front Office en MVC | Étudiants + groupe | ☐ |

---

## 1️⃣ Corriger le diagramme de cas d'utilisation

- [ ] Récupérer toutes les remarques formulées par les tuteurs
- [ ] Lister chaque remarque et la décision prise (corrigée / discutée avec le tuteur)
- [ ] Mettre à jour les acteurs, cas d'utilisation et relations (`include`, `extend`, généralisation)
- [ ] Vérifier que chaque partie du projet (chaque étudiant) apparaît dans le diagramme
- [ ] Exporter le diagramme en image/PDF et le versionner dans le dépôt (ex. `docs/`)
- [ ] Faire relire par le groupe avant envoi

## 2️⃣ Préparer le diagramme de classes

- [ ] Identifier les entités principales à partir du diagramme de cas d'utilisation corrigé
- [ ] Définir pour chaque classe : attributs, types, méthodes
- [ ] Définir les relations : associations, multiplicités, héritage, composition/agrégation
- [ ] Vérifier la cohérence avec les modèles prévus dans `Models/` (voir §5)
- [ ] Exporter le diagramme et le versionner dans le dépôt (ex. `docs/`)

---

## 3️⃣ Créer les dépôts GitHub et inviter les tuteurs

### 3.1 Nom du dépôt (obligatoire)

Format : **`Esprit-[PI]-[Classe]-[AU]-[NomDuProjet]`**

Exemple du guide : `Esprit-PIDEV-3A10-2026-TaskManager`

- [ ] Remplacer `[PI]` par le code du projet intégré de votre module (à confirmer avec les tuteurs)
- [ ] Remplacer `[Classe]` par votre classe, `[AU]` par l'année universitaire, `[NomDuProjet]` par le nom du projet
- [ ] Nom final retenu : `Esprit-________-________-________-________________`

### 3.2 Création du dépôt

- [ ] GitHub → **New repository** → nom conforme, description, initialiser avec un `README.md`
- [ ] Dépôt **public** (la publication publique est validée par les tuteurs)
- [ ] Créer le dépôt dans l'organisation indiquée par les tuteurs (le guide utilise `Projet-technologies-web-2A`)
- [ ] **Inviter les tuteurs concernés** : Settings → Collaborators (ou accès via l'organisation)
  - Tuteur 1 : `__________` ☐ invité
  - Tuteur 2 : `__________` ☐ invité
- [ ] Ajouter les membres du groupe comme collaborateurs

### 3.3 Description du dépôt (obligatoire)

Doit contenir : *Developed at Esprit School of Engineering – Tunisia*, l'année universitaire et les technologies principales.

Exemple du guide :
> This project was developed as part of the PIDEV – 3rd Year Engineering Program at Esprit School of Engineering (Academic Year 2025–2026).

- [ ] Renseigner la description via l'icône ⚙️ à côté de **About** → *Edit repository details*

### 3.4 Topics obligatoires (minimum)

- [ ] `esprit-school-of-engineering`
- [ ] `academic-project`
- [ ] `esprit-[PI]` (ex. `esprit-pidev`)
- [ ] l'année universitaire (ex. `2026-2027`)
- [ ] la technologie principale (ex. `php`)

> Les topics se séparent par des espaces dans le champ *Topics*.

### 3.5 README structuré (obligatoire)

La **structure minimale requise** est celle du document de standardisation. Le guide README sert à apprendre la syntaxe Markdown et à rédiger les sections Installation / Contribution / Licence (à placer dans *Getting Started* / *Contributors* ci-dessous).

- [ ] Utiliser exactement ces titres :

```markdown
# Project Title
## Overview
## Features
## Tech Stack
### Frontend
### Backend
## Architecture
## Contributors
## Academic Context
## Getting Started
## Acknowledgments
```

- [ ] **Overview** : objectif, problème résolu, fonctionnalités principales, mention *Esprit School of Engineering* + année universitaire
- [ ] **Features** : liste des fonctionnalités
- [ ] **Tech Stack** : Frontend (ex. HTML/CSS/Bootstrap) et Backend (ex. PHP, MySQL)
- [ ] **Architecture** : MVC (dossiers `Controllers`, `Models`, `Views`) + schéma/diagrammes si possible
- [ ] **Contributors** : liens GitHub des membres et leur rôle
- [ ] **Academic Context** : *Developed at **Esprit School of Engineering – Tunisia*** + module + classe + année
- [ ] **Getting Started** : cloner le dépôt, placer dans `htdocs` (XAMPP) ou `www` (WAMP), démarrer Apache/MySQL, ouvrir `http://localhost/...`
- [ ] **Acknowledgments** : remerciements (tuteurs, ressources, templates utilisés et leurs licences)
- [ ] Utiliser obligatoirement le mot-clé **« Esprit School of Engineering »** pour désigner Esprit
- [ ] Syntaxe : liens `[texte](URL)`, **gras** `**texte**`, *italique* `*texte*`, blocs de code ```` ```bash ````
- [ ] Remplacer tous les liens exemples (`github.com/utilisateur/projet.git`) par le lien réel du dépôt

### 3.6 Workflow Git à chaque modification

```bash
git clone https://github.com/<organisation>/<nom-du-depot>.git
cd <nom-du-depot>

git add .
git commit -m "message clair décrivant la modification"
git push origin main
```

- [ ] Cloner le dépôt en local
- [ ] Faire des commits réguliers et explicites (le nombre de commits fait partie des indicateurs de suivi)
- [ ] Vérifier sur GitHub que les modifications apparaissent bien

### 3.7 Hébergement (recommandé, facultatif)

Options citées : GitHub Pages, Vercel, Render, Railway, DigitalOcean (via GitHub Education).

- [ ] Décider si le projet sera hébergé *(un projet PHP/MySQL nécessite un hébergeur compatible PHP, pas GitHub Pages seul)*
- [ ] Si hébergé : ajouter l'URL dans le champ **Website** du dépôt

### 3.8 Barème lié à GitHub (à viser)

| Critère | Points |
|---|---|
| Convention de nommage | 0,5 |
| Description du projet | 0,5 |
| Utilisation de « topics » | 0,5 |
| Mots-clés dans le README | 0,25 (ou 0,5 si pas d'hébergement) |
| Hébergement | 0,25 (facultatif) |

---

## 4️⃣ Préparer les pages HTML de chaque partie (par étudiant)

- [ ] Chaque étudiant liste les pages de **sa** partie (Front Office et/ou Back Office)
- [ ] Créer la maquette HTML de chaque page à partir des templates choisis
- [ ] Séparer clairement ce qui est **commun** (head, navbar/sidebar, header, footer, scripts) de ce qui est **spécifique** à la page
- [ ] Garder le visuel strictement identique au template d'origine

| Étudiant | Partie / module | Pages Front Office | Pages Back Office | Statut |
|---|---|---|---|---|
| `__________` | `__________` | `__________` | `__________` | ☐ |
| `__________` | `__________` | `__________` | `__________` | ☐ |
| `__________` | `__________` | `__________` | `__________` | ☐ |
| `__________` | `__________` | `__________` | `__________` | ☐ |

---

## 5️⃣ Intégrer les templates en respectant l'architecture MVC

> ⚠️ **Important :** l'intégration doit **impérativement** suivre les consignes et étapes du workshop (`Atelier-Intégration_Template.pdf`).

- [ ] Template **Front Office** choisi par l'équipe : `__________`
- [ ] Template **Back Office** choisi par l'équipe : `__________`
- [ ] Environnement prêt : XAMPP/WAMP (dossier `htdocs`/`www`) **ou** `php -S localhost:8000`

### 5.1 Front Office (pattern `includes/`)

**Structure cible :**

```
NomProjet/
├── Controllers/     (HomeController.php, ...)
├── Models/          (classes métier)
└── Views/
    ├── assets/      (css, img, js, lib)
    ├── includes/    (head.php, spinner.php, navbar.php, footer.php, scripts.php)
    ├── index.php, about.php, ...
```

- [ ] **Étape 1 – Analyser** le template : repérer les zones répétées (head, spinner, navbar, contenu, footer + back to top, scripts)
- [ ] **Étape 2 – Créer** le dossier `includes/`
- [ ] **Étape 3 – `head.php`** : copier le contenu entre `<head>` et `</head>` **sans** les balises elles-mêmes ; `<title>` dynamique via `$pageTitle`
- [ ] **Étape 4 – `spinner.php`** : déplacer le bloc spinner
- [ ] **Étape 5 – `navbar.php`** : menu dynamique avec `$currentPage` et une fonction `navActive($page, $currentPage)` qui ajoute la classe `active`
- [ ] **Étape 6 – `footer.php`** : footer complet **+ bouton « Back to Top »**
- [ ] **Étape 7 – `scripts.php`** : toutes les balises `<script>` de fin de page
- [ ] **Étape 8 – Assembler `index.php`** avec `require_once` (en définissant `$pageTitle` et `$currentPage` avant)
- [ ] **Étape 9 – Tester** en local (`http://localhost/<dossier>/index.php`) — sans serveur PHP, le code brut s'affiche
- [ ] **Étape 10 – Dupliquer** le pattern pour chaque autre page :
  1. Copier le contenu de la page HTML d'origine
  2. Définir `$pageTitle` et `$currentPage`, puis `require_once 'includes/head.php'`
  3. Remplacer spinner, navbar, footer, scripts par leurs `require_once`
  4. Garder tel quel le contenu spécifique de la page
  5. Vérifier que le lien du menu devient **actif automatiquement**
- [ ] Créer le **Controller** et, si nécessaire, le **Model** associés à chaque page

### 5.2 Back Office (pattern `partials/` + `layout.php`)

> Le workshop cite le dépôt **TailAdmin** pour le lien de téléchargement, mais les captures et noms de dossiers (`plainadmin-mvc`, « PlainAdmin ») correspondent au template **PlainAdmin**. Confirmer avec les tuteurs le template réellement utilisé.

**Structure cible :**

```
NomProjet/
├── index.php                (point d'entrée, ex. index.php?page=dashboard)
└── app/
    ├── routes.php
    ├── helpers.php          (isActive, isOpen, isCollapsed, groupIsExpanded)
    ├── Controllers/         (Controller.php abstrait, DashboardController.php, ...)
    ├── Models/              (Model.php, ...)
    └── Views/
        ├── layout.php
        ├── assets/          (css, fonts, images, js, scss)
        ├── pages/           (dashboard.php, tables.php, signin.php, dashboard_scripts.php ...)
        └── partials/        (head.php, sidebar.php, header.php, footer.php, scripts.php)
```

- [ ] **Étape 1 – Analyser** `index.html` (dashboard) : head + preloader, sidebar, header, contenu, footer, scripts
- [ ] **Étape 2 – Créer** `app/Views/partials/`
- [ ] **Étape 3 – `head.php`** : `<head>…</head>` **+ bloc preloader** ; `<title>` via `$title ?? '…'`
- [ ] **Étape 4 – `sidebar.php`** : `$currentPage` + fonctions utilitaires pour l'état actif / ouvert des liens et sous-menus
- [ ] **Étape 5 – `header.php`** : simple copier-coller (identique partout, aucune logique PHP)
- [ ] **Étape 6 – `footer.php`** : copyright + liens Conditions / Confidentialité
- [ ] **Étape 7 – `scripts.php`** : scripts communs (Bootstrap, Chart.js, etc.) + `$extraScripts` pour les scripts propres à une page
- [ ] **Étape 8 – `Views/layout.php`** : inclut head → sidebar → `<main>` (header → `<?= $content ?>` → footer) → scripts ; variables attendues : `$title`, `$currentPage`, `$content`, `$extraScripts`
- [ ] `Controller.php` (classe abstraite) : propriétés `$page` et `$title`, méthode `render($view, $data = [], $scriptsView = null)` qui capture la vue dans `$content` puis inclut `layout.php`
- [ ] **Étape 9 – Pages de contenu** (`Views/pages/*.php`) : HTML pur, **sans** `require_once`
- [ ] Un Controller par page (ex. `TablesController` : `$page = 'tables'`, `$title = 'Tables | …'`, méthode `index()` → `render('tables')`)
- [ ] **Étape 10 – Tester** : `php -S localhost:8000` depuis la racine, puis `http://localhost:8000/index.php?page=dashboard` (ou via `htdocs`/`www`)
- [ ] **Étape 11 – Dupliquer** pour les autres pages (une vue + un controller chacune) et **décommenter la route** correspondante dans `app/routes.php`

Pages restantes à créer d'après le workshop (clé de page → controller) :

| Clé de page | Controller | Pris par | Fait |
|---|---|---|---|
| settings | SettingsController | `______` | ☐ |
| blank-page | BlankPageController | `______` | ☐ |
| invoice | InvoiceController | `______` | ☐ |
| signup | SignupController | `______` | ☐ |
| alerts | AlertsController | `______` | ☐ |
| buttons | ButtonsController | `______` | ☐ |
| cards | CardsController | `______` | ☐ |
| typography | TypographyController | `______` | ☐ |
| icons | IconsController | `______` | ☐ |
| mdi-icons | MdiIconsController | `______` | ☐ |
| form-elements | FormElementsController | `______` | ☐ |
| notification | NotificationController | `______` | ☐ |

*(Dashboard, Tables et Sign In sont déjà fournis comme modèles.)*

### 5.3 Résultat attendu (critères de validation)

- [ ] Le visuel est **strictement identique** au template HTML d'origine
- [ ] Aucun bloc commun n'est dupliqué : un seul fichier par composant
- [ ] Le lien actif du menu s'affiche **automatiquement** sur chaque page
- [ ] Les pages de contenu restent lisibles (HTML pur)
- [ ] Séparation MVC respectée : Controllers (logique), Models (données), Views (affichage)

---

## 📤 Checklist finale avant la deadline

- [ ] Diagramme de cas d'utilisation corrigé et envoyé
- [ ] Diagramme de classes prêt
- [ ] Dépôt GitHub public, nom conforme, tuteurs invités
- [ ] Description + topics + README structuré en place (avec « Esprit School of Engineering »)
- [ ] Pages HTML de chaque étudiant prêtes
- [ ] Templates Front Office et Back Office intégrés en MVC, testés en local
- [ ] Tout est poussé sur `main` (`git add . && git commit && git push origin main`)

---

## 📝 Notes et questions pour les tuteurs

- Code `[PI]` à utiliser dans le nom du dépôt et le topic : `__________`
- Template Back Office : TailAdmin ou PlainAdmin ? `__________`
- Organisation GitHub / pseudos des tuteurs : `__________`
- Autres remarques : `__________`
