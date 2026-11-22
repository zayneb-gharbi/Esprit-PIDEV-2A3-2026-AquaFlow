# Configuration Google et Facebook

Le code ne peut pas créer les identifiants d’application du groupe. Les renseigner localement dans `config/local.php`, jamais dans Git ni dans une conversation.

## Adresse du site

Par défaut : `http://127.0.0.1:8000/amir/public`.

Les URL de retour doivent correspondre exactement à `base_url` et au fournisseur :

- Google : `http://127.0.0.1:8000/amir/public/index.php?page=oauth_callback&provider=google`
- Facebook : `http://127.0.0.1:8000/amir/public/index.php?page=oauth_callback&provider=facebook`

Pour une URL de démonstration HTTPS, remplacer le début par l’URL réelle du groupe, dans la configuration et dans la console du fournisseur. Facebook peut imposer HTTPS selon la configuration de l’application. Ne pas publier le serveur local ou créer un tunnel contenant des données réelles sans décider de l’hébergement avec le groupe.

## Google

1. Dans [Google Cloud Console](https://console.cloud.google.com/), ouvrir ou créer le projet du groupe.
2. Configurer Google Auth Platform et l’écran de consentement. Si le projet reste en test, ajouter les comptes de test autorisés.
3. Créer un client OAuth pour une **application Web**.
4. Ajouter l’URL de retour Google exacte dans les URI de redirection autorisés.
5. Copier le Client ID dans `google_id` et le Client Secret dans `google_secret` du fichier local.
6. Relancer le site puis essayer le bouton Google, l’annulation du consentement et la reconnexion au même compte.

Les permissions demandées sont `openid email profile`. Le serveur vérifie la signature RS256, l’émetteur, le client destinataire, l’expiration, le nonce et la vérification de l’email. Le flux inclut `state` et PKCE.

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

## Sources officielles consultées le 22 novembre 2026

- [Google OpenID Connect](https://developers.google.com/identity/openid-connect/openid-connect)
- [Google OAuth pour applications Web](https://developers.google.com/identity/protocols/oauth2/web-server)
- [Facebook — flux de connexion manuel](https://developers.facebook.com/documentation/facebook-login/guides/advanced/manual-flow)
- [Versions de Graph API](https://developers.facebook.com/docs/graph-api/changelog/versions/)
- [RFC 6238 — TOTP](https://www.rfc-editor.org/rfc/rfc6238)
