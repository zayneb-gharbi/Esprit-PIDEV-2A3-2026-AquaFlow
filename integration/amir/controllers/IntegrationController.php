<?php
class IntegrationController
{
    public function executer()
    {
        AmirAuth::demarrer();
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-store');
        header('Referrer-Policy: no-referrer');
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self' 'unsafe-inline'; img-src 'self' data:; frame-ancestors 'none'; base-uri 'none'; form-action 'self' https://accounts.google.com https://www.facebook.com");
        $_SESSION['csrf_token'] = $_SESSION['csrf'];
        $page = 'login';
        try {
            if (!in_array($_SERVER['REQUEST_METHOD'], ['GET','POST'], true)) { http_response_code(405); throw new DomainException('Méthode non autorisée.'); }
            foreach ($_POST as $value) { if (!is_string($value) || strlen($value)>8192) { throw new DomainException('Valeur de formulaire invalide.'); } }
            $page = champ($_GET, 'page', 40) ?: champ($_GET, 'action', 40) ?: 'home';
            $aliases = ['signin'=>'login','dashboard'=>'profile','citizens'=>'citoyens','plumbers'=>'plombiers'];
            $page = $aliases[$page] ?? $page;
            $model = new UtilisateurPersistant(AmirDatabase::connexion());
            $auth = new AmirAuth($model);
            $currentUser = $auth->utilisateur();
            $social = new AmirSocial($model,$auth);
            $passwords = new AuthController();
            $post = $_SERVER['REQUEST_METHOD']==='POST';
            if ($post) {
                $_POST['csrf'] = $_POST['csrf'] ?? $_POST['csrf_token'] ?? '';
                AmirAuth::csrf();
            }
            $public = ['home','login','signup','forgot_password','reset_password','oauth_start','oauth_callback','challenge','logout'];
            if (!$currentUser && !in_array($page,$public,true)) { rediriger(isset($_SESSION['pending']) ? 'challenge' : 'login'); }
            if ($currentUser) {
                $_SESSION['user_id']=$currentUser['id'];
                $_SESSION['id_user']=$currentUser['id'];
                $_SESSION['username']=$currentUser['nom'];
                $_SESSION['role']=integrationMode()==='groupe' ? [1=>'C',2=>'P',3=>'A'][$currentUser['id_role']] : [1=>'citoyen',2=>'plombier',3=>'admin'][$currentUser['id_role']];
                if (empty($_SESSION['integration_login'])) { integrationLog($currentUser['id'],'Connexion','Connexion réussie au compte AquaFlow'); $_SESSION['integration_login']=true; }
            } else { unset($_SESSION['user_id'],$_SESSION['id_user'],$_SESSION['role']); }
            if ($page==='home' || ($page==='login' && $currentUser)) { rediriger($currentUser ? ($currentUser['id_role']===3 ? 'admin' : 'profile') : (isset($_SESSION['pending'])?'challenge':'login')); }
            if (in_array($page,['admin','stats','citoyens','plombiers','users','details','global-history','export-users'],true)) { AmirAuth::administrateur($currentUser); }
            $csrfToken=$_SESSION['csrf'];
            $errors=[];
            $user=$currentUser ? $passwords->findById($currentUser['id']) : null;
            if ($page==='login') {
                if ($post) {
                    $_POST['email']=$_POST['email'] ?? $_POST['identifiant'] ?? '';
                    AmirAuth::limiter('login-ip-'.($_SERVER['REMOTE_ADDR'] ?? 'local'),30);
                    AmirAuth::limiter('login-email-'.strtolower(champ($_POST,'email',190)));
                    $errors=$passwords->login($_POST);
                    if (!$errors) { rediriger(isset($_SESSION['pending'])?'challenge':'home'); }
                }
                if (integrationMode()==='groupe') { $this->groupLogin($errors); return; }
                require __DIR__.'/../../yasmine/View/FrontOffice/login.php'; return;
            }
            if ($page==='logout') {
                if ($post) { integrationLog($currentUser['id'] ?? 0,'Déconnexion','Déconnexion du compte'); AmirAuth::effacer(); rediriger('login'); }
                integrationRender('Déconnexion','<form method="post"><input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'"><button type="submit" class="btn">Se déconnecter</button></form>'); return;
            }
            if ($page==='oauth_start') {
                if (!$post) { http_response_code(405); throw new DomainException('Utilise le bouton de connexion.'); }
                header('Location: '.$social->commencer(champ($_POST,'provider',20),$currentUser),true,303); exit;
            }
            if ($page==='oauth_callback') { rediriger($social->retour($_GET,$currentUser)); }
            if ($page==='challenge') {
                if ($currentUser) { rediriger('home'); }
                if (!isset($_SESSION['pending'])) { rediriger('login'); }
                if ($post) { $auth->terminer2fa(champ($_POST,'code',16)); rediriger('home'); }
                $this->view('challenge',[], 'Vérification en deux étapes');return;
            }
            if ($page==='forgot_password') {
                if ($post) {
                    AmirAuth::limiter('reset-'.($_SERVER['REMOTE_ADDR'] ?? 'local'),10);
                    $errors=$passwords->requestPasswordReset($_POST);
                    if (!$errors) { rediriger('forgot_password&sent=1'); }
                }
                require __DIR__.'/../../yasmine/View/FrontOffice/forgot_password.php';return;
            }
            if ($page==='reset_password') {
                $token=champ($_GET,'token',64) ?: champ($_POST,'token',64);
                $tokenValid=$passwords->findValidReset($token)!==null;
                if ($post && $tokenValid) {
                    $errors=$passwords->resetPassword($_POST);
                    if (!$errors) { AmirAuth::effacer(); rediriger('login&reset=1'); }
                }
                require __DIR__.'/../../yasmine/View/FrontOffice/reset_password.php';return;
            }
            if ($page==='change_password') {
                if ($post) {
                    AmirAuth::limiter('password-'.$currentUser['id'],10);
                    $errors=$passwords->changePassword($currentUser['id'],$_POST);
                    if (!$errors) { integrationLog($currentUser['id'],'Mot de passe modifié','Modification du mot de passe'); rediriger('change_password&changed=1'); }
                }
                require __DIR__.'/../../yasmine/View/FrontOffice/change_password.php';return;
            }
            if ($page==='security') {
                if ($post) {
                    $action=champ($_POST,'action',30);
                    if ($action==='prepare') { AmirTotp::preparer($currentUser); }
                    elseif ($action==='enable') { AmirTotp::activer($currentUser,champ($_POST,'code',6)); integrationLog($currentUser['id'],'Double authentification','Activation'); }
                    elseif ($action==='disable') { AmirTotp::desactiver($currentUser,champ($_POST,'code',16)); integrationLog($currentUser['id'],'Double authentification','Désactivation'); }
                    else { throw new DomainException('Action inconnue.'); }
                    rediriger('security');
                }
                $setup=$_SESSION['setup_totp'] ?? null;
                if ($setup && ($setup['expires']<time() || $setup['id']!==$currentUser['id'] || $setup['version']!==(int)$currentUser['version'])) { unset($_SESSION['setup_totp']);$setup=null; }
                $codes=$_SESSION['codes_secours'] ?? [];unset($_SESSION['codes_secours']);
                $services=$model->identites($currentUser['id']);
                $providers=['google'=>AmirSocial::disponible('google'),'facebook'=>AmirSocial::disponible('facebook')];
                $this->view('security',compact('currentUser','setup','codes','services','providers'),'Sécurité du compte');return;
            }
            if ($page==='edit') {
                if ($post) {
                    $email=champ($_POST,'email',150);
                    if (strtolower($email)!==$currentUser['email']) { AmirAuth::recente(); }
                    $model->updateProfile($currentUser['id'],champ($_POST,'nom',150),$email,champ($_POST,'telephone',40),champ($_POST,'adresse',255),champ($_POST,'prenom',150));
                    $updated=$model->getById($currentUser['id']);
                    $_SESSION['auth']['version']=(int)$updated['version'];$_SESSION['auth']['empreinte']=$updated['empreinte'];
                    unset($_SESSION['setup_totp'],$_SESSION['oauth']);
                    integrationLog($currentUser['id'],'Modification profil','Mise à jour du profil');rediriger('profile');
                }
                $this->view('edit',compact('currentUser'),'Modifier mon profil');return;
            }
            if ($page==='deactivate') {
                if (!$post) { http_response_code(405);throw new DomainException('Utilisez le formulaire du profil.'); }
                if ($currentUser['id_role']===3) { http_response_code(403);throw new DomainException('Réservé aux comptes citoyens et plombiers.'); }
                AmirAuth::recente();$model->deactivate($currentUser['id']);integrationLog($currentUser['id'],'Désactivation compte','Désactivation du compte personnel');AmirAuth::effacer();rediriger('login');
            }
            if ($page==='details') {
                if ($post) { http_response_code(405);throw new DomainException('Consultation uniquement.'); }
                $details=(new AmirDetails($model))->consulter($currentUser,$_GET['id'] ?? '');
                $this->view('details',compact('details'),'Détails utilisateur');return;
            }
            if (integrationMode()==='groupe') { $this->groupPage($page);return; }
            if (in_array($page,['users','citoyens','plombiers'],true)) {
                $users=$model->tous();
                if ($page!=='users') { $role=$page==='citoyens'?1:2;$users=array_filter($users,fn($u)=>$u['id_role']===$role); }
                $this->view('users',compact('users'),'Utilisateurs');return;
            }
            if ($page==='stats') { $stats=$model->getStats();$this->view('stats',compact('stats'),'Statistiques');return; }
            if ($page==='profile') { require __DIR__.'/../../yasmine/View/FrontOffice/user_space.php';return; }
            if ($page==='admin') { require __DIR__.'/../../yasmine/View/BackOffice/admin_space.php';return; }
            http_response_code(404);throw new DomainException('Page introuvable.');
        } catch (DomainException $e) {
            if (http_response_code()<400) { http_response_code(422); }
            integrationRender('AquaFlow','<div role="alert" class="alert alert-danger">'.h($e->getMessage()).'</div><a href="index.php?page='.h(in_array($page,['security','challenge','login','change_password'],true)?$page:'home').'">Réessayer</a>');
        } catch (Throwable $e) {
            http_response_code(503);error_log('AquaFlow intégration: '.get_class($e));
            integrationRender('AquaFlow','<div role="alert">Le service est indisponible. Vérifiez la configuration locale.</div>');
        }
    }
    private function view($name,$data,$title)
    {
        extract($data);ob_start();require __DIR__.'/../views/'.$name.'.php';$content=ob_get_clean();
        $content=preg_replace('/(<form\b[^>]*>)/i','$1<input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'">',$content);
        integrationRender($title,$content);
    }
    private function groupLogin($errors)
    {
        require_once __DIR__.'/../../groupe/app/helpers.php';
        require_once __DIR__.'/../../groupe/app/Controllers/Controller.php';
        require_once __DIR__.'/../../groupe/app/Controllers/IntegrationLoginController.php';
        (new App\Controllers\IntegrationLoginController())->afficher($errors);
    }
    private function groupPage($page)
    {
        require_once __DIR__.'/../../groupe/app/helpers.php';
        require __DIR__.'/../../groupe/app/routes.php';
        if (!isset($routes[$page]) || in_array($page,['signin','logout'],true)) { http_response_code(404);throw new DomainException('Page introuvable.'); }
        require_once __DIR__.'/../../groupe/app/Controllers/Controller.php';
        require_once __DIR__.'/../../groupe/app/Controllers/'.$routes[$page].'.php';
        $class='App\\Controllers\\'.$routes[$page];$controller=new $class();
        if ($page==='personal-history') { $controller->personnel(); }
        elseif ($page==='global-history') { $controller->global(); }
        else { $controller->index(); }
    }
}
function e($value) { return h($value); }
function integrationRender($title,$content)
{
    if (integrationMode()==='groupe') {
        $currentPage=$_GET['page'] ?? '';$withNav=!empty($_SESSION['auth']);$extraScripts='';
        require __DIR__.'/../../groupe/app/Views/layout.php';return;
    }
    $content='<div class="py-4">'.$content.'</div>';
    if (($_SESSION['role'] ?? '')==='admin') {
        $user=(new AuthController())->findById((int)$_SESSION['user_id']);
        require __DIR__.'/../../yasmine/View/BackOffice/layout_admin.php';
    } else { require __DIR__.'/../../yasmine/View/FrontOffice/layout.php'; }
}
function integrationSocialButtons()
{
    foreach (['google'=>'Google','facebook'=>'Facebook'] as $provider=>$label) {
        echo '<form method="post" action="index.php?page=oauth_start"><input type="hidden" name="csrf" value="'.h($_SESSION['csrf']).'"><input type="hidden" name="provider" value="'.$provider.'"><button class="btn btn-outline-secondary w-100 my-1" type="submit" '.(AmirSocial::disponible($provider)?'':'disabled').'>Se connecter avec '.$label.'</button></form>';
    }
}
function integrationLog($id,$action,$description)
{
    if (integrationMode()!=='groupe' || !$id) { return; }
    require_once __DIR__.'/../../groupe/app/Models/Historique.php';
    (new App\Models\Historique())->ajouter((int)$id,$action,$description);
}
