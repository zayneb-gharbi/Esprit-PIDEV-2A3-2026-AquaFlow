# Intégration Amir + Yasmine + Zayneb + Omar + Mariem

Cette branche commune part de `zayneb`, qui contient déjà l'intégration Omar, Zayneb et Mariem. Le dossier `integration/` y ajoute le travail d'Amir et de Yasmine dans une seule application. Les dossiers originaux de cette branche ne sont pas remplacés.

Cette version relie la connexion, le changement et la réinitialisation du mot de passe de Yasmine aux détails utilisateur, à Google/Facebook et à Google Authenticator d'Amir. Une même table `utilisateur` et une seule session sont utilisées. La version intégrée de Zayneb contient déjà l'inscription et l'approbation d'Omar, les listes/statistiques et la désactivation de Mariem, ainsi que les historiques et exports de Zayneb. Ses contrôleurs et son template sont conservés dans la copie `groupe/`. Les adaptations de connexion PDO et de pagination utilisent MySQL dans une base séparée ; les versions Oracle originales restent intactes. Les dossiers d'origine et les branches du groupe restent intacts : toutes les adaptations sont dans `integration/`.

## Démarrer sur Windows

1. Extraire/cloner cette branche. Démarrer **MySQL** dans XAMPP.
2. Ouvrir `integration/amir/DEMARRER_WINDOWS.bat`. Le programme crée une base séparée `aquaflow_integration_groupe`, sans supprimer les anciennes bases. Les comptes de démonstration générés sont dans `integration/amir/var/MES_COMPTES.txt`.
3. Ouvrir <http://127.0.0.1:8000/amir/public/index.php>. Garder le terminal ouvert.
4. Pour Google et Facebook, utiliser `integration/amir/CONFIGURER_CONNEXIONS_WINDOWS.bat`, puis le lanceur HTTPS du **même dossier**. Fermer l'ancien serveur avant d'utiliser celui-ci. `config/local.php` contient les identifiants locaux et reste exclu de Git.

Le serveur HTTPS reprend l'adresse `https://127.0.0.1:8443/amir/public`. Les retours OAuth restent :

- `https://127.0.0.1:8443/amir/public/index.php?page=oauth_callback&provider=google`
- `https://127.0.0.1:8443/amir/public/index.php?page=oauth_callback&provider=facebook`

Recopier seulement les paramètres Google/Facebook de l'ancienne configuration dans la nouvelle. Garder le nom de base propre à l'intégration. Ne pas écraser l'ancien dossier, sa base ou sa clé 2FA. Les comptes existants ne sont pas automatiquement importés dans cette base séparée.

## Emails de Yasmine

Dans `integration`, exécuter `composer install`. Copier `yasmine/config/mail.local.example.php` en `yasmine/config/mail.local.php`, renseigner SMTP et désactiver `MAIL_DEBUG` pour envoyer de vrais emails. Retirer une éventuelle constante `APP_URL` de ce fichier : l'adresse est celle de `amir/config/local.php`.

Sans configuration SMTP, le transport de développement écrit les notifications dans `yasmine/storage/mails.log`. Ce fichier est privé, ignoré par Git et inaccessible par le serveur. Les tests vérifient ce transport local, pas la livraison dans une boîte mail réelle.

## Vérifier chaque fonction

| Parcours | Vérification |
| --- | --- |
| Connexion | Se connecter avec chacun des comptes de démonstration. |
| Détails utilisateur | Avec l'administrateur, ouvrir **Citoyens** ou **Plombiers**, puis cliquer sur le nom. Un citoyen doit être refusé s'il saisit cette adresse. |
| Modifier le profil | **Mon espace → Modifier mes informations** ; vérifier les valeurs après rechargement. |
| Mot de passe | **Mot de passe** ; entrer l'ancien mot de passe puis le nouveau et sa confirmation. Se reconnecter avec le nouveau. |
| Mot de passe oublié | Depuis la connexion, demander le lien, l'ouvrir, choisir un nouveau mot de passe. Le lien ne peut servir qu'une fois. |
| Google / Facebook | Depuis la connexion, choisir le fournisseur configuré. Pour retrouver un compte existant, se connecter d'abord à ce compte puis utiliser **Sécurité → Associer**. Une adresse email identique n'entraîne pas une association automatique. |
| Google Authenticator | **Sécurité → Configurer**, saisir la clé dans Google Authenticator, confirmer le code. Conserver les codes de secours. Se déconnecter puis vérifier que le deuxième code est demandé après le mot de passe ou OAuth. |
| Désactiver le compte | Depuis l'espace d'un citoyen/plombier, désactiver son compte puis vérifier que la connexion est refusée. |

Un nouveau compte social est citoyen. Facebook sans adresse email peut créer un compte ; renseigner une adresse email personnelle avant d'utiliser la récupération de mot de passe. La réinitialisation du mot de passe conserve la 2FA et déconnecte les anciennes sessions.

## Architecture et adaptations

- PHP MVC et PDO. `amir/public/index.php` est le seul point d'entrée servi.
- `yasmine/` contient les formulaires et le traitement des mots de passe. Les adaptations de cette copie raccordent le modèle commun et le premier facteur d'Amir, invalident les anciennes sessions et rendent les jetons de réinitialisation atomiquement utilisables une seule fois.
- `amir/` conserve la vérification des identités OAuth, les codes TOTP, les codes de secours et les détails réservés à l'administrateur.
- Les secrets OAuth, les mots de passe et les clés de chiffrement ne sont pas versionnés. La clé `amir/var/cle.txt` doit accompagner toute sauvegarde des comptes avec 2FA.
- Les versions sources et les empreintes des fichiers avant adaptation sont dans `sources.json`.

## Tests

Dans `integration/tests` : `npm install`, puis `npm test`. PHP 8.4 WebAssembly et une base SQLite temporaire permettent de tester sans toucher aux bases locales. Les services OAuth sont simulés et les emails restent dans un journal privé de test. Résultats : `tests/resultats-groupe.json`.

Les essais avec Google/Facebook réels, le SMTP réel et le lanceur Windows doivent être réalisés sur la machine de validation avec sa configuration privée. Ne pas utiliser les anciens points d'entrée à côté de celui-ci pour tester l'intégration.

## Reprendre les comptes existants

Les anciennes bases ne sont jamais écrasées. Arrêter le serveur avant l'import et conserver une sauvegarde des bases et de `amir/var/cle.txt`. Pour une base cible vide, lancer `php amir/bin/installer.php` sans `--demo`, depuis le dossier `integration`.

1. Copier `amir/config/import.example.php` en `amir/config/import.local.php`. Renseigner les connexions locales aux sources souhaitées. `type` vaut `amir`, `yasmine` ou `groupe` ; `nom` doit rester identique lors des essais suivants. Une source d'intégration Amir + Yasmine utilise le type `yasmine` et sa clé `amir/var/cle.txt`. Une source de groupe utilise `groupe` et la table `utilisateur`.
2. Lancer `php amir/bin/importer_comptes.php` pour vérifier : cette simulation ne conserve aucune insertion.
3. Si la vérification réussit, lancer `php amir/bin/importer_comptes.php --appliquer`.

Les mots de passe hachés, rôles, statuts, profils, dates, identités Google/Facebook, secrets TOTP et codes de secours sont repris avec une correspondance des identifiants. L'historique est repris dans la version groupe. Une seconde exécution ne recrée pas les comptes déjà importés. Un email en double ou une clé TOTP incohérente annule l'import entier, sans fusion automatique. Les anciennes sessions et les liens de réinitialisation en cours doivent être renouvelés. Le pilote PDO correspondant à la base source doit être installé ; une source Oracle nécessite PDO OCI.

## Parcours du groupe à vérifier

| Fonction conservée | Où la vérifier |
| --- | --- |
| Inscription citoyen/plombier (Omar) | Connexion → Créer un compte. Un plombier reste en attente. |
| Approbation/refus (Omar) | Administrateur → Approbations. |
| Listes, recherche, désactivation, statistiques (Mariem) | Administrateur → Citoyens / Plombiers / Statistiques. |
| Profil et désactivation personnelle (Mariem) | Mon profil. |
| Historique personnel (Zayneb) | Mon historique, limité au compte connecté. |
| Historique global, filtres et pagination (Zayneb) | Administrateur → Historique global. |
| Export plombiers/citoyens (Zayneb) | Bouton Exporter en Excel dans chaque liste. XLSX avec Composer ; CSV compatible Excel sans la bibliothèque. |

Sources consultées : Yasmine `323ffb0`, Amir `5c51821`, Zayneb `02fa10a`, Omar `cbccd5f`, Mariem `b300ec5` (archive `aquaflow_integration_finale.zip`). Les différences d'Omar et Mariem ont été comparées à l'intégration de Zayneb : les parcours correspondants sont conservés. Le module événements n'est pas inclus dans cette intégration demandée.

Validation locale : tests HTTP/métier avec SQLite, retours OAuth signés simulés et emails en journal privé. L'export XLSX a également été exercé avec PhpSpreadsheet 3.10.10 fourni par la branche Zayneb ; ces dépendances ne sont pas recopiées dans Git. `tests/resultats-http-natif.json` décrit les contrôles du serveur PHP natif sous Linux. MySQL, Windows, Google/Meta réels et la livraison SMTP restent à vérifier sur la machine du groupe.
