<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}
require_once __DIR__ . '/https_configuration.php';

function amirCommande($commande, $journal)
{
    $processus = proc_open($commande, [0 => STDIN, 1 => ['file', $journal, 'a'], 2 => ['file', $journal, 'a']], $pipes, null, null, ['bypass_shell' => true]);
    if (!is_resource($processus) || proc_close($processus) !== 0) {
        throw new RuntimeException('Une commande a echoue. Consulter var/https/lanceur.log.');
    }
}

function amirPortLibre($port)
{
    $socket = @stream_socket_server('tcp://127.0.0.1:' . $port, $code, $message);
    if ($socket === false) {
        throw new RuntimeException('Le port ' . $port . ' est deja utilise. Fermer le precedent lanceur HTTPS puis reessayer.');
    }
    fclose($socket);
}

function amirAttendreServeur($processus, $port)
{
    for ($i = 0; $i < 100; $i++) {
        if (!proc_get_status($processus)['running']) {
            throw new RuntimeException('Le serveur ne demarre pas. Consulter var/https/lanceur.log.');
        }
        $socket = @stream_socket_client('tcp://127.0.0.1:' . $port, $code, $message, 0.1);
        if ($socket !== false) {
            fclose($socket);
            return;
        }
        usleep(100000);
    }
    throw new RuntimeException('Le serveur ne repond pas. Consulter var/https/lanceur.log.');
}

$processus = [];
register_shutdown_function(function () use (&$processus) {
    foreach (array_reverse($processus) as $serveur) {
        if (is_resource($serveur)) {
            if (proc_get_status($serveur)['running']) {
                proc_terminate($serveur);
            }
            proc_close($serveur);
        }
    }
});

try {
    if (PHP_OS_FAMILY !== 'Windows' || PHP_VERSION_ID < 80100) {
        throw new RuntimeException('Utiliser ce lanceur sur Windows avec PHP 8.1 ou plus recent.');
    }
    if (!extension_loaded('openssl') || !function_exists('proc_open')) {
        throw new RuntimeException('Activer openssl et proc_open dans PHP.');
    }
    $amir = dirname(__DIR__);
    $configuration = $amir . '/config/local.php';
    $phpCgi = dirname(PHP_BINARY) . '/php-cgi.exe';
    $systeme = getenv('SystemRoot') ?: 'C:\\Windows';
    $curl = $systeme . '/System32/curl.exe';
    $certutil = $systeme . '/System32/certutil.exe';
    foreach ([$configuration, $phpCgi, $curl, $certutil, dirname($amir) . '/assets/style.css'] as $fichier) {
        if (!is_file($fichier)) {
            throw new RuntimeException('Fichier introuvable : ' . $fichier . '. Ajouter ce complement au dossier amir deja installe.');
        }
    }
    amirPortLibre(8443);
    amirPortLibre(9001);
    $prive = $amir . '/var/https';
    if (!is_dir($prive) && !mkdir($prive, 0700, true)) {
        throw new RuntimeException('Impossible de creer var/https.');
    }
    $journal = $prive . '/lanceur.log';
    file_put_contents($journal, 'Demarrage HTTPS ' . date('c') . PHP_EOL);
    chmod($journal, 0600);
    $caddy = $prive . '/caddy.exe';
    if (!is_file($caddy)) {
        echo "Telechargement de Caddy depuis caddyserver.com. Patienter...\n";
        amirCommande([$curl, '--fail', '--location', '--proto', '=https', '--proto-redir', '=https', '--max-time', '240', '--retry', '2', '--output', $caddy . '.part', 'https://caddyserver.com/api/download?os=windows&arch=amd64'], $journal);
        if (file_get_contents($caddy . '.part', false, null, 0, 2) !== 'MZ' || !rename($caddy . '.part', $caddy)) {
            throw new RuntimeException('Telechargement Caddy invalide. Relancer le fichier.');
        }
    }
    amirCommande([$caddy, 'version'], $journal);
    putenv('AMIR_SITE_ROOT=' . str_replace('\\', '/', dirname($amir)));
    putenv('AMIR_HTTPS_STORAGE=' . str_replace('\\', '/', $prive . '/data'));
    $caddyfile = $amir . '/config/Caddyfile';
    amirCommande([$caddy, 'validate', '--config', $caddyfile, '--adapter', 'caddyfile'], $journal);

    putenv('PHP_FCGI_MAX_REQUESTS=0');
    $argumentsPhp = [$phpCgi];
    if (php_ini_loaded_file()) {
        array_push($argumentsPhp, '-c', php_ini_loaded_file());
    }
    array_push($argumentsPhp, '-d', 'cgi.fix_pathinfo=0', '-d', 'display_errors=0', '-d', 'log_errors=1', '-b', '127.0.0.1:9001');
    foreach ([$argumentsPhp, [$caddy, 'run', '--config', $caddyfile, '--adapter', 'caddyfile']] as $commande) {
        $serveur = proc_open($commande, [0 => ['file', 'NUL', 'r'], 1 => ['file', $journal, 'a'], 2 => ['file', $journal, 'a']], $pipes, null, null, ['bypass_shell' => true, 'create_process_group' => true]);
        if (!is_resource($serveur)) {
            throw new RuntimeException('Impossible de lancer le serveur.');
        }
        $processus[] = $serveur;
    }
    amirAttendreServeur($processus[0], 9001);
    amirAttendreServeur($processus[1], 8443);
    $certificat = $prive . '/data/pki/authorities/local/root.crt';
    if (!is_file($certificat)) {
        throw new RuntimeException('Le certificat local est absent. Consulter var/https/lanceur.log.');
    }
    $empreinte = openssl_x509_fingerprint(file_get_contents($certificat), 'sha1');
    if (!$empreinte) {
        throw new RuntimeException('Le certificat local est invalide.');
    }
    $verification = proc_open([$certutil, '-user', '-store', 'Root', $empreinte], [0 => STDIN, 1 => ['file', $journal, 'a'], 2 => ['file', $journal, 'a']], $pipes, null, null, ['bypass_shell' => true]);
    $dejaInstalle = is_resource($verification) && proc_close($verification) === 0;
    if (!$dejaInstalle) {
        echo "\nUn certificat AquaFlow local sera ajoute aux autorites de confiance\n";
        echo "de ton compte Windows pour permettre HTTPS dans Chrome/Edge.\n";
        echo "Le site reste accessible uniquement sur ce PC.\n";
        echo "Garde le dossier var/https prive : il contient la cle du certificat.\n";
        echo "Installer ce certificat ? Tape O puis Entree, ou Entree pour annuler : ";
        if (strtoupper(trim(fgets(STDIN) ?: '')) !== 'O') {
            echo "Annule. config/local.php n'a pas ete modifie.\n";
            exit;
        }
        amirCommande([$certutil, '-user', '-addstore', 'Root', $certificat], $journal);
        amirCommande([$certutil, '-user', '-store', 'Root', $empreinte], $journal);
    }
    $adresse = 'https://127.0.0.1:8443/amir/public';
    amirCommande([$curl, '--noproxy', '*', '--fail', '--silent', '--show-error', '--retry', '3', '--retry-all-errors', '--retry-delay', '1', '--max-time', '10', '--cacert', $certificat, '--ssl-revoke-best-effort', '--output', 'NUL', $adresse . '/security.css'], $journal);
    amirConfigurerHttps($configuration, $prive);
    echo "\nHTTPS est pret. Garde cette fenetre ouverte et MySQL demarre dans XAMPP.\n\n";
    echo "Ouvre dans Chrome :\n" . $adresse . "/index.php?page=login\n\n";
    echo "Retour Facebook a enregistrer chez Meta :\n" . $adresse . "/index.php?page=oauth_callback&provider=facebook\n\n";
    echo "Retour Google a ajouter dans le client Google :\n" . $adresse . "/index.php?page=oauth_callback&provider=google\n\n";
    echo "Les identifiants existants sont conserves. La sauvegarde privee est dans var/https.\n";
    echo "Pour arreter les deux serveurs proprement, appuie sur Entree ici.\n";
    fgets(STDIN);
} catch (Throwable $erreur) {
    fwrite(STDERR, "\n" . $erreur->getMessage() . "\n");
    exit(1);
}
