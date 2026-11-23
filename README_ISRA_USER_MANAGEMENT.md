# AquaFlow — Gestion des utilisateurs (partie Isra)

Ce document décrit les fonctionnalités ajoutées par Isra Benhassouna sur la base d'`origin/zayneb`
(branche `isra/status-model` → `isra/block-unblock-backend` → `isra/block-unblock-ui` → `isra/edit-profile`).

> **Branche `event/user`** : intégration locale sur une **seule** base MySQL/MariaDB `aquaflow`.
> Les branches Oracle (`isra/*`) et la branche événements d'origine restent inchangées.

## Installation XAMPP / MySQL (branche `event/user`)

1. Démarrer **Apache** et **MySQL** dans XAMPP.
2. Importer `database/aquaflow_mysql.sql` via phpMyAdmin ou :
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -uroot --default-character-set=utf8mb4 < database\aquaflow_mysql.sql
   ```
   Cela crée la base `aquaflow`, les **5 tables partagées** et 6 comptes de test
   (mot de passe `Test1234!`). Le script utilise `CREATE TABLE IF NOT EXISTS` et
   `INSERT IGNORE` : il ne supprime aucune donnée existante.
3. Copier le dossier du projet dans `C:\xampp\htdocs\AquwaFlow`.
 4. Ouvrir `http://localhost/AquwaFlow/`. Si vous n'êtes pas connecté, la page
    affiche automatiquement le formulaire de connexion ; sinon le tableau de bord.
 5. Identifiants : **e-mail** ou **téléphone** (ex. `admin@aquaflow.tn` / `20000001`),
    mot de passe `Test1234!`. C'est le **seul** login de tout le projet, y compris
    pour le module Événements.

Connexion dans `app/Models/Model.php` : `127.0.0.1:3306`, base `aquaflow`, utilisateur `root`,
mot de passe vide (valeurs XAMPP). Surcharge possible via `DB_HOST`, `DB_PORT`, `DB_NAME`,
`DB_USER`, `DB_PASS` — aucun identifiant réel n'est versionné.

## Apparence NOVA et documents de référence

- La couche visuelle **NOVA « Ville & Eau »** est appliquée par
  `app/Views/assets/css/nova.css`, chargée **après** `user.css` et `app.css` dans
  `app/Views/partials/head.php`. Elle ne modifie aucune logique : elle mappe seulement
  les classes existantes sur les tokens du `NOVA_DESIGN_SYSTEM.md`
  (`--navy-900: #063B6E`, `--teal-500: #14B8A6`, sidebar fixe, boutons teal à texte navy).
- Les documents qu'Isra voulait garder sont versionnés à la racine du dépôt :
  `START_HERE.md`, `ISRA_REPO_AUDIT_AND_PLAN.md`, `ISRA_USER_MANAGEMENT_BRIEF.md`,
  `NOVA_DESIGN_SYSTEM.md`, `AQUAFLOW_MODULES_SPEC.md` et `TODO_Semaine_Prochaine.md`.
- Index lisible dans le navigateur :
  `http://localhost/AquwaFlow/docs.php`
  (cliquez sur un document pour le lire, ou sur « Ouvrir le .md brut »).

## Un seul projet, un seul login

- **Racine du projet** : `http://localhost/AquwaFlow/`. Le contrôleur frontal affiche
  automatiquement la connexion si la session est absente, sinon le tableau de bord.
- **Tableau de bord** : `http://localhost/AquwaFlow/index.php?page=dashboard`. Chiffres clés
  et raccourcis vers la gestion des utilisateurs, l'historique, les événements et les documents.
- **Un seul login** : e-mail ou téléphone (`admin@aquaflow.tn` / `20000001`) + mot de passe
  `Test1234!`. Les rôles `A/C/P` de `utilisateur` commandent tous les espaces, y compris
  les pages Événements. Le faux login sans mot de passe de l'ancien module autonome a
  été supprimé.
- **Maquettes NOVA (référence statique, non connectées)** :
  `http://localhost/AquwaFlowMd/Views/pages/admin/index.php` et
  `http://localhost/AquwaFlowMd/Views/pages/front/index.php`.

## Une seule base, cinq tables, un pont d'identité

| Table | Module | Remarque |
|---|---|---|
| `utilisateur` | Utilisateurs (MVC) | Comptes, rôles `A/C/P`, statuts `en_attente/actif/bloque/desactive`. |
| `historique` | Utilisateurs (MVC) | Actions admin/profil, clé étrangère vers `utilisateur`. |
| `users` | Événements | Profils événementiels conservés ; `users.utilisateur_id` pointe vers `utilisateur.id_user`. |
| `events` | Événements | Clés étrangères vers `users`. |
| `event_registrations` | Événements | Clés étrangères vers `events` et `users`. |

`users` (événements) et `utilisateur` (MVC) restent deux tables distinctes : leurs colonnes,
rôles et statuts ne sont pas compatibles. Le script `database/006_integrated_event_auth.sql`
ajoute le pont explicite `users.utilisateur_id` sans supprimer aucune donnée événementielle.

### Migration depuis l'ancienne base `aquaflow_events` (une seule fois)

Conserver `aquaflow_events` comme sauvegarde, puis copier ses données dans `aquaflow` :

```sql
INSERT INTO aquaflow.users (id, first_name, last_name, email, role, status, password_hash, created_at)
SELECT id, first_name, last_name, email, role, status, password_hash, created_at FROM aquaflow_events.users;

INSERT INTO aquaflow.events
    (id, title, description, event_type, audience, starts_at, ends_at, location,
     zone, capacity, status, cancel_reason, created_by, cancelled_by, cancelled_at, created_at)
SELECT id, title, description, event_type, audience, starts_at, ends_at, location,
       zone, capacity, status, cancel_reason, created_by, cancelled_by, cancelled_at, created_at
FROM aquaflow_events.events;

INSERT INTO aquaflow.event_registrations (id, event_id, user_id, registered_at, status, cancelled_at)
SELECT id, event_id, user_id, registered_at, status, cancelled_at FROM aquaflow_events.event_registrations;
```

> Ne pas insérer `active_user_id` : c'est une colonne générée stockée. À vérifier :
> `SELECT 'users',COUNT(*) FROM aquaflow.users UNION ALL SELECT 'events',COUNT(*) FROM aquaflow.events UNION ALL SELECT 'registrations',COUNT(*) FROM aquaflow.event_registrations;`

## Fonctionnalités

| Fonctionnalité | Où | Détail |
|---|---|---|
| **Bloquer un compte** | Admin → Citoyens / Plombiers | Bouton « Bloquer » (rouge, contour) → modale avec motif facultatif (5–255 caractères). Écrit `statut = 'bloque'`. |
| **Débloquer un compte** | Admin → Citoyens / Plombiers | Bouton « Débloquer » (teal, contour) visible uniquement pour `bloque`. Écrit `statut = 'actif'`. |
| **Désactivation volontaire** | Profil → « Désactiver mon compte » | Écrit `statut = 'desactive'` (distinct d'un blocage admin). |
| **Modifier mes informations** | Profil → « Modifier mes informations » | `?page=profile-edit` : prénom, nom, e-mail, téléphone (+ zone de service et expérience pour les plombiers). |
| **Historique** | `?page=global-history` / `personal-history` | Lignes « Blocage utilisateur », « Déblocage utilisateur », « Modification profil », « Désactivation compte ». |

## Modèle de statuts (décision D1)

- `actif` : compte actif.
- `en_attente` : plombier en attente d'approbation.
- `bloque` : **bloqué par l'administrateur** — seul l'admin peut remettre en `actif`.
- `desactive` : **désactivé par l'utilisateur lui-même** — aucun bouton « Débloquer » n'est proposé.

Le motif du blocage n'a pas de colonne dédiée : il est stocké dans `HISTORIQUE.description`
avec l'administrateur auteur et la date (déjà présents dans la table). Aucune migration de colonne.

## Migration à exécuter une seule fois

> Sur la branche MySQL/XAMPP, importer uniquement `database/aquaflow_mysql.sql` (voir ci-dessus).
> Les deux scripts Oracle ci-dessous concernent les branches `isra/*` d'origine.

Dans Oracle SQL Developer connecté au schéma `AQUAFLOW` :

1. `database/historique_oracle.sql` (déjà requis par l'intégration de Zayneb).
2. `database/utilisateur_status_oracle.sql` (nouveau) — remplace la contrainte CHECK sur `STATUT`
   pour autoriser `desactive`. Aucune donnée n'est modifiée : les comptes déjà `bloque` restent bloqués.

Vérification :
```sql
SELECT statut, COUNT(*) FROM utilisateur GROUP BY statut ORDER BY statut;
```

## Comptes de test (à confirmer avec Omar — décision D3)

> Le script `database/utilisateur_oracle.sql` et les comptes de test ne sont pas encore versionnés
> dans cette branche. Les comptes doivent couvrir au minimum :

| Rôle | Statut | Usage |
|---|---|---|
| `A` | `actif` | Tester Bloquer / Débloquer et l'historique global. |
| `C` | `actif` | Tester « Modifier mes informations » puis « Désactiver mon compte ». |
| `C` | `bloque` | Vérifier le message « compte bloqué par un administrateur » et le bouton « Débloquer ». |
| `C` | `desactive` | Vérifier qu'aucun bouton « Débloquer » n'apparaît et le message « compte désactivé ». |
| `P` | `en_attente` | Vérifier qu'aucune action de blocage n'apparaît. |
| `P` | `actif` | Tester l'édition de la zone de service et de l'expérience. |

## Procédure de test (navigateur)

1. Se connecter en admin → `?page=citoyens`.
2. « Bloquer » sur un citoyen actif → saisir un motif → confirmer → flash « Compte bloqué. »
   et ligne « Blocage utilisateur » dans l'historique global.
3. Le citoyen se reconnecte → message « Votre compte a été bloqué par un administrateur. »
4. Admin → « Débloquer » → flash « Compte débloqué. » → le citoyen peut se reconnecter.
5. Citoyen → `?page=profile` → « Modifier mes informations » → changer le téléphone
   → flash « Vos informations ont été mises à jour. » + ligne « Modification profil ».
6. Citoyen → « Désactiver mon compte » → statut `desactive`, message « Votre compte est désactivé. »
7. Admin → le compte affiche « Désactivé » (et non « Bloqué »), sans bouton « Débloquer ».

## Sécurité

- Toutes les actions POST exigent un jeton CSRF.
- L'id du compte cible vient du POST **uniquement pour l'admin**, et est vérifié (`> 0`, pas soi-même).
- L'id du profil modifié vient **uniquement de la session**.
- Requêtes préparées + UPDATE conditionnels (statut/rôle vérifiés en SQL) sur le blocage.
- Liste blanche de champs : `role`, `statut`, `mot_de_passe`, `date_creation` ne sont jamais écrits
  par le formulaire de profil.
- Validation serveur (longueur, format, unicité) **et** client (mêmes règles).
- `e()` (htmlspecialchars) sur toutes les sorties, y compris le motif affiché dans l'historique.
