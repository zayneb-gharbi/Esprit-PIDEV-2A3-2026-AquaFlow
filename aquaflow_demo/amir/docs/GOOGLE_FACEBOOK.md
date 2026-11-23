# Configuration Google et Facebook

Ouvrir [COMMENCER_ICI.html](../COMMENCER_ICI.html) pour le parcours de test détaillé par étapes. Les identifiants d’application se renseignent localement dans `config/local.php`, jamais dans Git ni dans une conversation. `CONFIGURER_CONNEXIONS_WINDOWS.bat` ouvre ce fichier en conservant ses valeurs existantes.

## Adresse du site

Par défaut : `http://127.0.0.1:8000/amir/public`.

Les URL de retour doivent correspondre exactement à `base_url` et au fournisseur :

- Google : `http://127.0.0.1:8000/amir/public/index.php?page=oauth_callback&provider=google`
- Facebook : `http://127.0.0.1:8000/amir/public/index.php?page=oauth_callback&provider=facebook`

Meta impose HTTPS pour les sites de connexion. L’acceptation d’une adresse locale dépend des possibilités de test proposées par Meta ; elle n’a pas été vérifiée pour cette application. Si la console refuse l’adresse locale, utiliser une URL HTTPS acceptée, puis remplacer le début des deux redirections dans la configuration et les consoles des fournisseurs. Le lanceur fourni ne crée pas de service HTTPS. Toujours ouvrir le site avec la même adresse que celle définie par `base_url` pour conserver les cookies de session lors du retour.

## Google

1. Dans [Google Cloud Console](https://console.cloud.google.com/auth/clients), ouvrir le projet AquaFlow et le client Web déjà créé, ou créer un projet/client s’il n’existe pas encore.
2. Configurer Google Auth Platform et l’écran de consentement. Si le projet reste en test, ajouter les comptes de test autorisés.
3. Créer un client OAuth pour une **application Web**.
4. Ajouter l’URL de retour Google exacte dans les URI de redirection autorisés.
5. Copier le Client ID dans `google_id` et le Client Secret dans `google_secret` du fichier local. Le secret complet n’est visible et téléchargeable qu’au moment de sa création. S’il a été perdu, utiliser la rotation de secret du client pour en créer un nouveau, puis le conserver immédiatement.
6. Relancer le site puis essayer le bouton Google, l’annulation du consentement et la reconnexion au même compte.

Les permissions demandées sont `openid email profile`. Le serveur vérifie la signature RS256, l’émetteur, le client destinataire, l’expiration, le nonce et la vérification de l’email. Le flux inclut `state` et PKCE.

### Import du JSON Google

Si le fichier JSON du client Web a été conservé lors de la création : ouvrir `IMPORTER_GOOGLE_WINDOWS.bat`, glisser le JSON dans la fenêtre puis appuyer sur Entrée. Sur Linux : `php aquaflow_demo/amir/bin/importer_google.php /chemin/vers/client_secret.json`.

L’import vérifie le type Web, l’identifiant, le secret et la présence de la redirection exacte dans le JSON. Il enregistre les valeurs dans `config/local.php` et conserve les réglages MySQL/Facebook. Si le JSON ne contient pas la redirection à jour, vérifier celle-ci dans Google et utiliser la saisie manuelle. L’import n’effectue pas une connexion Google et ne valide pas les identifiants auprès du fournisseur.

## Facebook

1. Ouvrir [Meta for Developers](https://developers.facebook.com/apps/) et l’application du groupe adaptée à Facebook Login.
2. Configurer le cas d’utilisation de connexion et les réglages Facebook Login.
3. Ajouter l’URL de retour Facebook exacte parmi les redirections OAuth autorisées.
4. Copier l’App ID dans `facebook_id` et l’App Secret dans `facebook_secret` du fichier local.
5. En mode développement, utiliser des comptes ayant un rôle autorisé dans l’application. La mise à disposition au public peut nécessiter la validation de l’application et des informations supplémentaires demandées par Meta.
6. Garder une version Graph API prise en charge. Le code utilise `v26.0`, configurable via `facebook_version`.

Le serveur échange le code, vérifie le jeton (`app_id`, validité, expiration et utilisateur), puis récupère le profil avec `appsecret_proof`. Un email absent n’empêche pas la connexion : l’identité Facebook repose sur son identifiant stable.

## Association et 2FA

Pour associer un compte social à un compte local, utiliser **Sécurité → Associer** dans les cinq minutes après une connexion complète. L’association n’est jamais déduite d’un email identique. Si une 2FA existe déjà, toute connexion sociale doit encore passer par l’écran du code.

## Vérification sur le PC

`VERIFIER_WINDOWS.bat` contrôle PHP, les extensions et la base. Il affiche les adresses de retour exactes et indique si les identifiants sont renseignés, sans les révéler. Tester ensuite chaque fournisseur : nouvelle connexion, reconnexion au même compte, annulation, association explicite et demande du deuxième facteur si la 2FA est activée. Les tests automatiques utilisent des réponses simulées et ne remplacent pas ces essais.

## Sources officielles

- [Google OpenID Connect](https://developers.google.com/identity/openid-connect/openid-connect)
- [Google OAuth pour applications Web](https://developers.google.com/identity/protocols/oauth2/web-server)
- [Gestion des clients et secrets Google](https://support.google.com/cloud/answer/15549257?hl=en)
- [Configuration Google Auth Platform](https://support.google.com/cloud/answer/15544987?hl=en)
- [Facebook — flux de connexion manuel](https://developers.facebook.com/documentation/facebook-login/guides/advanced/manual-flow)
- [Versions de Graph API](https://developers.facebook.com/docs/graph-api/changelog/versions/)
- [Facebook Login : exigence HTTPS](https://developers.facebook.com/docs/facebook-login/changelog/)
- [RFC 6238 — TOTP](https://www.rfc-editor.org/rfc/rfc6238)
