<?php
class AmirSocial
{
    private $model;
    private $auth;

    public function __construct($model, $auth) {
        $this->model = $model;
        $this->auth = $auth;
    }

    public static function disponible($provider)
    {
        $config = amirConfig();
        return in_array($provider, ['google', 'facebook'], true) && $config[$provider . '_id'] !== '' && $config[$provider . '_secret'] !== '';
    }

    public static function callback($provider)
    {
        $base = amirConfig()['base_url'];
        $parts = parse_url($base);
        if (!$parts || !isset($parts['scheme'], $parts['host']) || isset($parts['query']) || isset($parts['fragment'])
            || isset($parts['user']) || ($parts['scheme'] !== 'https' && !($parts['scheme'] === 'http' && in_array($parts['host'],
            ['localhost', '127.0.0.1'], true)))) {
            throw new DomainException('Configure une adresse HTTPS ou localhost valide.');
        }
        return rtrim($base, '/') . '/index.php?page=oauth_callback&provider=' . $provider;
    }

    public function commencer($provider, $user)
    {
        if (!self::disponible($provider)) {
            throw new DomainException('Ce service n’est pas encore configuré.');
        }
        if ($user) {
            AmirAuth::recente();
        }
        $config = amirConfig();
        $state = bin2hex(random_bytes(32));
        $_SESSION['oauth'] = ['provider' => $provider, 'state' => $state, 'expires' => time() + 600, 'id' => $user ? (int) $user['id'] : null,
            'version' => $user ? (int) $user['version'] : null];
        $params = ['client_id' => $config[$provider . '_id'], 'redirect_uri' => self::callback($provider), 'state' => $state,
            'response_type' => 'code'];
        if ($provider === 'google') {
            $url = 'https://accounts.google.com/o/oauth2/v2/auth';
            $params['scope'] = 'openid email profile';
            $params['prompt'] = 'select_account';
            $params['nonce'] = bin2hex(random_bytes(32));
            $verifier = bin2hex(random_bytes(32));
            $_SESSION['oauth']['nonce'] = $params['nonce'];
            $_SESSION['oauth']['verifier'] = $verifier;
            $params['code_challenge'] = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'),
                '=');
            $params['code_challenge_method'] = 'S256';
        }
        else {
            if (!preg_match('/^v[0-9]+\.[0-9]+$/', $config['facebook_version'])) {
                throw new DomainException('Version Facebook invalide.');
            }
            $url = 'https://www.facebook.com/' . $config['facebook_version'] . '/dialog/oauth';
            $params['scope'] = 'email,public_profile';
        }
        return $url . '?' . http_build_query($params);
    }

    protected function json($url, $post = null, $bearer = null)
    {
        if (!function_exists('curl_init')) {
            throw new DomainException('Active l’extension PHP curl.');
        }
        $curl = curl_init($url);
        $headers = ['Accept: application/json'];
        if ($bearer !== null) {
            $headers[] = 'Authorization: Bearer ' . $bearer;
        }
        curl_setopt_array($curl, [CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 8, CURLOPT_TIMEOUT => 15,
                CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROTOCOLS => CURLPROTO_HTTPS, CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2]);
        if ($post !== null) {
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($post));
        }
        $data = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $numero = curl_errno($curl);
        curl_close($curl);
        return $this->reponseExterne($url, $data, $status, $numero);
    }

    protected function reponseExterne($url, $data, $status, $numero)
    {
        $json = is_string($data) ? json_decode($data, true) : null;
        if ($numero === 0 && $status === 200 && is_array($json) && !isset($json['error'])) {
            return $json;
        }
        $provider = parse_url($url, PHP_URL_HOST) === 'graph.facebook.com' ? 'Facebook' : 'Google';
        $path = parse_url($url, PHP_URL_PATH) ?: '';
        $etape = str_ends_with($path, '/access_token') || str_ends_with($path, '/token') ? 'échange du code' : 'vérification du compte';
        if (str_ends_with($path, '/debug_token')) {
            $etape = 'vérification du jeton';
        } elseif (str_ends_with($path, '/me')) {
            $etape = 'lecture du profil';
        }
        $message = $provider . ' — ' . $etape . ' : ';
        if ($numero !== 0) {
            $raisons = [6 => 'serveur introuvable', 7 => 'connexion au serveur impossible',
                28 => 'délai dépassé', 35 => 'connexion TLS impossible',
                60 => 'PHP ne peut pas vérifier le certificat HTTPS distant',
                77 => 'fichier de certificats PHP absent ou illisible'];
            $message .= ($raisons[$numero] ?? 'erreur de communication') . ' (cURL ' . (int) $numero . ').';
        } else {
            $message .= 'réponse refusée ou invalide (HTTP ' . (int) $status;
            $erreur = is_array($json) ? ($json['error'] ?? null) : null;
            if (is_array($erreur) && is_int($erreur['code'] ?? null)) {
                $message .= ', code ' . $erreur['code'];
                if (is_int($erreur['error_subcode'] ?? null)) {
                    $message .= ', sous-code ' . $erreur['error_subcode'];
                }
                if ($provider === 'Facebook') {
                    if (is_bool($erreur['is_transient'] ?? null)) {
                        $message .= $erreur['is_transient'] ? ', temporaire selon Meta' : ', non temporaire selon Meta';
                    }
                    $description = is_string($erreur['message'] ?? null) ? strtolower($erreur['message']) : '';
                    if (str_contains($description, 'client secret')) {
                        $message .= ', clé secrète de l’application à vérifier';
                    } elseif (str_contains($description, 'redirect_uri') || str_contains($description, 'redirect uri')) {
                        $message .= ', adresse de retour à vérifier';
                    } elseif (str_contains($description, 'verification code')) {
                        $message .= ', code de connexion à renouveler';
                    }
                }
            } elseif (is_string($erreur) && in_array($erreur, ['invalid_client', 'invalid_grant', 'invalid_request',
                    'invalid_scope', 'unauthorized_client', 'access_denied', 'redirect_uri_mismatch',
                    'temporarily_unavailable', 'server_error'], true)) {
                $message .= ', ' . $erreur;
            }
            $message .= ').';
        }
        @file_put_contents(__DIR__ . '/../var/oauth_diagnostic.log', date('c') . ' ' . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
        throw new DomainException($message);
    }

    private static function decode64($value)
    {
        $result = base64_decode(strtr($value, '-_', '+/'), true);
        if ($result === false) {
            throw new DomainException('Identité Google invalide.');
        }
        return $result;
    }

    public function verifierGoogle($token, $nonce)
    {
        if (!is_string($token) || strlen($token) > 16000) {
            throw new DomainException('Identité Google invalide.');
        }
        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new DomainException('Identité Google invalide.');
        }
        $header = json_decode(self::decode64($parts[0]), true);
        $claims = json_decode(self::decode64($parts[1]), true);
        if (!is_array($header) || !is_array($claims) || ($header['alg'] ?? '') !== 'RS256' || !is_string($header['kid'] ?? null)) {
            throw new DomainException('Identité Google invalide.');
        }
        $certs = $this->json('https://www.googleapis.com/oauth2/v1/certs');
        $cert = $certs[$header['kid']] ?? null;
        if (!is_string($cert) || openssl_verify($parts[0] . '.' . $parts[1], self::decode64($parts[2]), $cert, OPENSSL_ALGO_SHA256) !== 1) {
            throw new DomainException('Signature Google invalide.');
        }
        $client = amirConfig()['google_id'];
        if (!in_array($claims['iss'] ?? '', ['accounts.google.com', 'https://accounts.google.com'], true) || ($claims['aud'] ?? '') !== $client
            || (isset($claims['azp']) && $claims['azp'] !== $client) || !is_int($claims['exp'] ?? null) || $claims['exp'] <= time()
            || !is_int($claims['iat'] ?? null) || $claims['iat'] > time() + 60 || !is_string($claims['nonce'] ?? null)
            || !hash_equals($nonce, $claims['nonce']) || ($claims['email_verified'] ?? false) !== true) {
            throw new DomainException('Identité Google expirée ou non vérifiée.');
        }
        return $claims;
    }

    public function retour($get, $user)
    {
        $session = $_SESSION['oauth'] ?? [];
        unset($_SESSION['oauth']);
        $provider = champ($get, 'provider', 20);
        $state = champ($get, 'state', 64);
        if (!self::disponible($provider) || ($session['provider'] ?? '') !== $provider || ($session['expires'] ?? 0) < time()
            || $state === '' || !hash_equals($session['state'] ?? '', $state)) {
            throw new DomainException('Connexion externe expirée. Recommence.');
        }
        if (isset($get['error'])) {
            throw new DomainException('Connexion externe annulée.');
        }
        $code = champ($get, 'code', 4096);
        if ($code === '') {
            throw new DomainException('Code de connexion absent.');
        }
        $config = amirConfig();
        $params = ['client_id' => $config[$provider . '_id'], 'client_secret' => $config[$provider . '_secret'],
            'redirect_uri' => self::callback($provider), 'code' => $code];
        if ($provider === 'google') {
            $params['grant_type'] = 'authorization_code';
            $params['code_verifier'] = $session['verifier'];
            $token = $this->json('https://oauth2.googleapis.com/token', $params);
            $info = $this->verifierGoogle($token['id_token'] ?? null, $session['nonce']);
            $subject = $info['sub'] ?? '';
        }
        else {
            $base = 'https://graph.facebook.com/' . $config['facebook_version'];
            $token = $this->json($base . '/oauth/access_token?' . http_build_query($params, '', '&', PHP_QUERY_RFC3986));
            $access = $token['access_token'] ?? null;
            if (!is_string($access) || $access === '') {
                throw new DomainException('Jeton Facebook absent.');
            }
            $debug = $this->json($base . '/debug_token?' . http_build_query(['input_token' => $access]), null, $config['facebook_id'] . '|' . $config['facebook_secret']);
            $data = $debug['data'] ?? [];
            if (($data['is_valid'] ?? false) !== true || (string) ($data['app_id'] ?? '') !== (string) $config['facebook_id']
                || ($data['type'] ?? '') !== 'USER' || !isset($data['expires_at']) || ($data['expires_at'] != 0
                && $data['expires_at'] <= time()) || (isset($data['data_access_expires_at']) && $data['data_access_expires_at'] != 0
                && $data['data_access_expires_at'] <= time())) {
                throw new DomainException('Identité Facebook non vérifiée.');
            }
            $info = $this->json($base . '/me?' . http_build_query(['fields' => 'id,name,email', 'appsecret_proof' => hash_hmac('sha256',
                    $access, $config['facebook_secret'])]), null, $access);
            $subject = $info['id'] ?? '';
            if ((string) $subject !== (string) ($data['user_id'] ?? '')) {
                throw new DomainException('Identité Facebook incohérente.');
            }
        }
        if (!is_string($subject) || $subject === '' || strlen($subject) > 190) {
            throw new DomainException('Identifiant externe invalide.');
        }
        return $this->associer($provider, $subject, $info, $session, $user);
    }

    private function associer($provider, $subject, $info, $session, $user)
    {
        $id = $this->model->idSocial($provider, $subject);
        if ($session['id'] !== null) {
            if (!$user || (int) $user['id'] !== $session['id'] || (int) $user['version'] !== $session['version']) {
                throw new DomainException('Reconnecte-toi avant d’associer ce service.');
            }
            AmirAuth::recente();
            if ($id !== false && (int) $id !== (int) $user['id']) {
                throw new DomainException('Cette identité est déjà associée à un autre compte.');
            }
            if ($id === false) {
                $this->model->associerSocial($user['id'], $provider, $subject);
            }
            $_SESSION['message'] = 'Compte externe associé.';
            return 'security';
        }
        if ($user) {
            throw new DomainException('Déconnecte-toi avant de changer de compte.');
        }
        if ($id !== false) {
            return $this->auth->premierFacteur($this->model->getById((int) $id));
        }
        $email = isset($info['email']) ? strtolower(champ($info, 'email', 190)) : null;
        if ($email !== null && $this->model->getByEmail($email)) {
            throw new DomainException('Ce compte existe déjà. Connecte-toi avec ton mot de passe, puis associe le service dans Sécurité.');
        }
        $nom = champ($info, 'name', 150);
        if ($nom === '') {
            $nom = 'Utilisateur AquaFlow';
        }
        $created = $this->model->creerSocial($nom, $email, $provider, $subject);
        return $this->auth->premierFacteur($created);
    }
}
