# AquaFlow — Intégration module Utilisateurs + Historique + Export

Cette version combine le projet MVC d'Omar et Mariem avec le module de Zayneb (historique personnel/global et export Excel), en conservant le projet principal, ses vues et sa connexion Oracle.

## Base de données
- Connexion conservée dans `app/Models/Model.php` : Oracle `//localhost:1521/XE`, utilisateur `AQUAFLOW`.
- Ne pas importer le fichier MySQL `aquaflow.sql` de l'ancien module autonome.
- Exécuter une seule fois `database/historique_oracle.sql` dans le schéma Oracle `AQUAFLOW`. Cette migration ajoute la table `HISTORIQUE`, sa séquence, son trigger et ses index sans supprimer les tables existantes.

## Fonctionnalités intégrées
- Historique personnel pour chaque utilisateur connecté.
- Historique global réservé à l'administrateur.
- Recherche, filtre par action/date/rôle et pagination.
- Enregistrement automatique des connexions, déconnexions, créations de comptes, approbations/refus et désactivations.
- Export Excel `.xlsx` des listes citoyens/plombiers depuis les pages correspondantes. Si PhpSpreadsheet n'est pas chargé, export CSV compatible Excel.

## Installation
1. Faire une copie de sauvegarde du dossier actuel.
2. Extraire le contenu de `aquaflowmvc` dans `C:\wamp64\www\aquaflowmvc` (ou garder le nom de dossier choisi).
3. Vérifier la connexion Oracle dans `app/Models/Model.php`.
4. Exécuter `database/historique_oracle.sql` dans Oracle SQL Developer connecté avec le compte `AQUAFLOW`.
5. Démarrer WAMP et ouvrir `http://localhost/aquaflowmvc/aquaflow/`.

## Liens
- `index.php?page=personal-history` : historique personnel.
- `index.php?page=global-history` : historique global (admin uniquement).
- Les pages `citoyens` et `plombiers` contiennent le bouton d'export Excel.
- `README_ISRA_USER_MANAGEMENT.md` : blocage / déblocage, désactivation volontaire et modification du profil.

## À savoir
- L'export reprend les colonnes existantes du modèle Oracle : `id_user`, `nom`, `prenom`, `email`, `telephone`, `statut`, `date_creation`, `role`.
- Le projet dépend toujours de PDO_OCI/Oracle Instant Client côté WAMP.
- Les journaux ne bloquent pas les opérations principales si la table historique n'a pas encore été créée : l'erreur est envoyée au journal PHP.
- Les identifiants Oracle ne doivent pas être publiés dans un dépôt Git public.
