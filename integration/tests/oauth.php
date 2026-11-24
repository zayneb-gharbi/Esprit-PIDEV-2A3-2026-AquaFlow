<?php
require_once '/app/amir/config/bootstrap.php';
require_once '/app/amir/models/Installation.php';
AmirAuth::demarrer();
$db=AmirDatabase::connexion();Installation::schema($db);$model=new UtilisateurPersistant($db);$auth=new AmirAuth($model);$checks=0;
function check($value,$label) { global $checks;if (!$value) { throw new RuntimeException($label); }$checks++; }
class SocialTest extends AmirSocial
{
    public $subject='test-social';
    public $email='social-callback@test.test';
    public $nonce='';
    public $badApp=false;
    public function begin($provider,$user=null) { $this->commencer($provider,$user);$this->nonce=$_SESSION['oauth']['nonce'] ?? '';return ['provider'=>$provider,'state'=>$_SESSION['oauth']['state'],'code'=>'code-de-test']; }
    protected function json($url,$post=null,$bearer=null)
    {
        if (str_contains($url,'googleapis.com/token')) {
            $b64=function($v){return rtrim(strtr(base64_encode($v),'+/','-_'),'=');};
            $claims=['iss'=>'https://accounts.google.com','aud'=>'test-client','exp'=>time()+600,'iat'=>time(),'nonce'=>$this->nonce,'email_verified'=>true,'email'=>$this->email,'name'=>'Compte social','sub'=>$this->subject];
            $data=$b64(json_encode(['alg'=>'RS256','kid'=>'test'])).'.'.$b64(json_encode($claims));
            openssl_sign($data,$signature,file_get_contents('/app/tests/private.pem'),OPENSSL_ALGO_SHA256);
            return ['id_token'=>$data.'.'.$b64($signature)];
        }
        if (str_contains($url,'/certs')) { return ['test'=>file_get_contents('/app/tests/public.pem')]; }
        if (str_contains($url,'/oauth/access_token')) { check($post===null,'Échange Facebook GET conservé');return ['access_token'=>'jeton-de-test']; }
        if (str_contains($url,'/debug_token')) { return ['data'=>['is_valid'=>true,'app_id'=>$this->badApp?'autre-app':'test-facebook','user_id'=>$this->subject,'type'=>'USER','expires_at'=>time()+600]]; }
        if (str_contains($url,'/me?')) { return ['id'=>$this->subject,'name'=>'Compte Facebook']+($this->email?['email'=>$this->email]:[]); }
        throw new RuntimeException('Requête inattendue.');
    }
}
$social=new SocialTest($model,$auth);
foreach (['google','facebook'] as $provider) {
    AmirAuth::effacer();$social->subject='subject-'.$provider;$social->email=$provider==='google'?'callback-google@test.test':null;
    $request=$social->begin($provider);$result=$social->retour($request,null);$user=$auth->utilisateur();
    check($result==='profile' && $user && $user['id_role']===1,$provider.' crée un citoyen');
    check((new AuthController())->findById($user['id'])->getId()===$user['id'],$provider.' partage le compte avec Yasmine');
    $before=count($model->tous());AmirAuth::effacer();$social->retour($social->begin($provider),null);
    check(count($model->tous())===$before,$provider.' réutilise son identité stable');
    $table=$model->table();$idColumn=$model->idColumn();
    $secret=AmirTotp::chiffrer(AmirTotp::base32(random_bytes(20)));
    $db->prepare("UPDATE $table SET totp_secret=?,version=version+1 WHERE $idColumn=?")->execute([$secret,$user['id']]);
    AmirAuth::effacer();$result=$social->retour($social->begin($provider),null);
    check($result==='challenge' && empty($_SESSION['auth']) && empty($_SESSION['user_id']),$provider.' respecte le deuxième facteur');
    $db->prepare("UPDATE $table SET ".$model->statusColumn()."='bloque' WHERE $idColumn=?")->execute([$user['id']]);
    AmirAuth::effacer();$denied=false;try {$social->retour($social->begin($provider),null);}catch(DomainException $e){$denied=true;}
    check($denied,$provider.' respecte le blocage');
}
$admin=$model->create('Administrateur','collision@test.test','PasswordTest42!',3);
AmirAuth::effacer();$social->subject='nouvelle-identite';$social->email='collision@test.test';$denied=false;
try {$social->retour($social->begin('google'),null);}catch(DomainException $e){$denied=true;}
check($denied && $model->idSocial('google','nouvelle-identite')===false,'Pas de fusion automatique sur email');
$auth->premierFacteur($admin);$result=$social->retour($social->begin('google',$admin),$admin);
check($result==='security' && (int)$model->idSocial('google','nouvelle-identite')===$admin['id'],'Association volontaire du compte existant');
AmirAuth::effacer();$social->badApp=true;$denied=false;
try {$social->retour($social->begin('facebook'),null);}catch(DomainException $e){$denied=true;}
check($denied,'Jeton Facebook destiné à une autre application refusé');
echo $checks;
