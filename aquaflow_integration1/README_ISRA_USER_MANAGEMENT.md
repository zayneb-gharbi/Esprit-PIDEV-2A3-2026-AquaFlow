# AquaFlow — Gestion des utilisateurs (partie Isra)

Ce document décrit les fonctionnalités ajoutées par Isra Benhassouna sur la base d'`origin/zayneb`
(branche `isra/status-model` → `isra/block-unblock-backend` → `isra/block-unblock-ui` → `isra/edit-profile`).

> **Branche `isra/mysql-xampp`** : port local de la base Oracle vers MySQL/MariaDB pour XAMPP.
> Les branches `isra/*` précédentes restent sur Oracle ; ce port n'est qu'une branche de travail locale.

## Installation XAMPP / MySQL (branche `isra/mysql-xampp`)

1. Démarrer **Apache** et **MySQL** dans XAMPP.
2. Importer `database/aquaflow_mysql.sql` via phpMyAdmin ou :
   ```powershell
   C:\xampp\mysql\bin\mysql.exe -uroot < database\aquaflow_mysql.sql
   ```
   Cela crée la base `aquaflow`, les 2 tables et 6 comptes de test (mot de passe `Test1234!`).
3. Copier le dossier du projet dans `C:\xampp\htdocs\AquwaFlow`.
4. Ouvrir `http://localhost/AquwaFlow/aquaflow_integration1/index.php?page=signin`.
5. Identifiants : sous forme **e-mail** ou **téléphone** (ex. `admin@aquaflow.tn` / `20000001`).

Connexion dans `app/Models/Model.php` : `127.0.0.1:3306`, base `aquaflow`, utilisateur `root`,
mot de passe vide (valeurs XAMPP). Surcharge possible via `DB_HOST`, `DB_PORT`, `DB_NAME`,
`DB_USER`, `DB_PASS` — aucun identifiant réel n'est versionné.

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
