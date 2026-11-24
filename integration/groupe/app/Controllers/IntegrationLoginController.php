<?php
namespace App\Controllers;
class IntegrationLoginController extends Controller
{
    protected string $page='signin';
    protected string $title='Connexion — AquaFlow';
    protected bool $withNav=false;
    public function afficher($errors)
    {
        $flash=takeFlash();
        $this->render('signin',['activeView'=>'login','message'=>implode(' ',$errors) ?: ($flash['text'] ?? ''),
            'messageType'=>$errors?'error':($flash['type'] ?? 'success'),'oldIdentifier'=>\champ($_POST,'email',190)],'signin_scripts');
    }
}
