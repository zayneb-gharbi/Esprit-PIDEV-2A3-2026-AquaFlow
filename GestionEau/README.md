# GestionEau — Authentification (MVC)

Fonctions : connexion (email + mot de passe), connexion avec Google, changer mot de passe,
mot de passe oublié (lien par email), emails de notification.

## Installation locale (XAMPP)
1. Importer `sql/schema.sql` dans phpMyAdmin.
2. Dossier dans `C:\xampp\htdocs\GestionEau`.
3. Toujours utiliser `http://localhost/GestionEau/` (pas 127.0.0.1).

## Emails réels (Gmail)
1. Mot de passe d'application Google (validation en 2 étapes requise).
2. PHPMailer : soit `composer require phpmailer/phpmailer`, soit copier le dossier `src` de PHPMailer dans `lib/PHPMailer/src`.
3. Copier `config/mail.local.example.php` en `config/mail.local.php` et le remplir.

## Connexion avec Google
1. Créer un identifiant OAuth (type « Application Web ») dans Google Cloud Console.
2. URI de redirection autorisée : `http://localhost/GestionEau/index.php?action=google_callback`
3. Copier `config/google.local.example.php` en `config/google.local.php` et mettre Client ID / Secret.

`mail.local.php` et `google.local.php` sont ignorés par Git : ne jamais les publier.

## Routes
login · google_login · google_callback · logout · forgot_password · reset_password&token=… · change_password · dashboard · admin
