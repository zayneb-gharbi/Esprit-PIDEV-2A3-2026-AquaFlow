<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../config/application.php';

$erreurs = 0;
echo "Diagnostic AquaFlow - aucune cle ni aucun mot de passe ne sera affiche.\n\n";
if (PHP_VERSION_ID < 80100) {
    echo "A CORRIGER : PHP 8.1 ou plus recent est necessaire.\n";
    $erreurs++;
} else {
    echo "OK : PHP " . PHP_VERSION . "\n";
}
foreach (['pdo_mysql', 'openssl', 'curl'] as $extension) {
    if (extension_loaded($extension)) {
        echo "OK : extension " . $extension . "\n";
    } else {
        echo "A CORRIGER : activer l'extension " . $extension . " dans php.ini.\n";
        $erreurs++;
    }
}
try {
    $config = amirConfig();
    echo "\nAdresse du site : " . rtrim($config['base_url'], '/') . "/index.php\n";
    foreach (['google', 'facebook'] as $provider) {
        $present = trim($config[$provider . '_id']) !== '' && trim($config[$provider . '_secret']) !== '';
        echo ucfirst($provider) . ($present ? " : valeurs renseignees, connexion reelle a tester.\n" : " : identifiants manquants dans config/local.php.\n");
        echo "Retour " . $provider . " : " . rtrim($config['base_url'], '/') . '/index.php?page=oauth_callback&provider=' . $provider . "\n";
    }
    if (extension_loaded('pdo_mysql')) {
        require_once __DIR__ . '/../models/UtilisateurPersistant.php';
        $model = new UtilisateurPersistant(AmirDatabase::connexion());
        $stats = $model->getStats();
        echo "\nOK : base accessible, " . (int) $stats['users'] . " compte(s).\n";
    }
    if (is_file(__DIR__ . '/../var/cle.txt')) {
        echo "OK : cle locale de chiffrement presente.\n";
    } else {
        echo "A FAIRE : lancer DEMARRER_WINDOWS.bat pour installer la base et la cle locale.\n";
    }
} catch (Throwable $e) {
    echo "A CORRIGER : verifier config/local.php, MySQL dans XAMPP, puis relancer DEMARRER_WINDOWS.bat.\n";
    $erreurs++;
}
echo "\nCe diagnostic ne remplace pas un essai de connexion avec tes vrais comptes.\n";
exit($erreurs ? 1 : 0);
