<?php
require_once __DIR__ . '/../config/application.php';

class ConfigurationLocale
{
    public function importerGoogle($file)
    {
        if (!is_file($file) || !is_readable($file) || filesize($file) > 65536) {
            throw new DomainException('Choisis le fichier JSON du client Web téléchargé depuis Google.');
        }
        $json = json_decode(file_get_contents($file), true);
        $web = is_array($json) ? ($json['web'] ?? null) : null;
        if (!is_array($web) || !is_string($web['client_id'] ?? null) || !is_string($web['client_secret'] ?? null)
            || !preg_match('/^[a-zA-Z0-9_-]+\.apps\.googleusercontent\.com$/', $web['client_id'])
            || strlen($web['client_secret']) < 16 || strlen($web['client_secret']) > 512) {
            throw new DomainException('Ce fichier ne contient pas un client OAuth Web avec son code secret.');
        }
        $config = amirConfig();
        $retour = rtrim($config['base_url'], '/') . '/index.php?page=oauth_callback&provider=google';
        if (!is_array($web['redirect_uris'] ?? null) || !in_array($retour, $web['redirect_uris'], true)) {
            throw new DomainException('Redirection absente du JSON : ' . $retour . '. Vérifie-la dans Google puis utilise CONFIGURER_CONNEXIONS_WINDOWS.bat si ton JSON est ancien.');
        }
        $config['google_id'] = $web['client_id'];
        $config['google_secret'] = $web['client_secret'];
        $destination = __DIR__ . '/../config/local.php';
        $temporaire = tempnam(dirname($destination), 'oauth_');
        if ($temporaire === false) {
            throw new RuntimeException('Le dossier config doit être accessible en écriture.');
        }
        try {
            chmod($temporaire, 0600);
            if (file_put_contents($temporaire, "<?php\nreturn " . var_export($config, true) . ";\n", LOCK_EX) === false
                || !rename($temporaire, $destination)) {
                throw new RuntimeException('Impossible de sauvegarder config/local.php.');
            }
        } finally {
            if (is_file($temporaire)) {
                unlink($temporaire);
            }
        }
    }
}
