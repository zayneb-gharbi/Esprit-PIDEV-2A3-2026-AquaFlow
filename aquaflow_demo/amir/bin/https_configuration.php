<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

function amirConfigurerHttps($destination, $dossierSauvegarde)
{
    $config = require $destination;
    if (!is_array($config)) {
        throw new RuntimeException('config/local.php doit retourner un tableau PHP.');
    }
    $adresse = 'https://127.0.0.1:8443/amir/public';
    if (($config['base_url'] ?? '') === $adresse) {
        return;
    }
    $sauvegarde = $dossierSauvegarde . '/local-avant-https-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.php';
    if (!copy($destination, $sauvegarde)) {
        throw new RuntimeException('Impossible de sauvegarder la configuration. Aucun changement applique.');
    }
    chmod($sauvegarde, 0600);
    $config['base_url'] = $adresse;
    $temporaire = tempnam(dirname($destination), 'oauth_');
    if ($temporaire === false) {
        throw new RuntimeException('Impossible de preparer la configuration HTTPS.');
    }
    try {
        chmod($temporaire, 0600);
        $contenu = "<?php\nreturn " . var_export($config, true) . ";\n";
        if (file_put_contents($temporaire, $contenu, LOCK_EX) !== strlen($contenu)
            || !rename($temporaire, $destination)) {
            throw new RuntimeException('Impossible de remplacer config/local.php. La sauvegarde reste dans var/https.');
        }
    } finally {
        if (is_file($temporaire)) {
            unlink($temporaire);
        }
    }
}
