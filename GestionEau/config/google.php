<?php
/**
 * config/google.php
 * Connexion avec Google (OAuth 2.0). Les vraies clés vont dans config/google.local.php (non versionné).
 * Requiert config/mail.php chargé avant (pour APP_URL).
 */
if (is_file(__DIR__ . '/google.local.php')) {
    require __DIR__ . '/google.local.php';
}
defined('GOOGLE_CLIENT_ID')     or define('GOOGLE_CLIENT_ID', '');
defined('GOOGLE_CLIENT_SECRET') or define('GOOGLE_CLIENT_SECRET', '');
defined('GOOGLE_CA_BUNDLE')     or define('GOOGLE_CA_BUNDLE', ''); // chemin de cacert.pem si erreur SSL cURL sous Windows
define('GOOGLE_REDIRECT_URI', APP_URL . '/index.php?action=google_callback');
