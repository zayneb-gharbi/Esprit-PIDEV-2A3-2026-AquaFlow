<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require_once __DIR__ . '/../models/ConfigurationLocale.php';

try {
    $file = $_SERVER['argv'][1] ?? '';
    if ($file === '') {
        echo "Glisse ici le fichier JSON du client Google, puis appuie sur Entree :\n";
        $file = trim(fgets(STDIN) ?: '', " \t\r\n\"'");
    }
    $configuration = new ConfigurationLocale();
    $configuration->importerGoogle($file);
    echo "Identifiants Google enregistres dans config/local.php.\n";
    echo "La configuration existante de la base et de Facebook a ete conservee.\n";
    echo "Ouvre AquaFlow et teste le bouton Google. Aucune connexion reelle n'a encore ete verifiee.\n";
} catch (DomainException $e) {
    fwrite(STDERR, $e->getMessage() . "\n");
    exit(1);
} catch (Throwable $e) {
    fwrite(STDERR, "Import impossible. Verifie le fichier Google et les droits du dossier config.\n");
    exit(1);
}
