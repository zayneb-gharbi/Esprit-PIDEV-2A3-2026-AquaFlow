<?php
require_once __DIR__ . '/../models/UtilisateurPersistant.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/TotpController.php';
require_once __DIR__ . '/../controllers/SocialController.php';
require_once __DIR__ . '/../controllers/DetailsController.php';
AmirAuth::demarrer();
$checks = [];
function check($condition, $label) { global $checks; if (!$condition) { throw new RuntimeException($label); } $checks[] = $label; }
function refuse($fn, $label) { try { $fn(); } catch (DomainException $e) { check(true, $label); return; } throw new RuntimeException($label); }
$db = AmirDatabase::connexion();
$db->exec(file_get_contents(__DIR__ . '/sqlite.sql'));
$model = new UtilisateurPersistant($db);
$auth = new AmirAuth($model);
$admin = $model->create('Administrateur','admin@test.test','TestMotDePasse42!',3);
$citizen = $model->create('Citoyen','citoyen@test.test','TestMotDePasse42!',1);
$plumber = $model->create('Plombier','plombier@test.test','TestMotDePasse42!',2);
check(password_verify('TestMotDePasse42!', $citizen['password']), 'Mots de passe hachés');
$details = new AmirDetails($model);
refuse(fn() => $details->consulter($citizen, '1'), 'Citoyen interdit sur les détails');
refuse(fn() => $details->consulter($plumber, '1'), 'Plombier interdit sur les détails');
refuse(fn() => $details->consulter(null, '1'), 'Anonyme interdit sur les détails');
$d = $details->consulter($admin, (string) $citizen['id']);
check($d['nom']==='Citoyen' && !isset($d['password'],$d['totp_secret']), 'Fiche admin sans secrets');
refuse(fn() => $details->consulter($admin, '1 OR 1=1'), 'Identifiant injecté refusé');
refuse(fn() => $details->consulter($admin, '999999'), 'Identifiant absent refusé');
refuse(fn() => $details->consulter($admin, ['1']), 'Identifiant tableau refusé');
$secret = AmirTotp::base32('12345678901234567890');
foreach ([59=>'94287082',1111111109=>'07081804',1111111111=>'14050471',1234567890=>'89005924',2000000000=>'69279037',20000000000=>'65353130'] as $time=>$code) {
    check(AmirTotp::code($secret,intdiv($time,30),8)===$code, 'Vecteur RFC 6238 : '.$time);
}
$cipher = AmirTotp::chiffrer($secret);
check($cipher !== $secret && AmirTotp::dechiffrer($cipher)===$secret, 'Secret 2FA chiffré et déchiffré');
check($auth->premierFacteur($citizen)==='profile', 'Premier facteur sans TOTP');
AmirTotp::preparer($citizen);
$setup = $_SESSION['setup_totp']['secret'];
refuse(fn() => AmirTotp::activer($citizen,'bad'), 'Activation refusée sans code valide');
$now = intdiv(time(),30);
AmirTotp::activer($citizen,AmirTotp::code($setup,$now));
$codes = $_SESSION['codes_secours'];
$citizen = $model->getById($citizen['id']);
check(count($codes)===8 && $citizen['totp_secret']!==$setup, 'Activation et huit codes de secours');
check($db->query('SELECT empreinte FROM amir_secours')->fetchColumn()!==$codes[0], 'Secours hachés en base');
check($auth->premierFacteur($citizen)==='challenge' && $auth->utilisateur()===null, 'Pas de session utilisateur avant le deuxième facteur');
refuse(fn() => $auth->terminer2fa(AmirTotp::code($setup,$now)), 'Code de préparation non rejouable');
$auth->terminer2fa($codes[0]);
check($auth->utilisateur()['id']===$citizen['id'], 'Connexion avec un code de secours');
refuse(fn() => AmirTotp::verifier($citizen,$codes[0]), 'Code de secours à usage unique');
AmirTotp::verifier($citizen,AmirTotp::code($setup,$now+1));
check(true, 'Tolérance de trente secondes');
refuse(fn() => AmirTotp::verifier($citizen,AmirTotp::code($setup,$now+1)), 'Code TOTP à usage unique');
$db->exec('DELETE FROM amir_tentatives');
for($i=0;$i<5;$i++){refuse(fn() => AmirTotp::verifier($citizen,'bad'), 'Essai invalide '.$i);}
refuse(fn() => AmirTotp::verifier($citizen,$codes[1]), 'Limitation des essais même avec un code valide');
$db->exec('DELETE FROM amir_tentatives');
AmirTotp::desactiver($citizen,$codes[1]);
check($model->getById($citizen['id'])['totp_secret']===null && (int)$db->query('SELECT COUNT(*) FROM amir_secours')->fetchColumn()===0,'Désactivation et suppression des secours');
$citizen = $model->getById($citizen['id']);
$auth->premierFacteur($citizen);
$_SESSION['auth']['verified_at']=time()-400;
refuse(fn() => AmirTotp::preparer($citizen), 'Nouvelle vérification nécessaire pour configurer la 2FA');
$auth->premierFacteur($citizen);
$db->prepare("UPDATE amir_utilisateurs SET statut='Inactif' WHERE id=?")->execute([$citizen['id']]);
check($auth->utilisateur()===null,'Compte désactivé invalide la session');
$db->prepare("UPDATE amir_utilisateurs SET statut='Actif' WHERE id=?")->execute([$citizen['id']]);

class SocialFixture extends AmirSocial
{
    public $overrides=[];
    public $subject='google-user-123';
    public $email='google@test.test';
    public $facebook=[];
    public $facebookId='facebook-123';
    public $fbEmail='facebook@test.test';
    public $badSignature=false;
    public $requests=[];
    protected function json($url,$post=null,$bearer=null)
    {
        $this->requests[] = [$url,$post,$bearer];
        if($url==='https://www.googleapis.com/oauth2/v1/certs'){return ['test-key'=>file_get_contents(__DIR__.'/public-test.pem')];}
        if($url==='https://oauth2.googleapis.com/token'){
            global $nonce;
            $header=['alg'=>'RS256','kid'=>'test-key'];
            $claims=array_replace(['iss'=>'https://accounts.google.com','aud'=>'test-google-client','sub'=>$this->subject,'email'=>$this->email,'email_verified'=>true,'name'=>'Compte Google','nonce'=>$nonce,'iat'=>time(),'exp'=>time()+3600],$this->overrides);
            $encode=fn($x)=>rtrim(strtr(base64_encode(json_encode($x)),'+/','-_'),'=');
            $body=$encode($header).'.'.$encode($claims);
            openssl_sign($body,$signature,file_get_contents(__DIR__.'/private-test.pem'),OPENSSL_ALGO_SHA256);
            if($this->badSignature){$signature[0]=chr(ord($signature[0])^1);}
            return ['id_token'=>$body.'.'.rtrim(strtr(base64_encode($signature),'+/','-_'),'=')];
        }
        if(str_contains($url,'/oauth/access_token')){return ['access_token'=>'facebook-test-token'];}
        if(str_contains($url,'/debug_token?')){return ['data'=>array_replace(['is_valid'=>true,'type'=>'USER','app_id'=>'test-facebook-app','user_id'=>$this->facebookId,'expires_at'=>time()+3600],$this->facebook)];}
        if(str_contains($url,'/me?')){return array_filter(['id'=>$this->facebookId,'name'=>'Compte Facebook','email'=>$this->fbEmail],fn($x)=>$x!==null);}
        throw new RuntimeException('Requête inattendue');
    }
}
$social = new SocialFixture($model,$auth);
function callbackFixture($social,$provider,$user=null)
{
    global $nonce;
    $social->commencer($provider,$user);
    $session=$_SESSION['oauth'];
    $nonce=$session['nonce']??'';
    return $social->retour(['provider'=>$provider,'state'=>$session['state'],'code'=>'fake-code'],$user);
}
AmirAuth::effacer();
$url=$social->commencer('google',null);
check(str_contains($url,'code_challenge_method=S256')&&str_contains($url,'nonce='),'Google : PKCE, nonce et state');
refuse(fn()=>$social->retour(['provider'=>'google','state'=>'bad','code'=>'fake'],null),'State falsifié refusé');
check(count($social->requests)===0,'Aucun échange réseau pour un mauvais state');
$social->commencer('google',null);$_SESSION['oauth']['expires']=time()-1;
refuse(fn()=>$social->retour(['provider'=>'google','state'=>$_SESSION['oauth']['state'],'code'=>'fake'],null),'State expiré refusé');
check(callbackFixture($social,'google')==='profile','Google : échange signé et création du compte');
$google=$auth->utilisateur();
check((int)$google['id_role']===1,'Un nouveau compte social est toujours citoyen');
AmirAuth::effacer();
check(callbackFixture($social,'google')==='profile' && $auth->utilisateur()['id']===$google['id'],'Google : même sujet retrouve le même compte');
AmirAuth::effacer();
foreach ([['aud'=>'autre-client'],['iss'=>'https://evil.test'],['nonce'=>'wrong'],['exp'=>time()-1],['email_verified'=>false],['iat'=>time()+3600],['azp'=>'autre-client']] as $overrides) {
    $social->overrides=$overrides;
    refuse(fn()=>callbackFixture($social,'google'),'JWT refusé : '.array_key_first($overrides));
}
$social->overrides=[];$social->badSignature=true;
refuse(fn()=>callbackFixture($social,'google'),'Signature Google falsifiée refusée');
$social->badSignature=false;$social->subject='other-google';$social->email='admin@test.test';
refuse(fn()=>callbackFixture($social,'google'),'Aucune association automatique par email admin');
$auth->premierFacteur($admin);
check(callbackFixture($social,'google',$admin)==='security','Association explicite au compte connecté');
AmirAuth::effacer();
check(callbackFixture($social,'google')==='profile' && (int)$auth->utilisateur()['id_role']===3,'Compte admin associé conserve son rôle');
AmirAuth::effacer();
check(callbackFixture($social,'facebook')==='profile','Facebook : vérification du jeton et du profil');
AmirAuth::effacer();
foreach ([['is_valid'=>false],['app_id'=>'autre-app'],['expires_at'=>time()-1],['type'=>'PAGE'],['data_access_expires_at'=>time()-1],['user_id'=>'autre-personne']] as $overrides) {
    $social->facebook=$overrides;
    refuse(fn()=>callbackFixture($social,'facebook'),'Facebook refusé : '.array_key_first($overrides));
}
$social->facebook=[];$social->facebookId='sans-email';$social->fbEmail=null;
check(callbackFixture($social,'facebook')==='profile','Facebook sans email reste utilisable');
$db->exec('DELETE FROM amir_tentatives');
$user=$auth->utilisateur();AmirTotp::preparer($user);
$secret=$_SESSION['setup_totp']['secret'];AmirTotp::activer($user,AmirTotp::code($secret,intdiv(time(),30)));
AmirAuth::effacer();
check(callbackFixture($social,'facebook')==='challenge' && $auth->utilisateur()===null,'Facebook ne contourne pas le deuxième facteur');
check(!isset($_SESSION['oauth']),'State consommé une seule fois');
file_put_contents(__DIR__.'/unit-results.json',json_encode($checks,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo count($checks)." vérifications métier et OAuth réussies (réponses externes simulées).\n";
