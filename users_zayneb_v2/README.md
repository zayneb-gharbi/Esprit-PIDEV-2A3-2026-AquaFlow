# AquaFlow — Module utilisateurs (Version 2)

Module PHP natif / MySQL en architecture MVC pour WAMP. Cette version reste autonome pour les tests et utilise la base `aquaflow` fournie dans `database/aquaflow.sql`.

## Fonctionnalités V2
- Historique personnel : recherche texte, filtre par action et date (avant / après / pendant), pagination.
- Historique global Admin : recherche utilisateur/email/action/description, filtres rôle/action et date (avant / après / pendant), pagination et statistiques.
- Liste des plombiers : recherche, filtre par statut et export de la sélection.
- Export : vrai fichier `.xlsx` formaté si PhpSpreadsheet est installé ; sinon CSV UTF-8 avec BOM, ouvrable dans Excel.
- Enregistrement réutilisable des activités via `Historique::ajouter(id, action, description)`.
- Connexion/déconnexion de démonstration enregistrées automatiquement.

## Installation WAMP
1. Extraire le dossier dans `C:\wamp64\www\users_zayneb_v2` (ou autre nom).
2. Importer `database/aquaflow.sql` dans phpMyAdmin. **Attention :** ce script recrée les tables de la base `aquaflow` et efface les anciennes tables de démonstration portant ces noms. Sauvegarder avant réimport si nécessaire.
3. Vérifier `config/database.php` (par défaut WAMP : `root`, mot de passe vide).
4. Pour un vrai `.xlsx`, ouvrir un terminal dans le dossier du projet et exécuter `composer install`. Composer doit être installé. Sans Composer, le bouton exporte un CSV compatible Excel.
5. Ouvrir `http://localhost/users_zayneb_v2/` (adapter le chemin au nom du dossier).

## Comptes de démonstration
- `admin@aquaflow.tn` — Administrateur
- `ahmed@gmail.com` — Citoyen
- `mariem@gmail.com` — Plombier
- `youssef@gmail.com` — Citoyen
- `sami@gmail.com` — Plombier
- `nour@gmail.com` — Plombier

Le sélecteur de compte simule une connexion locale ; il ne s'agit pas d'une authentification de production.

## Tests conseillés
1. Se connecter en Ahmed : rechercher `Connexion`, filtrer une date, passer aux pages suivantes si assez d'entrées.
2. Se connecter en Admin : rechercher `Ahmed`, filtrer par rôle `citoyen`, choisir une action et une date en mode Avant / Après / Pendant.
3. Depuis la page Plombiers, tester les deux boutons d’export : plombiers et citoyens.
3. Ouvrir Plombiers : chercher `Mariem`, filtrer par statut, exporter la sélection.
4. Vérifier que les liens de pagination conservent les filtres.

## Intégration future
Les autres contrôleurs peuvent enregistrer une action après une opération réussie :

```php
$historiqueModel->ajouter($idUtilisateur, 'Modification du profil', 'Téléphone mis à jour');
```

À l'intégration, remplacer le sélecteur de compte démo par la vraie session d'authentification. Ne pas committer les vrais identifiants de base de données.
