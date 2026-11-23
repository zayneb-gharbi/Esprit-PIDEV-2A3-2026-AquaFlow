# AquaFlow — les trois tâches restantes (Amir)

1. **Admin : consulter les détails d’un utilisateur.**
2. **Utilisateur : se connecter avec Google ou Facebook.**
3. **Utilisateur : utiliser une authentification à deux facteurs avec Google Authenticator.**

Ce dossier est une extension de la branche `mariem` du dépôt [Esprit-PIDEV-2A3-2026-AquaFlow](https://github.com/zayneb-gharbi/Esprit-PIDEV-2A3-2026-AquaFlow). Aucun fichier existant du groupe n’est remplacé. Les fichiers de cette contribution sont uniquement dans `aquaflow_demo/amir/`.

## Ouvrir et tester sur Windows

Ouvrir d’abord [COMMENCER_ICI.html](COMMENCER_ICI.html) par double-clic : ce guide indique où cliquer et le résultat attendu pour chaque tâche. Il fonctionne hors ligne.

1. Récupérer la branche de cette contribution, ou ajouter le dossier `amir` dans `aquaflow_demo` de la branche `mariem`.
2. Installer XAMPP avec PHP 8.1 ou supérieur. Ouvrir XAMPP et démarrer **MySQL**.
3. Ouvrir `aquaflow_demo/amir/DEMARRER_WINDOWS.bat` par double-clic.
4. Le fichier privé `var/MES_COMPTES.txt` s’ouvre avec trois comptes de test : administrateur, citoyen et plombier. Copier l’email et le mot de passe du compte voulu.
5. Ouvrir <http://127.0.0.1:8000/amir/public/index.php>. Laisser la fenêtre du serveur ouverte.

PHP doit avoir les extensions `pdo_mysql`, `openssl` et `curl`. Le script utilise `C:\xampp\php\php.exe`, puis `php` s’il ne trouve pas XAMPP. Si ton installation est ailleurs, adapte la variable `AMIR_PHP` du script.

La base créée s’appelle `aquaflow_amir`. Elle contient uniquement les tables préfixées `amir_`. L’installateur ne supprime pas de données et ne réinitialise pas les mots de passe des comptes déjà présents. Les comptes générés sont des comptes de test, avec des adresses fictives et des mots de passe aléatoires.

Pour une connexion MySQL différente, ouvrir `CONFIGURER_CONNEXIONS_WINDOWS.bat`, puis modifier uniquement les valeurs nécessaires dans `config/local.php`. Le fichier réel est exclu de Git et les réglages existants sont conservés. `VERIFIER_WINDOWS.bat` vérifie PHP, les extensions, la base et la présence de la configuration, sans afficher les secrets.

Sur Linux : `bash aquaflow_demo/amir/DEMARRER_LINUX.sh` depuis la racine du dépôt.

## Vérifier les trois fonctionnalités

### 1. Fiche utilisateur côté admin

Se connecter avec le compte administrateur → **Citoyens** ou **Plombiers** → **Consulter**. La fiche contient le nom, l’email, le téléphone, l’adresse, le rôle, le statut, la date de création et l’état des connexions associées/2FA. Les mots de passe et les clés secrètes ne sont jamais affichés.

Se déconnecter, se connecter comme citoyen et essayer l’adresse `index.php?page=details&id=1` : l’accès doit être refusé. Un visiteur est envoyé à la connexion. Un identifiant inexistant renvoie une page 404.

### 2. Google et Facebook

Les deux connexions sont implémentées côté serveur. Pour utiliser de vrais comptes, configurer les applications Google et Meta suivant [docs/GOOGLE_FACEBOOK.md](docs/GOOGLE_FACEBOOK.md). Aucun identifiant réel n’est fourni dans le dépôt. Si le JSON du client Web Google a été conservé lors de sa création, `IMPORTER_GOOGLE_WINDOWS.bat` permet de l’importer sans recopier le secret. Une valeur renseignée ne prouve pas que le fournisseur accepte la configuration : un essai réel reste nécessaire.

Après configuration, utiliser **Google** ou **Facebook** depuis la connexion. Une première connexion crée un compte citoyen ; les connexions suivantes retrouvent ce compte. Le profil, les listes et la fiche admin utilisent la même base persistante.

Pour un compte déjà existant : se connecter avec son mot de passe → **Sécurité** → **Associer Google/Facebook**. L’association doit être effectuée dans les cinq minutes après la connexion. L’email identique d’un compte externe ne suffit jamais à récupérer un compte existant. L’association conserve le rôle du compte ; une nouvelle inscription ne crée jamais un administrateur.

### 3. Google Authenticator

Se connecter → **Sécurité** → **Configurer Google Authenticator**. Dans l’application du téléphone : **+ → Saisir une clé de configuration**, copier la clé affichée et choisir **Basé sur l’heure**. Saisir le code à six chiffres pour activer la protection.

Conserver les huit codes de secours affichés une seule fois. Se déconnecter puis se reconnecter : un code du téléphone ou un code de secours sera demandé, y compris après une connexion Google/Facebook. Chaque code de secours est à usage unique. Un code du téléphone déjà accepté n’est pas réutilisable ; attendre le suivant.

La désactivation nécessite une connexion récente et un code valide. Les clés TOTP sont chiffrées en base. Sauvegarder **privément** la base et `var/cle.txt` ensemble ; ne jamais publier ce fichier.

## Lien avec le travail du groupe

| Élément du groupe | Utilisation par cette contribution |
|---|---|
| `assets/style.css` et `assets/script.js` | Chargés directement : même template, couleurs, tableaux et recherche. |
| `models/Utilisateur.php` | Classe de base étendue par `models/UtilisateurPersistant.php`, avec les mêmes méthodes et champs. |
| `controllers/UserController.php` | Étendu par `AmirProfil` : vues et profil réutilisés ; mise à jour/invalidation des sessions dans le contrôleur de l’extension. |
| `views/user/*.php` | Vues existantes réutilisées sans les modifier ; ajout des jetons de formulaire lors du rendu. |
| `controllers/AdminController.php` | Réutilisé pour les statistiques. |
| Listes citoyens/plombiers | Même présentation, avec ajout du lien vers la fiche détaillée. |

La branche `mariem` utilise actuellement des comptes simulés dans `$_SESSION['demo_users']`. Cette extension leur fournit une version persistante compatible via PDO. Elle démarre avec ses propres comptes de test et n’importe pas automatiquement les simulations. L’ancien `aquaflow_demo/index.php` reste inchangé et reste une démonstration ; ouvrir **`amir/public/index.php`** pour cette version sécurisée. Le lanceur local utilise un routeur qui n’expose que cette entrée et les ressources CSS/JS nécessaires.

Le code `omar_hermi` consulté utilise Oracle et des champs différents. Sa fusion avec la base MySQL commune reste une décision de groupe ; cette contribution ne change pas ce code. La branche `evenement` n’est pas modifiée non plus.

Pour l’intégration finale, adopter ce point d’entrée ou transférer ses contrôles d’accès dans le routeur commun. Toute connexion du groupe, après vérification du mot de passe ou d’un fournisseur externe, doit appeler `AmirAuth::premierFacteur($user)` : la session utilisateur définitive ne doit être créée qu’après la 2FA. Les autres modules doivent demander `AmirAuth::utilisateur()` et contrôler le rôle côté serveur. Ne pas considérer un simple `$_SESSION['user_id']` comme preuve de validation du deuxième facteur.

## Organisation du code

- `models/UtilisateurPersistant.php` : accès PDO aux comptes, compatible avec les méthodes du groupe.
- `models/SecuriteUtilisateur.php` : requêtes PDO pour les tentatives de connexion, les secrets 2FA et les codes de secours.
- `models/ConfigurationLocale.php` : lecture et enregistrement local des identifiants Google importés.
- `controllers/DetailsController.php` : contrôle du rôle et sélection des informations de la fiche.
- `controllers/SocialController.php` : Google/Facebook, vérification de l’identité, association et connexion.
- `controllers/TotpController.php` : codes Google Authenticator, activation, vérification et secours.
- `controllers/AuthController.php` : sessions et contrôle du premier/deuxième facteur ; connexion locale minimale pour tester et associer un compte.
- `controllers/ProfilController.php` : adaptation du contrôleur du groupe pour maintenir les sessions après modification du profil.
- `controllers/ApplicationController.php` : routes, contrôle des accès, traitement des formulaires et choix des vues.
- `public/index.php` : point d’entrée minimal ; charge `config/bootstrap.php` puis lance l’application.
- `views/` : nouveaux écrans dans le template existant.

Les requêtes SQL de l’application se trouvent dans les modèles, avec la connexion PDO dans `config/database.php`. Les modèles ne lisent ni les formulaires HTTP ni les sessions. Les contrôleurs appellent les modèles et fournissent les données aux vues. Les scripts d’installation sous `bin/` sont des outils en ligne de commande, hors du parcours Web.

La connexion locale est un support à ces trois tâches. Cette contribution ne réimplémente ni le changement/réinitialisation du mot de passe, ni l’envoi de mails, ni les historiques, ni l’export Excel.

## Vérifications

**140 vérifications passent** : 81 contrôles métier/OAuth/architecture et 59 contrôles HTTP, sous **PHP 8.4.27 WebAssembly avec SQLite temporaire**. Les réponses Google/Facebook sont simulées dans les tests ; aucun vrai compte externe n’a été utilisé. Le schéma et l’installation MySQL sont fournis mais n’ont pas été exécutés sur XAMPP dans cet environnement. Les lanceurs Windows restent à exécuter sur le PC de validation.

```bash
cd aquaflow_demo/amir/tests
npm ci
npm test
```

Le rapport est dans [docs/resultats-tests.json](docs/resultats-tests.json). Les tests vérifient notamment les rôles, les identifiants invalides, CSRF, les signatures/claims Google, les jetons Facebook, les vecteurs RFC 6238, l’anti-rejeu, les codes de secours, les sessions et les pages du groupe réutilisées. Ils couvrent aussi l’import JSON Google sans écrasement des autres réglages, les transactions d’association sociale et la séparation SQL/contrôleurs/vues.

## Intégration Git

La branche de contribution est `amir/details-oauth-2fa`, basée sur `mariem` (`15daf591659c21646198c71a33006b0310459384`). La pull request doit cibler **`mariem`** pour que les fichiers dont dépend cette extension soient présents. Une fusion globale vers `main` sera ensuite faite par le groupe.

Ne pas ajouter `config/local.php`, `var/MES_COMPTES.txt`, `var/cle.txt`, des fichiers de base locale ou `tests/node_modules`. Le `.gitignore` du dossier les exclut.

Pour mettre à jour la branche de contribution déjà publiée :

Ouvrir un terminal dans une copie locale propre du dépôt et récupérer la branche :

```bash
git switch amir/details-oauth-2fa
git pull --ff-only
```

Ensuite, copier le dossier `aquaflow_demo/amir` de la nouvelle archive dans cette copie du dépôt. Conserver les éventuels `config/local.php` et `var/` privés présents sur le PC. Vérifier et publier les modifications :

```bash
git status --short
git add aquaflow_demo/amir
git diff --cached --stat
git commit -m "fix(amir): séparer MVC et guider les tests OAuth et 2FA"
git push origin amir/details-oauth-2fa
```

Les seules modifications attendues sont sous `aquaflow_demo/amir/`. Ne pas fusionner automatiquement les travaux du groupe. Si la mise à jour est déjà publiée, `git pull --ff-only` suffit ; aucun nouveau commit n’est nécessaire.

Pour un premier dépôt sur une autre copie locale de `mariem`, utiliser un nouveau nom de branche :

```bash
git switch mariem
git switch -c amir/integration-details-oauth-2fa
git add aquaflow_demo/amir
git commit -m "feat(amir): détails utilisateur, Google/Facebook et double authentification"
git push -u origin amir/integration-details-oauth-2fa
```

Si le compte n’a pas le droit d’écrire dans le dépôt du groupe, utiliser un fork et ouvrir une pull request vers `mariem`, ou demander un accès collaborateur à la propriétaire.
