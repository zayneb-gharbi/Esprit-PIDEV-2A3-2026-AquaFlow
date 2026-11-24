<?php
class DiagnosticFixture extends AmirSocial
{
    public function lire($url, $data, $status, $numero = 0)
    {
        return $this->reponseExterne($url, $data, $status, $numero);
    }
}
$diagnostic = new DiagnosticFixture(null, null);
$fichierDiagnostic = __DIR__ . '/../var/oauth_diagnostic.log';
if (is_file($fichierDiagnostic)) { unlink($fichierDiagnostic); }
check($diagnostic->lire('https://graph.facebook.com/v26.0/me', '{"id":"123"}', 200) === ['id' => '123'], 'Diagnostic : une reponse valide reste acceptee');
$essaisDiagnostic = [
    ['https://graph.facebook.com/v26.0/oauth/access_token?code=SECRET_QUERY', false, 0, 60, 'certificat HTTPS distant (cURL 60)'],
    ['https://graph.facebook.com/v26.0/debug_token?input_token=SECRET_TOKEN', '{"error":{"code":190,"error_subcode":463,"message":"SECRET_MESSAGE"}}', 400, 0, 'code 190, sous-code 463'],
    ['https://oauth2.googleapis.com/token', '{"error":"invalid_client","error_description":"SECRET_CLIENT"}', 401, 0, 'invalid_client'],
    ['https://www.googleapis.com/oauth2/v1/certs', '{"private":"SECRET_BODY"}', 503, 0, 'HTTP 503'],
    ['https://graph.facebook.com/v26.0/me', 'SECRET_HTML', 200, 0, 'HTTP 200'],
    ['https://graph.facebook.com/v26.0/me', false, 0, 28, 'cURL 28'],
    ['https://graph.facebook.com/v26.0/me', false, 0, 77, 'cURL 77'],
    ['https://oauth2.googleapis.com/token', '{"error":"SECRET_ERROR"}', 400, 0, 'HTTP 400'],
    ['https://graph.facebook.com/v26.0/oauth/access_token?client_secret=SECRET_QUERY', '{"error":{"code":1,"is_transient":false,"message":"Error validating client secret: SECRET_VALUE"}}', 400, 0, 'non temporaire selon Meta, clé secrète de l’application à vérifier'],
    ['https://graph.facebook.com/v26.0/oauth/access_token', '{"error":{"code":1,"is_transient":true,"message":"An unknown error SECRET_DETAILS"}}', 400, 0, 'code 1, temporaire selon Meta'],
    ['https://graph.facebook.com/v26.0/oauth/access_token', '{"error":{"code":100,"message":"redirect_uri invalid https://SECRET_SITE/?code=SECRET_CODE"}}', 400, 0, 'adresse de retour à vérifier'],
    ['https://graph.facebook.com/v26.0/oauth/access_token', '{"error":{"code":100,"message":"Error validating verification code SECRET_CODE"}}', 400, 0, 'code de connexion à renouveler'],
    ['https://graph.facebook.com/v26.0/oauth/access_token', '{"error":{"code":1,"message":["SECRET_ARRAY"],"is_transient":"SECRET_BOOL"}}', 400, 0, 'HTTP 400, code 1']
];
foreach ($essaisDiagnostic as [$url, $body, $status, $numero, $attendu]) {
    try {
        $diagnostic->lire($url, $body, $status, $numero);
        throw new RuntimeException('La reponse invalide doit etre refusee');
    } catch (DomainException $erreur) {
        check(str_contains($erreur->getMessage(), $attendu) && !str_contains($erreur->getMessage(), 'SECRET_'), 'Diagnostic detaille sans secret : ' . $attendu);
    }
}
$journalDiagnostic = file_get_contents($fichierDiagnostic);
check(!str_contains($journalDiagnostic, 'SECRET_') && !str_contains($journalDiagnostic, 'https://')
    && str_contains($journalDiagnostic, 'cURL 60') && str_contains($journalDiagnostic, 'code 190'), 'Journal OAuth sans jeton, code de connexion, URL ou corps de reponse');
