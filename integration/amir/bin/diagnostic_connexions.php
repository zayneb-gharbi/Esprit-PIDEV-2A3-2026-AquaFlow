<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/application.php';
require_once __DIR__ . '/../controllers/SocialController.php';

echo "Diagnostic Google/Facebook - aucun mot de passe ni jeton affiche.\n\n";
echo 'PHP : ' . PHP_VERSION . PHP_EOL;
echo 'Configuration PHP : ' . (php_ini_loaded_file() ?: 'aucun php.ini charge') . PHP_EOL;
if (!extension_loaded('curl')) {
    echo "ERREUR : extension PHP curl absente.\n";
    exit(1);
}
$version = curl_version();
echo 'Bibliotheque TLS de PHP : ' . ($version['ssl_version'] ?? 'inconnue') . PHP_EOL;
$ca = ini_get('curl.cainfo');
echo 'curl.cainfo : ' . ($ca === '' ? 'non defini' : (is_readable($ca) ? 'fichier lisible' : 'fichier absent ou illisible')) . PHP_EOL;

try {
    $config = amirConfig();
    foreach (['google', 'facebook'] as $provider) {
        echo "\nRetour " . ucfirst($provider) . ' a enregistrer : ' . AmirSocial::callback($provider) . PHP_EOL;
        echo 'Identifiants : ' . (trim($config[$provider . '_id']) !== '' && trim($config[$provider . '_secret']) !== ''
            ? 'renseignes (acceptation par le fournisseur non verifiee)' : 'manquants') . PHP_EOL;
    }
    echo "\nTests HTTPS publics, sans identifiants ni connexion a un compte :\n";
    foreach (['Google' => 'https://www.googleapis.com/oauth2/v1/certs', 'Facebook' => 'https://graph.facebook.com/'] as $provider => $url) {
        $curl = curl_init($url);
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2]);
        curl_exec($curl);
        $numero = curl_errno($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        if ($numero === 0 && $status >= 100) {
            echo $provider . ' : transport HTTPS OK (HTTP ' . $status . "). Cela ne valide pas la connexion OAuth.\n";
        } else {
            echo $provider . ' : ECHEC du transport HTTPS (cURL ' . $numero . ', HTTP ' . $status . ").\n";
            if (in_array($numero, [60, 77], true)) {
                echo "Les certificats de confiance de PHP sont a verifier. Ne pas desactiver la verification TLS.\n";
            }
        }
    }
    $journal = __DIR__ . '/../var/oauth_diagnostic.log';
    if (is_file($journal)) {
        $lignes = file($journal, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        echo "\nDerniers diagnostics des connexions reelles :\n";
        foreach (array_slice($lignes ?: [], -5) as $ligne) {
            echo $ligne . PHP_EOL;
        }
    }
} catch (Throwable $erreur) {
    echo "Diagnostic interrompu. Verifier la syntaxe de config/local.php et base_url.\n";
    exit(1);
}
echo "\nEnvoie le resultat de cette fenetre. Aucun identifiant secret n'a ete affiche.\n";
