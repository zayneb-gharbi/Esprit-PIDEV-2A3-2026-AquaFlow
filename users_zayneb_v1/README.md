# AquaFlow — Historique & export des plombiers

Petit module PHP natif / MySQL en architecture MVC, préparé pour WAMP. Le style reprend l'esprit visuel du projet de référence Mariem (barre supérieure, cartes, tableaux, turquoise), sans reprendre ses données de démonstration.

## Fonctionnalités
- Utilisateur : consulter son historique personnel.
- Administrateur : consulter l'historique global, rechercher et filtrer par action/date.
- Administrateur : consulter la liste des plombiers et exporter leurs informations.
- Jeu de données fictif fourni pour les tests.
- Sélection de compte de démonstration pour tester les rôles sans attendre le module d'authentification de l'équipe.

## Installation WAMP
1. Décompresser `AquaFlow_Mon_Module` dans `C:\wamp64\www\`.
2. Démarrer WAMP et attendre l'icône verte.
3. Ouvrir `http://localhost/phpmyadmin/`.
4. Onglet **Importer** > choisir `database/aquaflow.sql` > **Importer / Exécuter**.
5. Vérifier les identifiants MySQL dans `config/database.php` (par défaut WAMP : utilisateur `root`, mot de passe vide).
6. Ouvrir `http://localhost/AquaFlow_Mon_Module/`.
7. Choisir un compte dans la liste. Le choix simule la connexion pour le développement local.

## Comptes de test
Mot de passe fictif pré-haché présent dans la base : `password` (le module n'implémente pas de formulaire de connexion par mot de passe; le sélecteur de rôle est uniquement une facilité de test).
- admin@aquaflow.tn — Administrateur
- ahmed@gmail.com — Citoyen
- mariem@gmail.com — Plombier
- youssef@gmail.com — Citoyen
- sami@gmail.com — Plombier
- nour@gmail.com — Plombier

## Export Excel
Le contrôleur génère un vrai fichier `.xlsx` si PhpSpreadsheet est installé. Dans le dossier du projet, exécuter :

```bash
composer require phpoffice/phpspreadsheet
```

Sans cette dépendance, le bouton télécharge automatiquement un CSV UTF-8 ouvrable dans Excel. Le projet n'inclut pas `vendor/`.

## Notes importantes
- Il s'agit d'un module de démonstration local, pas d'une application prête pour la production.
- Le mode de sélection de compte n'est pas une authentification sécurisée. Lors de l'intégration, remplacer cette partie par la session créée par le vrai contrôleur de connexion de l'équipe.
- Les actions de connexion/déconnexion en mode démo sont ajoutées au journal. Les autres fonctionnalités de l'équipe devront appeler `Historique::ajouter($idUtilisateur, $action, $description)` après leurs opérations réussies.
- Le schéma SQL est autonome et utilise `role(id_role, nom_role)`, `utilisateur(id, nom, prenom, email, mot_de_passe, telephone, id_role, statut, date_creation)` et `historique(...)`.
