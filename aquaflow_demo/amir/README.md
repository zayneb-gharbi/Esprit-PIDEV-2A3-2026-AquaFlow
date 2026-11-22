# AquaFlow — les trois tâches restantes (Amir)

1. **Admin : consulter les détails d’un utilisateur.**
2. **Utilisateur : se connecter avec Google ou Facebook.**
3. **Utilisateur : utiliser une authentification à deux facteurs avec Google Authenticator.**

Ce dossier est une extension de la branche `mariem` du dépôt [Esprit-PIDEV-2A3-2026-AquaFlow](https://github.com/zayneb-gharbi/Esprit-PIDEV-2A3-2026-AquaFlow). Aucun fichier existant du groupe n’est remplacé. Les fichiers de cette contribution sont uniquement dans `aquaflow_demo/amir/`.

## Ouvrir et tester sur Windows

1. Récupérer la branche de cette contribution, ou ajouter le dossier `amir` dans `aquaflow_demo` de la branche `mariem`.
2. Installer XAMPP avec PHP 8.1 ou supérieur. Ouvrir XAMPP et démarrer **MySQL**.
3. Ouvrir `aquaflow_demo/amir/DEMARRER_WINDOWS.bat` par double-clic.
4. Le fichier privé `var/MES_COMPTES.txt` s’ouvre avec trois comptes de test : administrateur, citoyen et plombier. Copier l’email et le mot de passe du compte voulu.
5. Ouvrir <http://127.0.0.1:8000/amir/public/index.php>. Laisser la fenêtre du serveur ouverte.

PHP doit avoir les extensions `pdo_mysql`, `openssl` et `curl`. Le script utilise `C:\xampp\php\php.exe`, puis `php` s’il ne trouve pas XAMPP. Si ton installation est ailleurs, adapte la variable `AMIR_PHP` du script.

La base créée s’appelle `aquaflow_amir`. Elle contient uniquement les tables préfixées `amir_`. L’installateur ne supprime pas de données et ne réinitialise pas les mots de passe des comptes déjà présents. Les comptes générés sont des comptes de test, avec des adresses fictives et des mots de passe aléatoires.

Pour une connexion MySQL différente, copier `config/local.example.php` en `config/local.php`, puis modifier uniquement les valeurs nécessaires. Le fichier réel est exclu de Git.

Sur Linux : `bash aquaflow_demo/amir/DEMARRER_LINUX.sh` depuis la racine du dépôt.

## Vérifier les trois fonctionnalités

### 1. Fiche utilisateur côté admin

Se connecter avec le compte administrateur → **Citoyens** ou **Plombiers** → **Consulter**. La fiche contient le nom, l’email, le téléphone, l’adresse, le rôle, le statut, la date de création et l’état des connexions associées/2FA. Les mots de passe et les clés secrètes ne sont jamais affichés.

Se déconnecter, se connecter comme citoyen et essayer l’adresse `index.php?page=details&id=1` : l’accès doit être refusé. Un visiteur est envoyé à la connexion. Un identifiant inexistant renvoie une page 404.

### 2. Google et Facebook

Les deux connexions sont implémentées côté serveur. Pour utiliser de vrais comptes, il faut d’abord configurer les applications Google et Meta du groupe, suivant [docs/GOOGLE_FACEBOOK.md](docs/GOOGLE_FACEBOOK.md). Aucun identifiant réel n’est fourni dans le dépôt.

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
| `controllers/UserController.php` | Réutilisé pour le profil, la modification et la désactivation du compte. |
| `views/user/*.php` | Vues existantes réutilisées sans les modifier ; ajout des jetons de formulaire lors du rendu. |
| `controllers/AdminController.php` | Réutilisé pour les statistiques. |
| Listes citoyens/plombiers | Même présentation, avec ajout du lien vers la fiche détaillée. |

La branche `mariem` utilise actuellement des comptes simulés dans `$_SESSION['demo_users']`. Cette extension leur fournit une version persistante compatible via PDO. Elle démarre avec ses propres comptes de test et n’importe pas automatiquement les simulations. L’ancien `aquaflow_demo/index.php` reste inchangé et reste une démonstration ; ouvrir **`amir/public/index.php`** pour cette version sécurisée. Le lanceur local utilise un routeur qui n’expose que cette entrée et les ressources CSS/JS nécessaires.

Le code `omar_hermi` consulté utilise Oracle et des champs différents. Sa fusion avec la base MySQL commune reste une décision de groupe ; cette contribution ne change pas ce code. La branche `evenement` n’est pas modifiée non plus.

Pour l’intégration finale, adopter ce point d’entrée ou transférer ses contrôles d’accès dans le routeur commun. Toute connexion du groupe, après vérification du mot de passe ou d’un fournisseur externe, doit appeler `AmirAuth::premierFacteur($user)` : la session utilisateur définitive ne doit être créée qu’après la 2FA. Les autres modules doivent demander `AmirAuth::utilisateur()` et contrôler le rôle côté serveur. Ne pas considérer un simple `$_SESSION['user_id']` comme preuve de validation du deuxième facteur.

## Organisation du code

- `models/UtilisateurPersistant.php` : accès PDO aux comptes, compatible avec les méthodes du groupe.
- `controllers/DetailsController.php` : contrôle du rôle et sélection des informations de la fiche.
- `controllers/SocialController.php` : Google/Facebook, vérification de l’identité, association et connexion.
- `controllers/TotpController.php` : codes Google Authenticator, activation, vérification et secours.
- `controllers/AuthController.php` : sessions et contrôle du premier/deuxième facteur ; connexion locale minimale pour tester et associer un compte.
- `public/index.php` : routes, contrôle des accès, traitement des formulaires et réutilisation des pages du groupe.
- `views/` : nouveaux écrans dans le template existant.

La connexion locale est un support à ces trois tâches. Cette contribution ne réimplémente ni le changement/réinitialisation du mot de passe, ni l’envoi de mails, ni les historiques, ni l’export Excel.

## Vérifications

**118 vérifications passent** : 62 contrôles métier/OAuth et 56 contrôles HTTP, sous **PHP 8.4.27 WebAssembly avec SQLite temporaire**. Les réponses Google/Facebook sont simulées dans les tests ; aucun vrai compte externe n’a été utilisé. Le schéma et l’installation MySQL sont fournis mais n’ont pas été exécutés sur XAMPP dans cet environnement.

```bash
cd aquaflow_demo/amir/tests
npm ci
npm test
```

Le rapport est dans [docs/resultats-tests.json](docs/resultats-tests.json). Les tests vérifient notamment les rôles, les identifiants invalides, CSRF, les signatures/claims Google, les jetons Facebook, les vecteurs RFC 6238, l’anti-rejeu, les codes de secours, les sessions et les pages du groupe réutilisées.

## Intégration Git

La branche de contribution est `amir/details-oauth-2fa`, basée sur `mariem` (`15daf591659c21646198c71a33006b0310459384`). La pull request doit cibler **`mariem`** pour que les fichiers dont dépend cette extension soient présents. Une fusion globale vers `main` sera ensuite faite par le groupe.

Ne pas ajouter `config/local.php`, `var/MES_COMPTES.txt`, `var/cle.txt`, des fichiers de base locale ou `tests/node_modules`. Le `.gitignore` du dossier les exclut.

Pour déposer le dossier manuellement sur une copie locale de `mariem` :

```bash
git switch mariem
git switch -c amir/details-oauth-2fa
git add aquaflow_demo/amir
git commit -m "feat(amir): détails utilisateur, Google/Facebook et double authentification"
git push -u origin amir/details-oauth-2fa
```

Si le compte n’a pas le droit d’écrire dans le dépôt du groupe, utiliser un fork et ouvrir une pull request vers `mariem`, ou demander un accès collaborateur à la propriétaire.
