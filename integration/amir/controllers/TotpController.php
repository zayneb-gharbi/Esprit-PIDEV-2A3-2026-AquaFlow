<?php
require_once __DIR__ . '/../models/SecuriteUtilisateur.php';

class AmirTotp
{

    public static function base32($bytes)
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split($bytes) as $byte) {
            $bits .= str_pad(decbin(ord($byte)), 8, '0', STR_PAD_LEFT);
        }
        $result = '';
        foreach (str_split($bits, 5) as $part) {
            $result .= $alphabet[bindec(str_pad($part, 5, '0'))];
        }
        return $result;
    }

    private static function decoder($secret)
    {
        $bits = '';
        foreach (str_split($secret) as $char) {
            $index = strpos('ABCDEFGHIJKLMNOPQRSTUVWXYZ234567', $char);
            if ($index === false) {
                throw new DomainException('Clé incorrecte.');
            }
            $bits .= str_pad(decbin($index), 5, '0', STR_PAD_LEFT);
        }
        $bytes = '';
        foreach (str_split($bits, 8) as $part) {
            if (strlen($part) === 8) {
                $bytes .= chr(bindec($part));
            }
        }
        return $bytes;
    }

    public static function code($secret, $step, $digits = 6)
    {
        $counter = pack('N2', (int) floor($step / 4294967296), $step % 4294967296);
        $hash = hash_hmac('sha1', $counter, self::decoder($secret), true);
        $offset = ord($hash[19]) & 15;
        $number = unpack('N', substr($hash, $offset, 4))[1] & 0x7fffffff;
        return str_pad((string) ($number % (10 ** $digits)), $digits, '0', STR_PAD_LEFT);
    }

    private static function cle()
    {
        $file = __DIR__ . '/../var/cle.txt';
        $key = is_file($file) ? trim(file_get_contents($file)) : '';
        if (!preg_match('/^[a-f0-9]{64}$/', $key)) {
            throw new DomainException('L’installation de la sécurité doit être terminée.');
        }
        return hex2bin($key);
    }

    public static function chiffrer($secret)
    {
        $iv = random_bytes(12);
        $tag = '';
        $data = openssl_encrypt($secret, 'aes-256-gcm', self::cle(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($data === false) {
            throw new RuntimeException('Chiffrement indisponible.');
        }
        return base64_encode($iv . $tag . $data);
    }

    public static function dechiffrer($value)
    {
        $data = base64_decode($value, true);
        if ($data === false || strlen($data) < 29) {
            throw new RuntimeException('Clé illisible.');
        }
        $secret = openssl_decrypt(substr($data, 28), 'aes-256-gcm', self::cle(), OPENSSL_RAW_DATA, substr($data,
            0, 12), substr($data, 12, 16));
        if ($secret === false) {
            throw new RuntimeException('Clé illisible.');
        }
        return $secret;
    }

    private static function trouver($secret, $code, $last = -1)
    {
        if (!is_string($code) || !preg_match('/^[0-9]{6}$/', $code)) {
            return null;
        }
        $now = intdiv(time(), 30);
        for ($i = -1; $i <= 1; $i++) {
            $step = $now + $i;
            if ($step > $last && hash_equals(self::code($secret, $step), $code)) {
                return $step;
            }
        }
        return null;
    }

    public static function preparer($user)
    {
        AmirAuth::recente();
        if ($user['totp_secret']) {
            throw new DomainException('La double authentification est déjà activée.');
        }
        $_SESSION['setup_totp'] = ['id' => (int) $user['id'], 'version' => (int) $user['version'], 'secret' => self::base32(random_bytes(20)),
            'expires' => time() + 600];
    }

    public static function activer($user, $code)
    {
        $setup = $_SESSION['setup_totp'] ?? [];
        if (($setup['expires'] ?? 0) < time() || ($setup['id'] ?? 0) !== (int) $user['id'] || ($setup['version'] ?? 0) !== (int) $user['version']
            || $user['totp_secret']) {
            throw new DomainException('La préparation a expiré. Recommence.');
        }
        AmirAuth::limiter('totp-setup-' . $user['id']);
        $step = self::trouver($setup['secret'], $code);
        if ($step === null) {
            throw new DomainException('Code incorrect. Vérifie l’heure de ton téléphone.');
        }
        $codes = [];
        $empreintes = [];
        for ($i = 0; $i < 8; $i++) {
            $code = bin2hex(random_bytes(8));
            $codes[] = $code;
            $empreintes[] = hash('sha256', $code);
        }
        $securite = new SecuriteUtilisateur(AmirDatabase::connexion());
        $securite->activer2fa($user['id'], $user['version'], self::chiffrer($setup['secret']), $step, $empreintes);
        unset($_SESSION['setup_totp'], $_SESSION['oauth']);
        $_SESSION['codes_secours'] = $codes;
        $_SESSION['auth']['version'] = (int) $user['version'] + 1;
    }

    public static function verifier($user, $code)
    {
        AmirAuth::limiter('totp-' . $user['id']);
        $securite = new SecuriteUtilisateur(AmirDatabase::connexion());
        $current = $securite->utilisateurActif($user['id'], $user['version']);
        if (!$current || !$current['totp_secret']) {
            throw new DomainException('Reconnecte-toi pour continuer.');
        }
        if (is_string($code) && preg_match('/^[a-f0-9]{16}$/', $code)) {
            if ($securite->consommerSecours($user['id'], $user['version'], hash('sha256', $code))) {
                return;
            }
        }
        $step = self::trouver(self::dechiffrer($current['totp_secret']), $code, (int) $current['totp_last']);
        if ($step !== null) {
            if ($securite->consommerTotp($user['id'], $user['version'], $current['totp_secret'], $step)) {
                return;
            }
        }
        throw new DomainException('Code incorrect ou déjà utilisé. Attends le prochain code.');
    }

    public static function desactiver($user, $code)
    {
        AmirAuth::recente();
        self::verifier($user, $code);
        $securite = new SecuriteUtilisateur(AmirDatabase::connexion());
        $securite->desactiver2fa($user['id'], $user['version']);
        $_SESSION['auth']['version'] = (int) $user['version'] + 1;
        unset($_SESSION['codes_secours'], $_SESSION['setup_totp'], $_SESSION['oauth']);
    }
}
